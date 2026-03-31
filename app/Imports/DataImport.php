<?php

namespace App\Imports;

use App\Models\Product;
// use App\Models\Supplier; 🔴 (Nggak perlu dipanggil lagi, karena kita biarin kosong)
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class DataImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. TANGKAP NAMA KOLOM EXCEL (Header Excel: "nama", "kategori_material", "unit_beli")
        $nama_produk = $row['nama'] ?? null;
        
        // 2. ABAIKAN BARIS KOSONG
        if (empty($nama_produk)) {
            return null;
        }

        // 3. BERSIHKAN DATA (Sanitasi Anti XSS)
        $nama_produk = strip_tags(trim($nama_produk));
        $kategori    = $row['kategori_material'] ?? 'Umum';
        
        // Deteksi Satuan Pintar (Tetap gue pertahanin, ini bagus!)
        $rawUnit = strtolower(trim($row['unit_beli'] ?? 'pcs'));
        $satuan = 'Pcs'; // Default
        
        if (Str::contains($rawUnit, 'kg')) { $satuan = 'Kg'; }
        elseif (Str::contains($rawUnit, 'gr') || Str::contains($rawUnit, 'gram')) { $satuan = 'Gram'; }
        elseif (Str::contains($rawUnit, 'ltr') || Str::contains($rawUnit, 'lt')) { $satuan = 'Liter'; }
        elseif (Str::contains($rawUnit, 'ml')) { $satuan = 'Mililiter'; }
        elseif (Str::contains($rawUnit, 'sct') || Str::contains($rawUnit, 'sachet')) { $satuan = 'Sachet'; }
        elseif (Str::contains($rawUnit, 'btl') || Str::contains($rawUnit, 'botol')) { $satuan = 'Botol'; }
        elseif (Str::contains($rawUnit, 'pack') || Str::contains($rawUnit, 'pck')) { $satuan = 'Pack'; }

        // 4. SIMPAN KE DATABASE
        return Product::firstOrCreate(
            ['product_name' => $nama_produk],
            [
                'category'            => trim($kategori),
                'unit'                => $satuan,
                'price_type'          => 'dynamic', 
                'het_price'           => null, // Diubah ke null sesuai standar
                'consignment_margin'  => null, // Diubah ke null sesuai standar
                'description'         => 'Diimpor otomatis dari Master Data Excel',
                'is_active'           => 1,
                // 👇🔥 INI HASIL KITA NEGOSIASI KE BOS YUDHI! 🔥👇
                'supplier_id'         => null // Langsung isi null tanpa ribet bikin data palsu!
            ]
        );
    }
}