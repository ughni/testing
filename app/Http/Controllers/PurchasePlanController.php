<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierOffer; 
use App\Models\Product;
use App\Models\AuditLog; 
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ProcessPlanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PurchasePlanController extends Controller
{
    public function index(Request $request)
    {
        $approvedOffers = SupplierOffer::where('status', 'approved')
                                  ->orderBy('updated_at', 'desc')
                                  ->paginate(10, ['*'], 'approved_page');

        $search = $request->input('search');
        $query = SupplierOffer::where('status', 'pending');

        if ($request->has('order_type') && $request->order_type !== 'all') {
            $criticalProductNames = Product::whereHas('dailyPricings', function($q) {
                $q->where('stock', '<', 20);
            })->pluck('product_name');

            $safeProductNames = Product::whereHas('dailyPricings', function($q) {
                $q->where('stock', '>=', 20);
            })->pluck('product_name');

            if ($request->order_type === 'otomatis') {
                $query->whereIn('product_name', $criticalProductNames);
            } elseif ($request->order_type === 'manual') {
                $query->whereIn('product_name', $safeProductNames);
            }
        }

        $pendingOffers = $query->orderBy('created_at', 'desc')
                               ->paginate(10, ['*'], 'pending_page');

        return view('process_plan.index', compact('approvedOffers', 'pendingOffers', 'search'));
    }

    public function updateOffer(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'qty' => 'required|numeric',
        ]);

        $offer = SupplierOffer::findOrFail($id);
        $offer->update([
            'price' => $request->price,
            'qty' => $request->qty,
        ]);

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => "Mengedit Harga/Qty penawaran {$offer->product_name} dari supplier {$offer->supplier_name}.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'Data Penawaran berhasil diperbarui!');
    }

    public function autoSelectCheapest()
    {
        $pendingProducts = SupplierOffer::where('status', 'pending')
                            ->select('product_name')
                            ->distinct()
                            ->pluck('product_name');

        $count = 0;

        foreach ($pendingProducts as $productName) {
            // 1. Ambil SEMUA penawaran untuk produk ini, jejerin dari yang PALING MURAH
            $offers = SupplierOffer::where('product_name', $productName)
                                ->where('status', 'pending')
                                ->orderBy('price', 'asc') 
                                ->get();

            $cheapestValidOffer = null;

            // 2. INTEROGASI SATPAM KONTRAK (RADAR HELIKOPTER ANTI BUG PHP)
            foreach ($offers as $offer) {
                $namaSupplierBersih = trim($offer->supplier_name);
                $supplier = \App\Models\Supplier::where('nama_supplier', 'LIKE', '%' . $namaSupplierBersih . '%')->first();
                $isLegal = false; // Anggap ilegal dulu

                if ($supplier) {
                    $latestContract = \App\Models\SupplierContract::where('supplier_id', $supplier->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();

                    if ($latestContract && !empty($latestContract->valid_until)) {
                        $tanggalHabis = \Carbon\Carbon::parse($latestContract->valid_until)->endOfDay();
                        if ($tanggalHabis->isFuture() || $tanggalHabis->isToday()) {
                            $isLegal = true; // TERBUKTI AMAN!
                        }
                    }
                }

                // Kalau dia LEGAL (Aman), langsung pilih dia dan BERHENTI nyari!
                if ($isLegal) {
                    $cheapestValidOffer = $offer;
                    break; 
                }
            }

            // 3. Eksekusi kalau dapet yang Legal & Termurah!
            if ($cheapestValidOffer) {
                // 🔥 AMBIL QTY ASLI DARI PENAWARAN (Bukan 10 lagi!) 🔥
                $qtyAsli = $cheapestValidOffer->qty ?? 1;

                // Set ACC
                $cheapestValidOffer->update([
                    'status' => 'approved'
                ]);
                $count++;

                // LOGIKA AUTO-LINK & TAMBAH STOK OTOMATIS!
                $product = Product::where('product_name', $cheapestValidOffer->product_name)->first();
                if ($product) {
                    $supplier = \App\Models\Supplier::where('nama_supplier', trim($cheapestValidOffer->supplier_name))->first();
                    if ($supplier) {
                        $product->supplier_id = $supplier->id;
                    }
                    // 🔥 Tambah Brankas Stok SESUAI QTY ASLI! 🔥
                    $product->increment('stock', $qtyAsli);
                }
                
                // Buang sisa penawaran yang kalah saing / ilegal
                SupplierOffer::where('product_name', $productName)
                        ->where('status', 'pending')
                        ->where('id', '!=', $cheapestValidOffer->id)
                        ->update(['status' => 'rejected']);
            }
        }

        if ($count == 0) {
            return redirect()->back()->with('error', 'Tidak ada data penawaran yang memenuhi syarat (Legal & Termurah).');
        }

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => "Menjalankan sistem Auto Termurah. $count produk LEGAL berhasil divalidasi otomatis.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', " $count Produk berhasil dipilih OTOMATIS dari supplier LEGAL TERMURAH!");
    }
    public function approve($id)
    {
        $offer = SupplierOffer::findOrFail($id);
        $offer->update(['status' => 'approved']);

        SupplierOffer::where('product_name', $offer->product_name)
                ->where('status', 'pending')
                ->where('id', '!=', $offer->id)
                ->update(['status' => 'rejected']);

        // 🔥 TAHAP 2 (B): LOGIKA AUTO-LINK & TAMBAH STOK MANUAL! 🔥
        $product = Product::where('product_name', $offer->product_name)->first();
        if ($product) {
            $supplier = \App\Models\Supplier::where('nama_supplier', $offer->supplier_name)->first();
            if ($supplier) {
                $product->supplier_id = $supplier->id;
            }
            // Tambah Brankas Stok sesuai jumlah Qty yang ada di penawaran!
            $product->increment('stock', (int) $offer->qty);
        }

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => 'Memilih manual (ACC) supplier "' . $offer->supplier_name . '" untuk produk "' . $offer->product_name . '".',
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'Supplier ' . $offer->supplier_name . ' berhasil dipilih dan stok otomatis bertambah!');
    }

    public function holdForDuration(Request $request, $id, $duration)
    {
        $offer = SupplierOffer::findOrFail($id);
        
        $holdDays = 1; 
        if ($duration === '1_week') {
            $holdDays = 7; 
        }

        $offer->update([
            'status' => 'hold',
            'hold_until' => now()->addDays($holdDays)->format('Y-m-d')
        ]);

        $msg = "Penawaran {$offer->supplier_name} untuk {$offer->product_name} berhasil DITUNDA.";
        if ($duration === '1_week') {
            $msg .= " Silakan cek kembali tanggal " . now()->addDays(7)->format('d M Y') . ".";
        }

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => 'Menunda validasi supplier "' . $offer->supplier_name . '" selama ' . $holdDays . ' hari.',
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', $msg);
    }

    public function reject($id)
    {
        $offer = SupplierOffer::findOrFail($id);
        $offer->update(['status' => 'rejected']);
        
        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'DELETE',
            'module' => 'Process Plan',
            'description' => 'Menolak/Menghapus penawaran dari supplier "' . $offer->supplier_name . '" untuk produk "' . $offer->product_name . '".',
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'Penawaran berhasil dihapus dari layar (Masuk Riwayat).');
    }

    public function cancel($id)
    {
        $offer = SupplierOffer::findOrFail($id);

        // 🔥 TAHAP 2 (BONUS): TARIK BALIK STOK KALAU MANAJER BATALIN ACC! 🔥
        if ($offer->status === 'approved') {
            $product = Product::where('product_name', $offer->product_name)->first();
            if ($product) {
                // Kurangi stok sebesar Qty yang sebelumnya udah di-ACC biar gak bocor
                $product->decrement('stock', (int) $offer->qty);
            }
        }

        $offer->update(['status' => 'pending']);

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => 'Membatalkan ACC untuk supplier "' . $offer->supplier_name . '". Data kembali ke antrean.',
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'Status supplier dibatalkan. Stok gudang berhasil ditarik kembali!');
    }

    public function printPDF()
    {
        $approvedOffers = SupplierOffer::where('status', 'approved')->orderBy('supplier_name', 'asc')->get();
        
        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'CREATE',
            'module' => 'Process Plan',
            'description' => 'Mencetak dokumen Surat PO (PDF).',
            'ip_address' => request()->ip()
        ]);

        $pdf = Pdf::loadView('purchase_plan.pdf', compact('approvedOffers'));
        return $pdf->stream('Surat_PO_' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'CREATE',
            'module' => 'Process Plan',
            'description' => 'Melakukan Export Excel Laporan Validasi Harga.',
            'ip_address' => request()->ip()
        ]);

        return Excel::download(new ProcessPlanExport, 'Laporan_Validasi_Harga_' . date('Y-m-d') . '.xlsx');
    }

  // 🔥 FITUR ARSIPKAN 1 BARANG & NAMBAH STOK 🔥
    public function archive($id)
    {
        $offer = SupplierOffer::findOrFail($id);
        
        // 1. Ubah status jadi 'completed' (Arsip)
        $offer->update(['status' => 'completed']);

        // 2. OTOMATIS TAMBAH STOK KE MASTER PRODUK!
        $product = Product::where('product_name', $offer->product_name)->first();
        if($product) {
            $product->increment('stock', $offer->qty);
        }

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => 'Mengarsipkan PO produk "' . $offer->product_name . '" dan MENAMBAH STOK ' . $offer->qty . ' ke Gudang.',
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'PO diarsipkan & Stok Gudang berhasil bertambah!');
    }

    // 🔥 FITUR ARSIPKAN SEMUA SEKALIGUS (SAPU BERSIH) & NAMBAH STOK 🔥
    public function archiveAll()
    {
        $offers = SupplierOffer::where('status', 'approved')->get();
        $count = $offers->count();

        foreach($offers as $offer) {
            // 1. Arsipkan
            $offer->update(['status' => 'completed']);
            
            // 2. OTOMATIS TAMBAH STOK KE MASTER PRODUK!
            $product = Product::where('product_name', $offer->product_name)->first();
            if($product) {
                $product->increment('stock', $offer->qty);
            }
        }

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => "Mengarsipkan SEMUA PO ($count barang) dan menambahkan semua stoknya ke Gudang.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', "Mantap! $count Barang diarsipkan & Stoknya otomatis masuk Gudang!");
    }
}