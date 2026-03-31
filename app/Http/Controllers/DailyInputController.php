<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyInputRequest;
use App\Models\AuditLog;
use App\Models\DailyInput;
use App\Models\DailyPricing;
use App\Models\NotificationSystem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyInputController extends Controller
{
  /**
     * Menyimpan data harian ke database dan otomatis memicu Pricing Engine
     */
    public function store(StoreDailyInputRequest $request)
    {
        // 1. Data yang masuk dari Form HTML dijamin bersih
        $validated = $request->validated();

        // 2. Cari HPP dari inputan hari sebelumnya (sebelum input_date)
        $dataKemarin = DailyInput::where('product_id', $validated['product_id'])
            ->whereDate('input_date', '<', $validated['input_date'])
            ->orderBy('input_date', 'desc')
            ->first();

        $hppKemarin = $dataKemarin ? $dataKemarin->hpp : $validated['hpp'];

        // 3. Simpan Raw Data ke tabel `daily_inputs` (Input Harian Asli)
        $dailyInput = DailyInput::updateOrCreate(
            [
                'product_id' => $validated['product_id'],
                'input_date' => $validated['input_date'],
            ],
            $validated // Update semua data dari form HTML
        );

        $actionType = $dailyInput->wasRecentlyCreated ? 'CREATE' : 'UPDATE';

        // ==========================================
        // 🔥 TAHAP 2 (A): UPDATE BRANKAS STOK KTP PRODUK! 🔥
        // ==========================================
        $product = Product::findOrFail($validated['product_id']);
        
        // Timpa angka stok di Master Produk pakai angka Sisa Stok dari Input Harian
        $product->update([
            'stock' => $validated['stock'] 
        ]);
        // ==========================================

        // 4. JALANKAN MESIN PRICING ENGINE OTOMATIS!
        $engine = new PricingEngineController;
        $result = $engine->runPricingEngine($request, $product);

        // 5. Simpan Hasil Masakan ke Etalase Dashboard Laporan (tabel `daily_pricings`)
        DailyPricing::updateOrCreate(
            [
                'product_id' => $product->id,
                'date_input' => $validated['input_date'],
            ],
            [
                'hpp' => $validated['hpp'],
                'hpp_prev' => $hppKemarin, 
                'c1' => $request->c1 ?? null, 
                'c2' => $request->c2 ?? null,
                'c3' => $request->c3 ?? null,
                'stock' => $validated['stock'],
                'demand' => $request->demand ?? 'normal', 
                'final_price' => $result['final_price'] ?? 0,
                'margin_percent' => $result['margin_percent'] ?? 0,
                'status_margin' => $result['status_margin'] ?? 'Aman',
            ]
        );

        // --- 🎥 CCTV INPUT HARIAN ---
        $verb = $actionType === 'CREATE' ? 'Memasukkan' : 'Memperbarui';
        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => $actionType,
            'module' => 'Input Harian',
            'description' => "$verb data stok dan HPP (Rp ".number_format($validated['hpp'], 0, ',', '.').') untuk produk "'.$product->product_name.'".',
            'ip_address' => $request->ip(),
        ]);

        // --- 🔥 ALARM NOTIFIKASI OTOMATIS JIKA RUGI 🔥 ---
        if (isset($result['status_margin']) && $result['status_margin'] == 'Rugi') {
            NotificationSystem::create([
                'type' => 'danger',
                'title' => 'Peringatan: Margin Rugi Terdeteksi!',
                'message' => 'HPP terbaru untuk produk <b>'.$product->product_name.'</b> menyebabkan margin terjun bebas ke zona merah. Segera lakukan penyesuaian HET atau hubungi Supplier!',
                'icon' => 'fas fa-exclamation-triangle',
            ]);
        }

        // 6. Redirect dengan pesan sukses
        $pesanRekomendasi = $result['recommendation_message'] ?? 'Harga berhasil dikalkulasi.';

        return redirect()->back()->with('success', 'Data tersimpan! '.$pesanRekomendasi);
    }

    /**
     * Menampilkan form input harian
     */
    public function create()
    {
        $products = Product::all();

        return view('daily_inputs.create', compact('products'));
    }

    /**
     * Menampilkan Halaman Laporan Pricing Engine + Filter + Export Excel
     */
    public function reportPricing(Request $request)
    {
        // 1. Mulai Query dasar
        $query = DailyPricing::with('product')
            ->orderBy('date_input', 'desc')
            ->orderBy('created_at', 'desc'); // 🔥 INI OBATNYA! Biar yang detik ini diinput langsung nongkrong di baris paling atas!

        // 2. LOGIKA FILTER (Jika bos lu nyari nama barang, tanggal, atau status)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date_input', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status')) {
            $query->where('status_margin', $request->status);
        }

        // 3. LOGIKA EXCEL DOWNLOAD (Disamain plek ketiplek sama tabel di website!)
        if ($request->has('export') && $request->export == 'excel') {
            $pricings = $query->get(); // Ambil SEMUA data yang udah difilter

            $fileName = 'Laporan_Pricing_Engine_'.date('Y-m-d').'.csv';
            $headers = [
                'Content-type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$fileName",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($pricings) {
                $file = fopen('php://output', 'w');

                // Judul Paling Atas
                fputcsv($file, ['LAPORAN PRICING ENGINE & DATA PASAR']);
                fputcsv($file, []); // Baris Kosong

                // BARIS HEADER 1 (Grup Utama persis di Website)
                fputcsv($file, [
                    'Informasi Produk & Gudang', '', '', '', // 4 kolom digabung
                    'Pergerakan HPP', '',                    // 2 kolom
                    'Kondisi Pasar', '', '', '',             // 4 kolom
                    'Variabel Pabrik', '',                   // 2 kolom
                    'Hasil Kalkulasi Engine', '', '',         // 3 kolom
                ]);

                // BARIS HEADER 2 (Sub-Kolom persis di Website)
                fputcsv($file, [
                    'Nama Barang & Tanggal', 'Kategori', 'Sisa Stok', 'Batas Kritis',
                    'HPP Kemarin', 'HPP Hari Ini',
                    'Demand', 'Komp 1', 'Komp 2', 'Komp 3',
                    'Yield (%)', 'Target Marjin',
                    'Harga Rekomendasi', 'Marjin Aktual', 'Status',
                ]);

                // ISI DATANYA BARIS PER BARIS
                foreach ($pricings as $row) {
                    fputcsv($file, [
                        ($row->product->product_name ?? 'N/A').' ('.$row->date_input.')',
                        $row->product->category ?? '-',
                        $row->stock.' '.($row->product->unit ?? 'Pcs'),
                        $row->threshold_stock ?? 20,
                        $row->hpp_prev,
                        $row->hpp,
                        ucfirst($row->demand), // Biar huruf depannya besar (Tinggi/Normal/Rendah)
                        $row->c1 ?? 0,
                        $row->c2 ?? 0,
                        $row->c3 ?? 0,
                        ($row->product->yield ?? 100).'%',
                        (($row->markup_base ?? 0) * 100).'%',
                        $row->final_price,
                        $row->margin_percent.'%',
                        ucfirst($row->status_margin), // Status: Aman / Rugi
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // 4. Kalau cuma buka halaman biasa, tampilkan tabel pakai pagination
        // appends($request->all()) buat nahan filter biar pas pindah halaman ke-2, filternya nggak ilang
        $pricings = $query->paginate(15)->appends($request->all());

        return view('reports.pricing', compact('pricings'));
    }
}
