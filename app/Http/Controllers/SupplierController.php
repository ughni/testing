<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\NotificationSystem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * TAMPILKAN DAFTAR KTP SUPPLIER (Master Data)
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                    ->orWhere('no_supplier', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kualifikasi')) {
            $query->where('kualifikasi', $request->kualifikasi);
        }

        $suppliers = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * TAMBAH SUPPLIER BARU (Buku KTP)
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_supplier' => 'required|string|unique:suppliers,no_supplier|max:50',
            'nama_supplier' => 'required|string|max:150',
            'kualifikasi' => 'required|in:produsen,distributor,agen,retail,pasar',
            'alamat' => 'nullable|string',
            'kontak_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
        ]);

        $supplier = Supplier::create([
            'no_supplier' => strip_tags($request->no_supplier),
            'nama_supplier' => strip_tags($request->nama_supplier),
            'kualifikasi' => $request->kualifikasi,
            'alamat' => strip_tags($request->alamat),
            'kontak_person' => strip_tags($request->kontak_person),
            'email' => strip_tags($request->email),
            'is_active' => true,
            'is_contract' => false,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'CREATE',
            'module' => 'Supplier Master',
            'description' => "Menambahkan Master Supplier baru: {$supplier->nama_supplier} ({$supplier->no_supplier}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Master Supplier berhasil ditambahkan ke database!');
    }

    /**
     * UPDATE DATA SUPPLIER
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'no_supplier' => 'required|string|max:50|unique:suppliers,no_supplier,'.$id,
            'nama_supplier' => 'required|string|max:150',
            'kualifikasi' => 'required|in:produsen,distributor,agen,retail,pasar',
            'is_active' => 'boolean',
        ]);

        $supplier->update([
            'no_supplier' => strip_tags($request->no_supplier),
            'nama_supplier' => strip_tags($request->nama_supplier),
            'kualifikasi' => $request->kualifikasi,
            'alamat' => strip_tags($request->alamat ?? $supplier->alamat),
            'kontak_person' => strip_tags($request->kontak_person ?? $supplier->kontak_person),
            'email' => strip_tags($request->email ?? $supplier->email),
            'is_active' => $request->has('is_active') ? $request->is_active : $supplier->is_active,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Supplier Master',
            'description' => "Mengubah data profil Supplier: {$supplier->nama_supplier}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Profil Supplier berhasil diperbarui!');
    }

    /**
     * HAPUS SUPPLIER 
     */
    public function destroy(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'DELETE',
            'module' => 'Supplier Master',
            'description' => "Menghapus permanen supplier {$supplier->nama_supplier} dari sistem.",
            'ip_address' => $request->ip(),
        ]);

        $supplier->delete();

        return redirect()->back()->with('success', 'Supplier berhasil dihapus. Data produk terkait otomatis di-set ke Non-Supplier.');
    }

    /**
     * PRODUK PER SUPPLIER (Tampilan Sesuai DB Pak Yudhi - Aturan Kontrak)
     */
    public function productsPerSupplier(Request $request)
    {
        $searchProduct = $request->input('search_product');
        $searchSupplier = $request->input('search_supplier');
        $searchCategory = $request->input('category');

        $query = \App\Models\Product::with(['supplier']);

        if (! empty($searchCategory)) {
            $query->where('category', $searchCategory);
        }

        if (! empty($searchProduct)) {
            $query->where('product_name', $searchProduct);
        }

        if (! empty($searchSupplier)) {
            $query->whereHas('supplier', function ($q) use ($searchSupplier) {
                $q->where('nama_supplier', $searchSupplier);
            });
        }

        // Tampilkan data Pagination
        $products = $query->latest('updated_at')->paginate(10);

        // Untuk Dropdown Filter
        $suppliers = Supplier::select('nama_supplier')->distinct()->orderBy('nama_supplier', 'asc')->get();
        $allProductsList = \App\Models\Product::select('product_name')->distinct()->orderBy('product_name', 'asc')->get();

        return view('suppliers.products', compact(
            'products',
            'searchProduct',
            'searchSupplier',
            'suppliers',
            'allProductsList'
        ));
    }

    // ==========================================
    // 🔥 UPLOAD KONTRAK 🔥
    // ==========================================
    public function uploadContractForm()
    {
        $suppliers = Supplier::select('id', 'nama_supplier')->distinct()->get();
        $contracts = \App\Models\SupplierContract::with('supplier')->orderBy('created_at', 'desc')->get();

        return view('suppliers.upload_contract', compact('suppliers', 'contracts'));
    }

    public function storeContract(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'contract_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'valid_until' => 'required|date',
        ]);

        $supplierId = $request->supplier_id;
        $lastVersion = \App\Models\SupplierContract::where('supplier_id', $supplierId)->max('contract_version');
        $newVersion = $lastVersion ? $lastVersion + 1 : 1;

        $file = $request->file('contract_file');
        $fileNameToStore = time().'_V'.$newVersion.'_'.str_replace(' ', '_', $file->getClientOriginalName());
        $path = $file->storeAs('contracts', $fileNameToStore, 'public');

        $contract = new \App\Models\SupplierContract;
        $contract->supplier_id = $supplierId;
        $contract->contract_file = $path;
        $contract->contract_version = $newVersion;
        $contract->valid_until = $request->valid_until;
        $contract->save();

        Supplier::where('id', $supplierId)->update(['is_contract' => true]);

        $supplierNameLog = Supplier::find($supplierId)->nama_supplier ?? 'Unknown Supplier';

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'CREATE',
            'module' => 'Document Center',
            'description' => "Mengunggah kontrak baru (V-$newVersion) untuk supplier \"$supplierNameLog\".",
            'ip_address' => $request->ip(),
        ]);

        NotificationSystem::create([
            'type' => 'success',
            'title' => '📄 Kontrak Diperbarui',
            'message' => "Dokumen kontrak baru (V-{$newVersion}) untuk Supplier <b>{$supplierNameLog}</b> berhasil diunggah.",
            'icon' => 'fas fa-file-contract',
        ]);

        return redirect()->back()->with('success', 'Aman! Kontrak berhasil diupload.');
    }

    public function contractHistory()
    {
        $contracts = \App\Models\SupplierContract::with('supplier')->orderBy('created_at', 'desc')->get();
        
        $maxVersions = \App\Models\SupplierContract::selectRaw('supplier_id, MAX(contract_version) as max_version')
            ->groupBy('supplier_id')
            ->pluck('max_version', 'supplier_id');

        return view('suppliers.contract_history', compact('contracts', 'maxVersions'));
    }

    // ==========================================
    // 🔥 FUNGSI BARU: UPDATE KONTRAK HARGA (HET/Consignment/Fixed) 🔥
    // ==========================================
    public function updateProductContract(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);

        $request->validate([
            'price_type' => 'required|in:dynamic,consignment,het,fixed',
            'het_price' => 'nullable|numeric|min:0',
            'consignment_margin' => 'nullable|numeric|min:0',
            'selling_price_fixed' => 'nullable|numeric|min:0',
        ]);

        $product->update([
            'price_type' => $request->price_type,
            'het_price' => $request->price_type == 'het' ? $request->het_price : null,
            'consignment_margin' => $request->price_type == 'consignment' ? $request->consignment_margin : null,
            'selling_price_fixed' => $request->price_type == 'fixed' ? $request->selling_price_fixed : null,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Produk per Supplier',
            'description' => "Memperbarui aturan kontrak harga ({$request->price_type}) untuk produk: {$product->product_name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Aturan Kontrak Harga berhasil diperbarui, Bos!');
    }
}