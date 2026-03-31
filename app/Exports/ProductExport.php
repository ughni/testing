<?php

namespace App\Exports;

use App\Models\DailyPricing; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function collection()
    {
        // Narik semua data histori harga beserta relasi produknya
        return DailyPricing::with('product')->orderBy('date_input', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Produk',
            'Kategori',
            'HPP (Rp)',
            'Kompetitor 1 (Rp)',
            'Kompetitor 2 (Rp)',
            'Kompetitor 3 (Rp)',
            'Rekomendasi Harga (Rp)',
            'Margin',
            'Sisa Stok',
            'Status Stok' // Berada di Kolom K
        ];
    }

    public function map($dp): array
    {
        // Logika Status Stok (Anggap batas kritis adalah di bawah 10 unit)
        $stock = $dp->stock ?? 0;
        $statusStok = $stock < 10 ? 'KURANG' : 'AMAN';

        return [
            \Carbon\Carbon::parse($dp->date_input)->format('d M Y'),
            $dp->product->product_name ?? 'Produk Dihapus',
            $dp->product->category ?? '-',
            $dp->hpp,
            $dp->c1,
            $dp->c2,
            $dp->c3,
            $dp->final_price,
            ($dp->margin_percent * 100) . '%',
            $stock,
            $statusStok,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // 1. Bikin Header (Baris 1 A-K) jadi Bold dan berlatar Abu-abu
                $sheet->getStyle('A1:K1')->getFont()->setBold(true);
                $sheet->getStyle('A1:K1')->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFD3D3D3');

                // 2. Looping data dari baris ke-2 sampai bawah untuk mewarnai status
                for ($row = 2; $row <= $highestRow; $row++) {
                    
                    // MEWARNAI STATUS STOK AMAN/KURANG (Sekarang di KOLOM K)
                    $statusStok = $sheet->getCell("K{$row}")->getValue();
                    if ($statusStok === 'KURANG') {
                        // Background Merah Muda, Teks Merah Tua
                        $sheet->getStyle("K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCCCC');
                        $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB(Color::COLOR_RED);
                    } elseif ($statusStok === 'AMAN') {
                        // Background Hijau Muda, Teks Hijau Tua
                        $sheet->getStyle("K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
                        $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB('FF008000');
                    }
                }
                
                // Auto-size kolom biar rapi nggak nabrak
                foreach (range('A', 'K') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}