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
            $offers = SupplierOffer::where('product_name', $productName)
                                ->where('status', 'pending')
                                ->orderBy('price', 'asc') 
                                ->get();

            $cheapestValidOffer = null;

            foreach ($offers as $offer) {
                $namaSupplierBersih = trim($offer->supplier_name);
                $supplier = \App\Models\Supplier::where('nama_supplier', 'LIKE', '%' . $namaSupplierBersih . '%')->first();
                $isLegal = false; 

                if ($supplier) {
                    $latestContract = \App\Models\SupplierContract::where('supplier_id', $supplier->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();

                    if ($latestContract && !empty($latestContract->valid_until)) {
                        $tanggalHabis = Carbon::parse($latestContract->valid_until)->endOfDay();
                        if ($tanggalHabis->isFuture() || $tanggalHabis->isToday()) {
                            $isLegal = true; 
                        }
                    }
                }

                if ($isLegal) {
                    $cheapestValidOffer = $offer;
                    break; 
                }
            }

            if ($cheapestValidOffer) {
                $qtyAsli = $cheapestValidOffer->qty ?? 1;

                $cheapestValidOffer->update([
                    'status' => 'approved'
                ]);
                $count++;

                // 🔥 LOGIKA AUTO-CREATE & SINKRONISASI MASTER PRODUK 🔥
                $supplier = \App\Models\Supplier::where('nama_supplier', trim($cheapestValidOffer->supplier_name))->first();
                $supplier_id = $supplier ? $supplier->id : null;
                $product = Product::where('product_name', $cheapestValidOffer->product_name)->first();

                if ($product) {
                    if ($supplier_id) $product->supplier_id = $supplier_id;
                    $product->stock = $product->stock + (int) $qtyAsli;
                    $product->save();
                } else {
                    $newProd = new Product();
                    $newProd->product_name = $cheapestValidOffer->product_name;
                    $newProd->supplier_id  = $supplier_id;
                    $newProd->stock        = (int) $qtyAsli;
                    $newProd->unit         = $cheapestValidOffer->unit ?? 'Pcs';
                    $newProd->price_type   = 'dynamic';
                    $newProd->category     = 'Produk Beli';
                    $newProd->is_active    = true;
                    $newProd->save();
                }
                
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

        // 🔥 LOGIKA AUTO-CREATE & SINKRONISASI MASTER PRODUK 🔥
        $supplier = \App\Models\Supplier::where('nama_supplier', trim($offer->supplier_name))->first();
        $supplier_id = $supplier ? $supplier->id : null;
        $product = Product::where('product_name', $offer->product_name)->first();

        if ($product) {
            if ($supplier_id) $product->supplier_id = $supplier_id;
            $product->stock = $product->stock + (int) $offer->qty;
            $product->save();
        } else {
            $newProd = new Product();
            $newProd->product_name = $offer->product_name;
            $newProd->supplier_id  = $supplier_id;
            $newProd->stock        = (int) $offer->qty;
            $newProd->unit         = $offer->unit ?? 'Pcs';
            $newProd->price_type   = 'dynamic';
            $newProd->category     = 'Produk Beli';
            $newProd->is_active    = true;
            $newProd->save();
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

        if ($offer->status === 'approved') {
            $product = Product::where('product_name', $offer->product_name)->first();
            if ($product) {
                // Tarik balik stok kalau dibatalkan
                $product->stock = $product->stock - (int) $offer->qty;
                $product->save();
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

    // 🔥 FITUR ARSIPKAN 1 BARANG & AUTO CREATE MASTER PRODUK 🔥
    public function archive($id)
    {
        $offer = SupplierOffer::findOrFail($id);
        $offer->update(['status' => 'completed']);

        // 🔥 LOGIKA AUTO-CREATE & SINKRONISASI MASTER PRODUK 🔥
        $supplier = \App\Models\Supplier::where('nama_supplier', trim($offer->supplier_name))->first();
        $supplier_id = $supplier ? $supplier->id : null;
        $product = Product::where('product_name', $offer->product_name)->first();

        if ($product) {
            if ($supplier_id) $product->supplier_id = $supplier_id;
            $product->stock = $product->stock + (int) $offer->qty;
            $product->save();
        } else {
            $newProd = new Product();
            $newProd->product_name = $offer->product_name;
            $newProd->supplier_id  = $supplier_id;
            $newProd->stock        = (int) $offer->qty;
            $newProd->unit         = $offer->unit ?? 'Pcs';
            $newProd->price_type   = 'dynamic';
            $newProd->category     = 'Produk Beli';
            $newProd->is_active    = true;
            $newProd->save();
        }

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => 'Mengarsipkan PO produk "' . $offer->product_name . '" dan MENAMBAH STOK ' . $offer->qty . ' ke Gudang.',
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'PO diarsipkan & Produk Otomatis Masuk Master/Gudang!');
    }

    // 🔥 FITUR ARSIPKAN SEMUA SEKALIGUS (SAPU BERSIH) & AUTO CREATE MASTER PRODUK 🔥
    public function archiveAll()
    {
        $offers = SupplierOffer::where('status', 'approved')->get();
        $count = $offers->count();

        foreach($offers as $offer) {
            $offer->update(['status' => 'completed']);
            
            // 🔥 LOGIKA AUTO-CREATE & SINKRONISASI MASTER PRODUK 🔥
            $supplier = \App\Models\Supplier::where('nama_supplier', trim($offer->supplier_name))->first();
            $supplier_id = $supplier ? $supplier->id : null;
            $product = Product::where('product_name', $offer->product_name)->first();

            if ($product) {
                if ($supplier_id) $product->supplier_id = $supplier_id;
                $product->stock = $product->stock + (int) $offer->qty;
                $product->save();
            } else {
                $newProd = new Product();
                $newProd->product_name = $offer->product_name;
                $newProd->supplier_id  = $supplier_id;
                $newProd->stock        = (int) $offer->qty;
                $newProd->unit         = $offer->unit ?? 'Pcs';
                $newProd->price_type   = 'dynamic';
                $newProd->category     = 'Produk Beli';
                $newProd->is_active    = true;
                $newProd->save();
            }
        }

        AuditLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Process Plan',
            'description' => "Mengarsipkan SEMUA PO ($count barang) dan menambahkan semua stoknya ke Gudang.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', "Mantap! $count Barang diarsipkan & Otomatis masuk Master Produk/Gudang!");
    }
}