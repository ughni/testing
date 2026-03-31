<?php

namespace App\Exports;

use App\Models\SupplierOffer; // 🔥 1. DIPERBAIKI: Ganti ke tabel Penawaran
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcessPlanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * Ngedesain biar baris pertama (Header) jadi tebal dan berwarna
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => '4F46E5']]],
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // 🔥 2. DIPERBAIKI: Narik dari SupplierOffer, bukan Supplier
        return SupplierOffer::where('status', 'approved')->orderBy('supplier_name', 'asc')->get();
    }

    /**
     * Judul Kolom (Mirip kayak screenshot lu)
     */
    public function headings(): array
    {
        return [
            'Tanggal Persetujuan',
            'Nama Produk',
            'Nama Supplier (Toko)',
            'HPP Lama (Harga Kemarin)',
            'Harga Deal Baru (Rp)',
            'Sisa Stok (Gudang)',
            'Status Stok'
        ];
    }

    /**
     * Map data dari database ke kolom Excel
     */
    public function map($offer): array
    {
        // Cari produknya buat ngecek HPP lama dan stok
        $product = Product::where('product_name', $offer->product_name)
                    ->with(['dailyInputs' => function($q) {
                        $q->orderBy('input_date', 'desc');
                    }])->first();
        
        $latestInput = $product ? $product->dailyInputs->first() : null;
        
        $hppLama = $latestInput ? $latestInput->hpp : 0;
        $stok = $latestInput ? $latestInput->stock : 0;
        
        $statusStok = $stok < 20 ? 'KURANG / KRITIS' : 'AMAN';

        return [
            \Carbon\Carbon::parse($offer->updated_at)->format('d-m-Y'), // 🔥 DIPERBAIKI DITAMBAH: Pakai updated_at biar akurat pas di-ACC
            $offer->product_name,
            $offer->supplier_name,
            $hppLama,
            $offer->price,
            $stok,
            $statusStok
        ];
    }
}