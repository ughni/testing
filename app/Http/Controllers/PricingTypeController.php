<?php

namespace App\Http\Controllers;

use App\Models\Product; // Pastikan model Product kepanggil
use Illuminate\Http\Request;

class PricingTypeController extends Controller
{
    // 🔥 PENTING: Tambahin Request $request di dalem kurung ini!
    public function index(Request $request, $type)
    {
        // 1. Validasi Keamanan: Pastikan URL gak diketik ngasal
        $validTypes = ['dynamic', 'consignment', 'HET'];
        if (!in_array($type, $validTypes)) {
            abort(404); // Kalau bos lu ngetik tipe aneh, langsung error 404
        }

        // 2. Siapkan Query Dasar (Belom narik data, baru ancang-ancang)
        $query = Product::where('price_type', $type)->orderBy('created_at', 'desc');

        // 🔥 3. OTAK PENCARIAN (FILTER) 🔥
        // Kalau bos lu ngetik sesuatu di kotak Search, fungsi ini bakal nyari
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_name', 'like', "%{$searchTerm}%")
                  // Baris orWhere('sku'...) dihapus!
                  ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        // 🔥 4. EKSEKUSI TARIK DATA PAKE PAGINATE (BUKAN GET) 🔥
        // Kita tarik 15 data per halaman biar rapi
        $products = $query->paginate(15);

        // 5. UI Generator (Biar 1 file Blade bisa ganti-ganti wajah)
        $ui = [
            'dynamic' => [
                'title' => 'Dynamic Pricing', 
                'icon' => 'fa-chart-line', 
                'desc' => 'Daftar produk dengan harga jual fluktuatif yang otomatis menyesuaikan pergerakan HPP harian.', 
                'color' => 'blue'
            ],
            'consignment' => [
                'title' => 'Consignment Product', 
                'icon' => 'fa-handshake', 
                'desc' => 'Daftar produk titipan/konsinyasi dengan sistem margin tetap atau bagi hasil dari vendor.', 
                'color' => 'purple'
            ],
            'HET' => [
                'title' => 'HET Product', 
                'icon' => 'fa-shield-alt', 
                'desc' => 'Daftar produk yang dilindungi aturan Harga Eceran Tertinggi (HET) dari regulasi pemerintah/pabrik.', 
                'color' => 'amber'
            ],
        ];

        $pageData = $ui[$type];

        return view('pricing_type.index', compact('products', 'type', 'pageData'));
    }
}