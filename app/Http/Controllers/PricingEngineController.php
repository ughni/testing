<?php

namespace App\Http\Controllers;

use App\Models\DailyPricing;
use App\Models\NotificationSystem;
use App\Models\Product;
use Illuminate\Http\Request;

class PricingEngineController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('pricing.index', compact('products'));
    }

    public function calculate(Request $request)
    {
        if ($request->filled('demand')) {
    $request->merge([
        'demand' => strtolower($request->demand)
    ]);
}
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'date_input' => 'required|date|before_or_equal:today',
            'hpp' => 'required|numeric|gt:0',
            'hpp_prev' => 'nullable|numeric|min:0',
            'manual_margin' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'yield_harian' => 'nullable|numeric|min:1|max:100',
            'shipping_cost' => 'nullable|numeric|min:0',
            'c1' => 'nullable|numeric|min:0',
            'c2' => 'nullable|numeric|min:0',
            'c3' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'demand' => 'required|in:tinggi,normal,rendah',
        ], [
            'hpp.gt' => 'HPP (Harga Pokok) harus lebih besar dari Rp 0!',
        ]);

        $product = Product::findOrFail($request->product_id);

        // ==========================================
        // LOGIKA MARGIN MANUAL (SUDAH DISINKRONKAN DENGAN YIELD & ONGKIR PER-PCS)
        // ==========================================
        if ($request->filled('manual_margin')) {
            $globalSetting = \App\Models\FormulaSetting::first();

            // 1. TENTUKAN YIELD (Prioritas: Form Harian -> Master Produk -> Global Setting -> Default 100%)
            $yield_percent = 1; // Default 100%

            if ($request->filled('yield_harian') && $request->yield_harian > 0) {
                $yield_percent = $request->yield_harian / 100;
            } elseif (isset($product->yield_percent) && $product->yield_percent > 0) {
                // Pasang penjinak angka gede buat Master Produk
                $yield_percent = ($product->yield_percent > 1) ? ($product->yield_percent / 100) : $product->yield_percent;
            } elseif ($globalSetting && isset($globalSetting->yield_percent)) {
                // Pasang penjinak angka gede buat Global Setting
                $yield_percent = ($globalSetting->yield_percent > 1) ? ($globalSetting->yield_percent / 100) : $globalSetting->yield_percent;
            }

            $hppKotor = ((float) $request->hpp) / $yield_percent;

            // ... (Kodingan ke bawahnya tetep sama) ...

            // 2. Hitung Biaya Siluman (Admin Fee Dihapus)
            $taxCost = ($request->tax_percent > 0) ? ($hppKotor * ($request->tax_percent / 100)) : 0;

            $shippingCost = 0;
            if ($request->filled('shipping_cost') && $request->shipping_cost > 0) {
                $shippingCost = $request->shipping_cost; // ✅ LANGSUNG AMBIL ANGKA PER-PCS
            }

            // HPP Bersih Total
            $hppBersih = $hppKotor + $taxCost + $shippingCost;

            // 3. Rumus Gross Margin Retail
            $marginPercent = $request->manual_margin / 100;
            if ($marginPercent >= 1) {
                $marginPercent = 0.99; // Safety system bagi 0
            }

            $finalPrice = $hppBersih / (1 - $marginPercent);

            $actualMargin = ($finalPrice > 0) ? (($finalPrice - $hppBersih) / $finalPrice) : 0;

            // 🔥 OTAK BARU: AMBIL TARGET DARI MASTER / GLOBAL 🔥
            $targetMarkup = isset($product->markup) ? $product->markup : ($globalSetting ? ($globalSetting->markup_base / 100) : 0.20);
            $bufferLimit = isset($product->buffer) ? $product->buffer : ($globalSetting ? ($globalSetting->buffer_percent / 100) : 0.05);

            // Standarisasi desimal
            if ($targetMarkup > 1) {
                $targetMarkup = $targetMarkup / 100;
            }
            if ($bufferLimit > 1) {
                $bufferLimit = $bufferLimit / 100;
            }

            // 🔥 TENTUKAN STATUS WARNA 🔥
            if ($actualMargin < $bufferLimit) {
                $status = 'RED'; // Darurat! Margin di bawah batas toleransi (Buffer)
            } elseif ($actualMargin >= $bufferLimit && $actualMargin < $targetMarkup) {
                $status = 'YELLOW'; // Waspada! Untung sih, tapi di bawah target
            } else {
                $status = 'GREEN'; // Aman Jaya! Sesuai atau melebihi target
            }

            $result = [
                'final_price' => $finalPrice,
                'margin_percent' => round($actualMargin * 100, 2),
                'status_margin' => $status,
                'recommendation_message' => '🎯 Harga Manual diterapkan. Total Biaya Tambahan per-Pcs: Rp '.number_format($taxCost + $shippingCost, 0, ',', '.').' !',
                'yield_percent' => $yield_percent,
            ];
        } else {
            // Eksekusi Mesin Otomatis
            $result = $this->runPricingEngine($request, $product);
        }
        // SIMPAN KE DATABASE
        DailyPricing::create([
            'product_id' => $product->id,
            'date_input' => $request->date_input,
            'hpp' => $request->hpp, 
            'hpp_prev' => $request->hpp_prev,
            'c1' => $request->c1,
            'c2' => $request->c2,
            'c3' => $request->c3,
            'yield_applied' => $result['yield_percent'],
            'stock' => $request->stock,
            'demand' => $request->demand,
            'final_price' => $result['final_price'],
            'margin_percent' => $result['margin_percent'],
            'status_margin' => $result['status_margin'],
            'supplier_contract_id' => null,
            'active_contract_version' => null,
        ]);

        // =========================================================
        // 🔥 KABEL SINKRONISASI OTOMATIS (SOLUSI PERMANEN) 🔥
        // Memaksa Master Produk update stok detik itu juga!
        // =========================================================
        $product->stock = $request->stock;
        $product->save();
        // =========================================================

        // ALARM MARGIN RUGI
        if ($result['status_margin'] === 'Rugi') {
            NotificationSystem::create([
                'type' => 'error',
                'title' => '🚨 Peringatan Margin Kritis!',
                'message' => 'Margin produk <b>'.$product->product_name.'</b> anjlok ke '.$result['margin_percent'].'%! Segera evaluasi dan naikkan harga jual.',
                'icon' => 'fas fa-exclamation-triangle',
            ]);
        }

        // ALARM STOK MENIPIS
        $batasKritis = $product->threshold ?? 20;
        if ($request->stock < $batasKritis) {
            NotificationSystem::create([
                'type' => 'warning',
                'title' => '📦 Stok Menipis (Purchase Plan)',
                'message' => 'Sisa stok <b>'.$product->product_name.'</b> tersisa '.$request->stock." pcs (Batas Aman: $batasKritis pcs). Segera jadwalkan pembelian ke Supplier!",
                'icon' => 'fas fa-box-open',
            ]);
        }

        return redirect()->back()->with('success', $result['recommendation_message']);
    }

    public function editHistory($id)
    {
        $history = DailyPricing::with('product')->findOrFail($id);
        $products = Product::all();

        return view('pricing.index', compact('history', 'products'));
    }

    public function updateHistory(Request $request, $id)
    {
        if ($request->filled('demand')) {
    $request->merge([
        'demand' => strtolower($request->demand)
    ]);
}
        $request->validate([
            'product_id' => 'required',
            'date_input' => 'required|date',
            'hpp' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'demand' => 'required|in:normal,tinggi,rendah',
        ]);

        $history = DailyPricing::findOrFail($id);
        $product = Product::findOrFail($request->product_id);

        if ($request->filled('manual_margin')) {
            $yield_percent = (isset($product->yield_percent) && $product->yield_percent > 0) ? ($product->yield_percent / 100) : 1;
            $hppBersih = ((float) $request->hpp) / $yield_percent;

            $marginPercent = $request->manual_margin / 100;
            if ($marginPercent >= 1) {
                $marginPercent = 0.99;
            }

            $finalPrice = $hppBersih / (1 - $marginPercent);
            $actualMargin = ($finalPrice > 0) ? (($finalPrice - $hppBersih) / $finalPrice) : 0;
            $status = ($actualMargin < 0) ? 'Rugi' : 'Aman';

            $result = [
                'final_price' => $finalPrice,
                'margin_percent' => round($actualMargin * 100, 2),
                'status_margin' => $status,
            ];
        } else {
            $result = $this->runPricingEngine($request, $product);
        }

        $history->update([
            'product_id' => $request->product_id,
            'date_input' => $request->date_input,
            'hpp' => $request->hpp,
            'hpp_prev' => $request->hpp_prev,
            'c1' => $request->c1,
            'c2' => $request->c2,
            'c3' => $request->c3,
            'stock' => $request->stock,
            'demand' => $request->demand,
            'final_price' => $result['final_price'],
            'margin_percent' => $result['margin_percent'],
            'status_margin' => $result['status_margin'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Data histori harga berhasil diubah dan dikalkulasi ulang!');
    }

    public function destroyHistory($id)
    {
        $history = DailyPricing::findOrFail($id);
        $history->delete();

        return redirect()->back()->with('success', 'Data histori perhitungan harga berhasil dihapus permanen dari sistem.');
    }

    public function analytics()
    {
        $totalProducts = Product::count();
        $tipeDynamic = Product::where('price_type', 'dynamic')->count();
        $tipeConsignment = Product::where('price_type', 'consignment')->count();
        $tipeHET = Product::where('price_type', 'HET')->count();

        $trendData = \App\Models\DailyInput::selectRaw('DATE(input_date) as tanggal, AVG(hpp) as rata_hpp')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->limit(7)
            ->get();

        $labelTanggal = [];
        $dataHPP = [];

        foreach ($trendData as $trend) {
            $labelTanggal[] = date('d M', strtotime($trend->tanggal));
            $dataHPP[] = round($trend->rata_hpp);
        }

        return view('analytics.index', compact(
            'totalProducts', 'tipeDynamic', 'tipeConsignment', 'tipeHET',
            'labelTanggal', 'dataHPP'
        ));
    }

    // ==========================================
    // FUNGSI PRIVATE: OTAK PRICING ENGINE (100% DINAMIS DARI DATABASE SETTINGS)
    // ==========================================
    public function runPricingEngine(Request $request, $product)
    {
        // Panggil Global Setting di paling atas biar bisa dipakai buat Yield
        $globalSetting = \App\Models\FormulaSetting::first();

        // 1. TENTUKAN YIELD (Prioritas: Form Harian -> Master Produk -> Global Setting -> Default 100%)
        $yield_percent = 1; // Default 100%

        if ($request->filled('yield_harian') && $request->yield_harian > 0) {
            $yield_percent = $request->yield_harian / 100;
        } elseif (isset($product->yield_percent) && $product->yield_percent > 0) {
            $yield_percent = ($product->yield_percent > 1) ? ($product->yield_percent / 100) : $product->yield_percent;
        } elseif ($globalSetting && isset($globalSetting->yield_percent)) {
            $yield_percent = ($globalSetting->yield_percent > 1) ? ($globalSetting->yield_percent / 100) : $globalSetting->yield_percent;
        }

        $hppKotor = ((float) $request->hpp) / $yield_percent;

        // 2. HITUNG BIAYA EKSTRA (PAJAK & ONGKIR PER-PCS)
        $taxCost = ($request->tax_percent > 0) ? ($hppKotor * ($request->tax_percent / 100)) : 0;

        $shippingCost = 0;
        if ($request->filled('shipping_cost') && $request->shipping_cost > 0) {
            $shippingCost = $request->shipping_cost; // ✅ LANGSUNG AMBIL ANGKA PER-PCS
        }

        // HPP Bersih = HPP (Yield) + Biaya Siluman
        $hpp = $hppKotor + $taxCost + $shippingCost;
        $finalPrice = $hpp;

        // 3. AMBIL DATA SETTINGS MASTER PRODUK & GLOBAL SETTING
        $buffer_percent = isset($product->buffer) ? $product->buffer : ($globalSetting ? ($globalSetting->buffer_percent / 100) : 0.05);
        if ($buffer_percent > 1) {
            $buffer_percent = $buffer_percent / 100;
        }

        $markup_base = isset($product->markup) ? $product->markup : ($globalSetting ? ($globalSetting->markup_base / 100) : 0.20);
        if ($markup_base > 1) {
            $markup_base = $markup_base / 100;
        }

        $threshold_stock = isset($product->threshold) ? $product->threshold : ($globalSetting ? $globalSetting->threshold_stock : 20);

        $floor_price = $hpp * (1 + $buffer_percent);

        $competitorPrices = array_filter([$request->c1, $request->c2, $request->c3], 'is_numeric');
        if (count($competitorPrices) > 0) {
            sort($competitorPrices);
            $count = count($competitorPrices);
            $mid = floor(($count - 1) / 2);
            $median_market = ($count % 2) ? $competitorPrices[$mid] : ($competitorPrices[$mid] + $competitorPrices[$mid + 1]) / 2;
        } else {
            $median_market = $hpp * 1.25;
        }

        // PERBAIKAN RUMUS BASE PRICE (GROSS MARGIN)
        if ($markup_base >= 1) {
            $markup_base = 0.99;
        }
        $base_price = $hpp / (1 - $markup_base);

        // =========================================================
        // 🔥 ADJUSTMENT TREND, DEMAND, STOK (DARI TABEL BARU LU) 🔥
        // =========================================================
        $autoAdjust = \App\Models\AdjustmentRule::first();

        $adj_hpp = 0;
        // Pake database desimal, nggak usah dibagi 100 lagi!
        $batas_hpp_naik = $autoAdjust ? $autoAdjust->hpp_increase_threshold : 0.03;
        $aksi_hpp_naik = $autoAdjust ? $autoAdjust->hpp_adjustment : 0.03;

        if ($request->hpp_prev && $request->hpp_prev > 0) {
            $hpp_growth = ($hpp - $request->hpp_prev) / $request->hpp_prev;
            if ($hpp_growth > $batas_hpp_naik) {
                $adj_hpp = $aksi_hpp_naik;
            }
        }

        $adj_demand = 0;
        $rule_demand_tinggi = $autoAdjust ? $autoAdjust->demand_high_adjustment : 0.03;
        $rule_demand_rendah = $autoAdjust ? -abs($autoAdjust->demand_low_adjustment) : -0.03;

        if ($request->demand === 'tinggi') {
            $adj_demand = $rule_demand_tinggi;
        } elseif ($request->demand === 'rendah') {
            $adj_demand = $rule_demand_rendah;
        }

        $adj_stock = 0;
        $rule_stok_menipis = $autoAdjust ? $autoAdjust->stock_low_adjustment : 0.02;

        if ($request->stock < $threshold_stock) {
            $adj_stock = $rule_stok_menipis;
        }

        $adjusted_price = $base_price * (1 + $adj_hpp + $adj_demand + $adj_stock);
        // =========================================================

        $temp_price = ($adjusted_price + $median_market) / 2;
        $dynamic_price = max($temp_price, $floor_price);

        // 4. PENENTUAN HARGA FINAL BERDASARKAN TIPE
        if (strtolower($product->price_type) === 'consignment') {
            if (! empty($product->selling_price_fixed)) {
                $finalPrice = $product->selling_price_fixed;
            } else {
                $margin = $product->consignment_margin ?? 0.10;
                if ($margin >= 1) {
                    $margin = 0.99;
                }
                $finalPrice = $hpp / (1 - $margin);
            }
        } elseif (strtoupper($product->price_type) === 'HET') {
            $het_price = $product->het_price ?? 999999999;
            $finalPrice = ($dynamic_price > $het_price) ? $het_price : $dynamic_price;
        } else {
            $finalPrice = $dynamic_price;
        }

        // 5. KALKULASI MARGIN AKTUAL & STATUS (OTAK BARU)
        $marginPercent = ($finalPrice > 0) ? (($finalPrice - $hpp) / $finalPrice) : 0;

        if ($marginPercent < $buffer_percent) {
            $statusMargin = 'RED'; // Margin lebih kecil dari Buffer = Darurat
            $recMsg = '🔴 Darurat (RED)! Margin aktual ('.round($marginPercent * 100, 2).'%) menembus batas bawah toleransi (Buffer). Evaluasi Harga/Modal!';
        } elseif ($marginPercent >= $buffer_percent && $marginPercent < $markup_base) {
            $statusMargin = 'YELLOW'; // Di atas buffer, tapi belum nyampe target = Waspada
            $recMsg = '🟡 Waspada (YELLOW). Margin aktual ('.round($marginPercent * 100, 2).'%) di bawah target ideal. Cek demand atau kompetitor.';
        } else {
            $statusMargin = 'GREEN'; // Sesuai/melebihi target = Aman
            $recMsg = '🟢 Aman (GREEN). Kalkulasi Selesai. Total Biaya Ekstra (per-Pcs): Rp '.number_format($taxCost + $shippingCost, 0, ',', '.').'.';
        }

        return [
            'final_price' => $finalPrice,
            'margin_percent' => round($marginPercent * 100, 2),
            'status_margin' => $statusMargin,
            'recommendation_message' => $recMsg,
            'yield_percent' => $yield_percent,
        ];
    }

    public function history()
    {
        // 1. Ambil data kontrak supplier
        $contracts = \App\Models\SupplierContract::with('supplier')->orderBy('created_at', 'desc')->get();

        // 2. Ambil versi kontrak tertinggi
        $maxVersions = \App\Models\SupplierContract::selectRaw('supplier_id, MAX(contract_version) as max_version')
            ->groupBy('supplier_id')
            ->pluck('max_version', 'supplier_id');

        // 3. Ambil data riwayat harga
        $histories = DailyPricing::with('product')->orderBy('created_at', 'desc')->paginate(10);

        // 🔥 4. DATA GRAFIK DUMMY (Sesuai dengan nama variabel di Blade lu) 🔥
        $chartLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $chartHpp = [45000, 46000, 46000, 48000, 49000, 50000, 50000]; // Dummy Data HPP
        $chartPrice = [55000, 57000, 57000, 60000, 62000, 65000, 65000]; // Dummy Data Harga Jual

        // 🔥 5. MASUKIN SEMUA VARIABEL KE COMPACT 🔥
        return view('pricing.history', compact('contracts', 'maxVersions', 'histories', 'chartLabels', 'chartHpp', 'chartPrice'));
    }
}
