<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DailyPricing;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use App\Models\PriceHistory;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataImport;

class PricingController extends Controller
{
    public function calculate(Request $request)
    {
        // Validasi input data user
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'hpp' => 'required|numeric|min:0',
            'c1' => 'nullable|numeric|min:0',
            'c2' => 'nullable|numeric|min:0',
            'c3' => 'nullable|numeric|min:0',
        ]);

        // 1. Ambil data settingan 'angka rahasia' dari database
        $settings = SystemSetting::first();
        if (!$settings) {
            return redirect()->back()->with('error', 'Pengaturan sistem tidak ditemukan.');
        }

        $product = Product::findOrFail($request->product_id);

        $hpp = $request->hpp;
        $c1 = $request->c1 ?? 0;
        $c2 = $request->c2 ?? 0;
        $c3 = $request->c3 ?? 0;

        // 2. LOGIKA RUMUS DINAMIS (Sesuai Dokumen Klien)
        // Cari nilai tengah (Median) dari 3 kompetitor
        $competitors = array_filter([$c1, $c2, $c3], function($value) {
            return !is_null($value) && $value !== '';
        });
        if (empty($competitors)) {
            $median_market = $hpp;
        } else {
            sort($competitors);
            $median_market = $competitors[floor(count($competitors) / 2)];
        }

        // Hitung harga dasar (HPP + Markup 20%)
        // Pastikan markup_base tidak 1 (100%) untuk menghindari division by zero
        $markup_base = $settings->markup_base ?? 0.2; // default 20%
        if ($markup_base >= 1) {
            $markup_base = 0.2; // fallback
        }
        $base_price = $hpp / (1 - $markup_base);

        // Tentukan harga akhir (Nilai tengah antara harga kita vs harga pasar)
        $final_price = ($base_price + $median_market) / 2;

        // 3. CEK KEAMANAN HARGA (HET & Buffer)
        // Harga gak boleh di bawah modal + buffer 5%
        $buffer_percent = $settings->buffer_percent ?? 0.05;
        $floor_price = $hpp * (1 + $buffer_percent);
        if ($final_price < $floor_price) {
            $final_price = $floor_price;
        }

        // Kalau produk tipe HET, harga gak boleh ngelewati batas pemerintah
        if (isset($product->price_type) && $product->price_type === 'HET' && isset($product->het_price) && $final_price > $product->het_price) {
            $final_price = $product->het_price;
        }

        // 4. TENTUKAN STATUS MARGIN (Warna Indikator)
        $margin = $hpp > 0 ? ($final_price - $hpp) / $final_price : 0;
        $status = 'GREEN';
        if ($margin < 0.10) {
            $status = 'RED';
        } elseif ($margin < 0.20) {
            $status = 'YELLOW';
        }

        // Pembulatan harga
        $harga_rekomendasi = round($final_price);
        PriceHistory::create([
                    'product_id' => $request->product_id, // Mengambil ID produk dari form
                    'hpp' => $request->hpp,               // Mengambil HPP dari form
                    'suggested_price' => $harga_rekomendasi, // Pastikan nama variabel ini sesuai dengan milikmu
                    'margin_percentage' => 20,            // Masukkan variabel margin persentasenya (bisa disesuaikan)
                    'status' => 'YELLOW',                 // Pastikan nama variabel status ini sesuai dengan milikmu
        ]);
        return redirect()->back()->with('success', 'Kalkulasi Berhasil! Harga Rekomendasi: Rp ' . number_format($harga_rekomendasi, 0, ',', '.') . ' (Status: ' . $status . ')');
    }
    /**
     * Fitur Download Excel dari Dashboard
     */
    public function importExcel(Request $request)
    {
        // Validasi file harus Excel
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            // Proses file Excel-nya
            Excel::import(new DataImport, $request->file('file_excel'));
            
            return redirect()->back()->with('success', 'Luar biasa! Ratusan data Excel berhasil masuk!');
        } catch (\Exception $e) {
            // KALAU ERROR, MUNCULKAN TEKS ASLINYA BIAR KITA TAHU PENYAKITNYA!
            dd('ERROR SAAT IMPORT: ' . $e->getMessage());
        }
    }
    /**
     * Menyediakan data JSON untuk Grafik Per Produk di Modal
     */
    public function getProductChart($id)
    {
        // Ambil histori harga untuk 1 produk ini, urutkan dari yang terlama ke terbaru
        $data = DailyPricing::where('product_id', $id)
                                        ->orderBy('date_input', 'asc')
                                        ->get();

        // Siapkan format array untuk digambar sama Chart.js
        return response()->json([
            'labels' => $data->pluck('date_input')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d M');
            }),
            'hpp' => $data->pluck('hpp'),
            'price' => $data->pluck('final_price'),
        ]);
    }
    public function exportExcel()
    {
        return Excel::download(new ProductExport, 'Rekap_Harga_Pasar.xlsx');
    }
}