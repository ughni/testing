<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier; 
use App\Models\SupplierOffer;
use Illuminate\Http\Request;
use App\Imports\DataImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AuditLog; 
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationSystem; 

class ProductController extends Controller
{
    /**
     * Menampilkan Halaman Daftar & Form Tambah Produk
     */
    public function index(Request $request)
    {
      $query = Product::with('dailyPricings');

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

        $approvedProducts = Product::select('product_name')->distinct()->get();

        return view('products.index', compact('products', 'approvedProducts'));
    }

    /**
     * Menyimpan Produk Baru ke Database (Create)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'price_type'   => 'required|in:dynamic,consignment,HET',
            'category'     => 'required|string|max:255', 
            'unit'         => 'required|string|max:50',  
            'het_price'          => 'nullable|required_if:price_type,HET|numeric|min:0',
            'consignment_margin' => 'nullable|required_if:price_type,consignment|numeric|min:0|max:1',
            'supplier_id'  => 'nullable|exists:suppliers,id', 
            
            // Tangkap form
            'markup'          => 'nullable|numeric|min:0|max:100',
            'buffer'          => 'nullable|numeric|min:0|max:100',
            'threshold'       => 'nullable|integer|min:0',
            'yield_percent'   => 'nullable|numeric|min:1|max:100',
        ], [
            'het_price.required_if'          => 'Batas Harga HET wajib diisi kalau pilih tipe HET, Breyy!',
            'consignment_margin.required_if' => 'Margin Konsinyasi wajib diisi kalau pilih Consignment!',
        ]);

        if (Product::where('product_name', $request->product_name)->exists()) {
            return redirect()->back()->with('error', 'Gagal! Produk ini sudah ada di Master Data.')->withInput();
        }

        $validated['description'] = 'Ditambahkan manual dari Master Data';
        $validated['is_active']   = true;

        if ($validated['price_type'] !== 'HET') {
            $validated['het_price'] = null;
        }
        if ($validated['price_type'] !== 'consignment') {
            $validated['consignment_margin'] = null;
        }

        // 🔥 LOGIKA SINKRONISASI DATABASE (BAGI 100 JIKA ADA ISINYA) 🔥
        // Jika kosong, jadikan NULL agar mesin otomatis mengambil Global Setting
// 🔥 KEMBALIKAN JADI NULL BIAR BISA NGAMBIL DARI GLOBAL SETTING 🔥
        $validated['markup']        = $request->filled('markup') ? ((float) $request->markup / 100) : null;
        $validated['buffer']        = $request->filled('buffer') ? ((float) $request->buffer / 100) : null;
        $validated['yield_percent'] = $request->filled('yield_percent') ? ((float) $request->yield_percent / 100) : null;
        $validated['threshold']     = $request->filled('threshold') ? $request->threshold : null;

        Product::create($validated);

        // --- 🎥 CCTV TAMBAH PRODUK ---
        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'CREATE',
            'module' => 'Master Produk',
            'description' => 'Menambahkan produk baru ke Master Data: "' . $request->product_name . '" (Kategori: ' . $request->category . ').',
            'ip_address' => $request->ip()
        ]);

        NotificationSystem::create([
            'type' => 'info',
            'title' => 'Master Produk Baru',
            'message' => 'Produk baru <b>' . $request->product_name . '</b> (Kategori: ' . $request->category . ') telah berhasil ditambahkan ke dalam sistem oleh Admin.',
            'icon' => 'fas fa-cube'
        ]);

        return redirect()->back()->with('success', 'Mantap! Produk lengkap berhasil ditambahkan!');
    }

    /**
     * Menampilkan Halaman Edit Produk
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();
        $approvedProducts = SupplierOffer::where('status', 'approved')->select('product_name')->distinct()->get();
        
        return view('products.edit', compact('product', 'suppliers', 'approvedProducts'));
    }

    /**
     * Menyimpan Perubahan Data Produk (Update)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'price_type'   => 'required|in:dynamic,consignment,HET',
            'category'     => 'required|string|max:255', 
            'unit'         => 'required|string|max:50',
            'het_price'          => 'nullable|required_if:price_type,HET|numeric|min:0',
            'consignment_margin' => 'nullable|required_if:price_type,consignment|numeric|min:0|max:1',
            'supplier_id'  => 'nullable|exists:suppliers,id', 
            
            // Tangkap form
            'markup'          => 'nullable|numeric|min:0|max:100',
            'buffer'          => 'nullable|numeric|min:0|max:100',
            'threshold'       => 'nullable|integer|min:0',
            'yield_percent'   => 'nullable|numeric|min:1|max:100',
        ]);

        if ($validated['price_type'] !== 'HET') {
            $validated['het_price'] = null;
        }
        if ($validated['price_type'] !== 'consignment') {
            $validated['consignment_margin'] = null;
        }

        // 🔥 LOGIKA SINKRONISASI DATABASE (BAGI 100 JIKA ADA ISINYA) 🔥
        // Jika form dikosongkan saat di-edit, simpan NULL ke database (agar kembali ke Global)
        $validated['markup']        = $request->filled('markup') ? ((float) $request->markup / 100) : null;
        $validated['buffer']        = $request->filled('buffer') ? ((float) $request->buffer / 100) : null;
        $validated['yield_percent'] = $request->filled('yield_percent') ? ((float) $request->yield_percent / 100) : null;
        $validated['threshold']     = $request->filled('threshold') ? $request->threshold : null;

        $product = Product::findOrFail($id);
        $oldProductName = $product->product_name; 
        
        $product->update($validated);

        // --- 🎥 CCTV EDIT PRODUK ---
        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Master Produk',
            'description' => 'Memperbarui data produk: "' . $oldProductName . '" menjadi "' . $request->product_name . '".',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('products.index')->with('success', 'Mantap! Data produk dan formula khususnya berhasil diperbarui.');
    }

    /**
     * Menghapus Data Produk dari Database (Delete)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->product_name; 

            // --- 🎥 CCTV HAPUS PRODUK ---
            AuditLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'DELETE',
                'module' => 'Master Produk',
                'description' => 'Menghapus permanen produk "' . $productName . '" beserta histori harganya dari sistem.',
                'ip_address' => $request->ip()
            ]);

            $product->delete();

            return redirect()->route('products.index')->with('success', 'Produk beserta histori harganya berhasil dihapus permanen!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Memproses File Excel yang Diupload
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file_excel.required' => 'File Excel wajib diisi!',
            'file_excel.mimes'    => 'Format file wajib .xlsx atau .csv!',
            'file_excel.max'      => 'Ukuran file maksimal 2MB!'
        ]);

        try {
            Excel::import(new DataImport, $request->file('file_excel'));

            // --- 🎥 CCTV IMPORT EXCEL ---
            AuditLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'CREATE',
                'module' => 'Master Produk',
                'description' => 'Melakukan Import Data Massal (Bulk Insert) Master Produk via file Excel.',
                'ip_address' => $request->ip()
            ]);

            NotificationSystem::create([
                'type' => 'success',
                'title' => 'Import Massal Berhasil',
                'message' => 'Sistem telah berhasil melakukan sinkronisasi data Master Produk secara massal melalui file Excel.',
                'icon' => 'fas fa-file-excel'
            ]);

            return redirect()->back()->with('success', 'Luar biasa! Ratusan data Excel berhasil masuk ke sistem dalam hitungan detik.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import! Pastikan format kolom Excel sudah benar. Error: ' . $e->getMessage());
        }
    }
}