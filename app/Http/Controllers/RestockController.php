<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierOffer;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class RestockController extends Controller
{
    // 1. Nampilin Halaman (Tabel Atas: Alarm, Tabel Bawah: Keranjang)
    public function index(Request $request)
    {
        // 🔥 1. CEK MEJA MANAGER (Pending / Approved) 🔥
        $barangLagiDipesan = SupplierOffer::whereIn('status', ['pending', 'approved'])
                                          ->pluck('product_name')
                                          ->filter()
                                          ->toArray();

        $search = $request->input('search');
        
        // 🔥 2. RADAR KRITIS (Dengan Pertahanan Anti-Bug Laravel) 🔥
        $query = Product::query();

        // CUMA jalanin pengecualian KALAU ADA barang di meja Manager
        if (!empty($barangLagiDipesan)) {
            $query->whereNotIn('product_name', $barangLagiDipesan);
        }

        // Cari yang stoknya murni di bawah 20 (Dikonversi ke Angka biar akurat)
        $query->whereRaw('CAST(stock AS UNSIGNED) < ?', [20]);

        // Fitur Pencarian
        if (!empty($search)) {
            $query->where('product_name', 'like', '%' . trim($search) . '%');
        }

        $products = $query->orderByRaw('CAST(stock AS UNSIGNED) ASC')
                          ->paginate(10, ['*'], 'katalog_page')
                          ->appends(['search' => $search]);

        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        $restockPlans = collect(); 

        return view('restock.index', compact('products', 'suppliers', 'restockPlans', 'search'));
    }

    // 2. Proses masukin ke Keranjang (Lempar ke Purchase Plan)
    public function processReorder(Request $request)
    {
        $request->validate([
            'product_name'  => 'required|string',
            'supplier_name' => 'required|string',
            'qty'           => 'required|numeric|min:1',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|string',
        ]);

        SupplierOffer::create([
            'supplier_name' => $request->supplier_name,
            'product_name'  => $request->product_name,
            'price'         => $request->price,
            'qty'           => $request->qty,
            'unit'          => $request->unit,
            // 🔥 Langsung lempar statusnya ke 'pending' biar terbang ke meja Manager di Purchase Plan!
            'status'        => 'pending', 
            'offer_date'    => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'RESTOCK_SUBMIT',
            'module' => 'Restock Gudang',
            'description' => 'Mengajukan pembelian ' . $request->product_name . ' (' . $request->qty . ' ' . $request->unit . ') ke Manager (Purchase Plan).',
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Mantap Breyy! Pengajuan berhasil dilempar ke meja Manager (Purchase Plan).');
    }

    // 3. Hapus barang dari Keranjang Draft (Kalau pakai Draft)
    public function destroyPlan($id)
    {
        $plan = SupplierOffer::findOrFail($id);
        $nama_barang = $plan->product_name;
        $plan->delete();

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'RESTOCK_REMOVE_CART',
            'module' => 'Restock Gudang',
            'description' => 'Membatalkan rencana beli ' . $nama_barang,
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', "Data $nama_barang berhasil dihapus dari Keranjang.");
    }

    // 4. Fitur Cetak PDF (Dinonaktifkan di sini karena udah pindah ke Purchase Plan)
    public function printPDF()
    {
        return redirect()->back()->with('error', 'Fitur Cetak PDF sekarang terpusat di halaman Purchase Plan Bos!');
    }

    // 5. Fitur Edit Keranjang Draft
    public function updatePlan(Request $request, $id)
    {
        $request->validate([
            'supplier_name' => 'required|string',
            'qty'           => 'required|numeric|min:1',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|string', 
        ]);

        $plan = SupplierOffer::findOrFail($id);
        $plan->update([
            'supplier_name' => $request->supplier_name,
            'qty'           => $request->qty,
            'price'         => $request->price,
            'unit'          => $request->unit,    
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'RESTOCK_EDIT_CART',
            'module' => 'Restock Gudang',
            'description' => 'Mengubah rencana beli ' . $plan->product_name . ' menjadi ' . $request->qty . ' ' . $request->unit . '.',
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', "Rencana beli {$plan->product_name} berhasil diperbarui Bos!");
    }

    // 6. Fitur Auto Restock (Radar Otomatis Lempar ke Purchase Plan)
    public function autoRestock()
    {
        $products = Product::all();
        $addedCount = 0;

        foreach ($products as $prod) {
            $stok = $prod->stock ?? 0;
            
            if ($stok < 20) {
                $supplierName = 'Belum Ada Supplier';
                if (!empty($prod->supplier_id)) {
                    $sup = Supplier::find($prod->supplier_id);
                    if ($sup) $supplierName = $sup->nama_supplier;
                } else {
                    $firstSup = Supplier::first();
                    if ($firstSup) $supplierName = $firstSup->nama_supplier;
                }

                $exists = SupplierOffer::where('product_name', $prod->product_name)
                                       ->where('status', 'pending') // Udah masuk antrean Purchase Plan?
                                       ->exists();

                if (!$exists) {
                    SupplierOffer::create([
                        'supplier_name' => $supplierName,
                        'product_name'  => $prod->product_name,
                        'price'         => 0, 
                        'qty'           => 20, 
                        'unit'          => $prod->unit ?? 'Pcs',
                        'status'        => 'pending', // Terbang langsung ke Purchase Plan!
                        'offer_date'    => now(),
                    ]);
                    $addedCount++;
                }
            }
        }

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'RESTOCK_AUTO_BULK',
            'module' => 'Restock Gudang',
            'description' => "Menjalankan Auto-Restock. $addedCount barang diajukan ke Manager.",
            'ip_address' => request()->ip()
        ]);

        if ($addedCount > 0) {
            return redirect()->back()->with('success', "Sihir berhasil Breyy! $addedCount produk kritis otomatis dilempar ke meja Manager (Purchase Plan).");
        }

        return redirect()->back()->with('error', 'Semua produk kritis sudah ada di antrean Purchase Plan, atau stok di gudang masih aman semua Breyy!');
    }
}