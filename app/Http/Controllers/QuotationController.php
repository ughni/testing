<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierOffer;
use App\Models\AuditLog;
use App\Models\Product; // 🔥 TAMBAHAN: Biar sistem kenal sama Master Produk
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    // Nampilin Form Halaman Baru
    public function create()
    {
        // Narik data KTP dari Ruangan 1
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        return view('quotations.create', compact('suppliers'));
    }

    // Nangkap Data Brosur dari Sales (Multi-Baris)
    public function store(Request $request)
    {
        // Validasi buat array 'unit' (Satuan)
        $validated = $request->validate([
            'supplier_name'   => 'required|string|max:255',
            'product_name'    => 'required|array|min:1',
            'product_name.*'  => 'required|string|max:255',
            'price'           => 'required|array|min:1',
            'price.*'         => 'required|numeric|min:0',
            'qty'             => 'nullable|array',
            'qty.*'           => 'nullable|numeric|min:1',
            'unit'            => 'required|array',         
            'unit.*'          => 'required|string',        
        ]);

        $count = 0;
        
        // 🔥 STEP 1: Cari ID Supplier yang lagi dipilih (Biar bisa disambungin ke KTP Produk)
        $supplier = Supplier::where('nama_supplier', $validated['supplier_name'])->first();
        $supplierId = $supplier ? $supplier->id : null;
        
        // Looping sebanyak baris produk yang diinput
        foreach ($request->product_name as $index => $prodName) {
            if (!empty($prodName) && !empty($request->price[$index])) {
                
                // 1. Simpan ke Antrean Purchase Plan (Buku Catatan Belanja)
                SupplierOffer::create([
                    'supplier_name' => $validated['supplier_name'],
                    'product_name'  => $prodName,
                    'price'         => $request->price[$index],
                    'qty'           => $request->qty[$index] ?? 1,
                    'unit'          => $request->unit[$index] ?? 'Pcs',
                    'status'        => 'pending', 
                    'offer_date'    => now(),
                ]);
                $count++;

                // 🔥 STEP 2: LOGIKA AUTO-LINK SILUMAN (SANGAT AMAN) 🔥
                if ($supplierId) {
                    // Cek apakah produk ini ada di Master Data?
                    $masterProduct = Product::where('product_name', $prodName)->first();

                    // KALAU ada, DAN supplier-nya belum diisi sama Admin...
                    if ($masterProduct && is_null($masterProduct->supplier_id)) {
                        // ...Isi otomatis tanpa ngeluarin error!
                        $masterProduct->update([
                            'supplier_id' => $supplierId
                        ]);
                    }
                }
                
            }
        }

        // 🎥 CCTV
        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'CREATE',
            'module' => 'Input Penawaran',
            'description' => 'Menerima ' . $count . ' penawaran produk baru dari ' . $validated['supplier_name'] . '.',
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', "Mantap Breyy! $count Penawaran baru berhasil masuk antrean Bos (Status: Pending).");
    }
}