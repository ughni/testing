@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

<style>
    /* 🔥 PAKSA SEMUA FONT JADI INTER BIAR NGGAK KAKU & UKURANNYA SERAGAM 🔥 */
    body, input, select, button, textarea, .ts-control, .ts-dropdown { 
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important; 
    }
    
    .ts-control { padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background-color: #f8fafc; font-size: 0.875rem; font-weight: 500; }
    .dark .ts-control { background-color: rgba(15, 23, 42, 0.5); border-color: #334155; color: #f1f5f9; }
    .dark .ts-dropdown { background-color: #1e293b; border-color: #334155; color: #f1f5f9; font-size: 0.875rem; }
    .dark .ts-dropdown .option:hover, .dark .ts-dropdown .active { background-color: #334155; color: #fff; }
    .ts-wrapper.single .ts-control:after { border-color: #94a3b8 transparent transparent transparent; }
    
    input[type="number"], input[type="text"], input[type="date"], select {
        font-size: 0.875rem !important; 
    }
</style>

@php
    $isEdit = isset($history);
@endphp

<div class="w-full max-w-[100%] px-2 sm:px-4 mx-auto pb-10">
    
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 {{ $isEdit ? 'bg-indigo-600 shadow-indigo-600/20' : 'bg-blue-600 shadow-blue-600/20' }} rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas {{ $isEdit ? 'fa-pen' : 'fa-calculator' }} text-lg"></i>
                </div>
                {{ $isEdit ? 'Edit Kalkulasi Harga' : 'Pricing Engine Calculator' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 ml-14">
                {{ $isEdit ? 'Ubah data historis atau sesuaikan target margin secara manual.' : 'Input data fluktuasi pasar harian untuk mendapatkan rekomendasi harga otomatis.' }}
            </p>
        </div>
        
        @if($isEdit)
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        @endif
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-300">
        <div class="bg-black px-6 sm:px-8 py-5 border-b border-slate-700">
            <h5 class="m-0 font-bold text-white text-lg flex items-center">
                <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-keyboard' }} mr-3 text-blue-400"></i> Form Fluktuasi Pasar
            </h5>
        </div>

        <div class="p-6 sm:p-8">
            <form action="{{ $isEdit ? route('pricing.update', $history->id) : route('pricing.calculate') }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <input type="hidden" name="user_id" value="{{ auth()->id() ?? 1 }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Produk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="select-produk" name="product_id" class="w-full text-slate-800 dark:text-slate-200 font-medium" required>
                                <option value="" data-yield="95" data-buffer="5" data-threshold="20" data-markup="20">-- Ketik untuk mencari produk... --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" 
                                        data-yield="{{ $p->yield_percent ?? 95 }}" 
                                        data-buffer="{{ $p->buffer ?? 5 }}" 
                                        data-threshold="{{ $p->threshold ?? 20 }}"
                                        data-markup="{{ $p->markup ?? 20 }}"
                                        {{ old('product_id', $isEdit ? $history->product_id : '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->product_name }} (Tipe: {{ strtoupper($p->price_type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Input <span class="text-red-500">*</span></label>
                        <input type="date" name="date_input" max="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-colors font-medium" value="{{ old('date_input', $isEdit ? \Carbon\Carbon::parse($history->date_input)->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-sm font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-tag mr-2"></i> 1. Harga Beli (HPP)
                    </h6>
                    <div class="p-5 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-slate-100 dark:border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">HPP Hari Ini (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><span class="text-slate-400 font-bold">Rp</span></div>
                                <input type="number" step="0.01" min="0" name="hpp" id="hpp_hari_ini" value="{{ old('hpp', $isEdit ? (float)$history->hpp : '') }}" class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">HPP Kemarin (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><span class="text-slate-400 font-bold">Rp</span></div>
                                <input type="number" step="0.01" min="0" name="hpp_prev" id="hpp_kemarin" value="{{ old('hpp_prev', ($isEdit && $history->hpp_prev) ? (float)$history->hpp_prev : '') }}" class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-sm font-extrabold text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> 2. Biaya Ekstra (Biaya Siluman)
                    </h6>
                    <div class="bg-rose-50/30 dark:bg-slate-800/30 p-5 rounded-2xl border border-rose-100 dark:border-slate-700/50 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pajak / PPN (%)</label>
                            <div class="relative">
                                <input type="number" name="tax_percent" id="tax_percent" value="{{ old('tax_percent', 0) }}" min="0" step="0.1" class="w-full pl-4 pr-10 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-white">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 font-bold">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Yield Harian (%)</label>
                            <div class="relative">
                                <input type="number" step="0.1" name="yield_harian" id="yield_harian" class="w-full px-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-white" placeholder="Kosongi jika ikut Master">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400 font-bold">%</div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ongkos Kirim (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-bold">Rp</span>
                                <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}" min="0" class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-sm font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-store-alt mr-2"></i> 3. Harga Pasar / Kompetitor
                    </h6>
                    <div class="p-5 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-slate-100 dark:border-slate-800 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 1 (Rp)</label>
                            <input type="number" step="0.01" min="0" name="c1" id="kompetitor_1" value="{{ old('c1', ($isEdit && $history->c1) ? (float)$history->c1 : '') }}" class="w-full px-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 2 (Rp)</label>
                            <input type="number" step="0.01" min="0" name="c2" id="kompetitor_2" value="{{ old('c2', ($isEdit && $history->c2) ? (float)$history->c2 : '') }}" class="w-full px-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 3 (Rp)</label>
                            <input type="number" step="0.01" min="0" name="c3" id="kompetitor_3" value="{{ old('c3', ($isEdit && $history->c3) ? (float)$history->c3 : '') }}"class="w-full px-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-colors">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-sm font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2"></i> 4. Kondisi Lapangan & Target
                    </h6>
                    <div class="p-5 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-slate-100 dark:border-slate-800 grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Sisa Stok <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" min="0" name="stock" id="stok_fisik" value="{{ old('stock', $isEdit ? $history->stock : '') }}" class="w-full px-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 transition-colors" required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none"><span class="text-slate-400 text-sm font-bold">Pcs</span></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Demand <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="demand" id="demand" class="w-full pl-4 pr-10 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 appearance-none transition-colors font-medium" required>
                                    <option value="normal" {{ old('demand', $isEdit ? $history->demand : '') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="tinggi" {{ old('demand', $isEdit ? $history->demand : '') == 'tinggi' ? 'selected' : '' }}>Tinggi (Ramai)</option>
                                    <option value="rendah" {{ old('demand', $isEdit ? $history->demand : '') == 'rendah' ? 'selected' : '' }}>Rendah (Sepi)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i class="fas fa-chevron-down text-xs"></i></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target Margin</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="manual_margin" id="margin_target" value="{{ old('manual_margin', ($isEdit && $history->margin_percent) ? $history->margin_percent : '') }}" class="w-full px-4 py-3 bg-amber-50/30 dark:bg-slate-900/80 border border-amber-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none"><span class="text-amber-500 font-black">%</span></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-500 dark:text-slate-400 mb-2">Yield Aktif</label>
                            <input type="text" id="display_yield" class="w-full px-4 py-3 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-white" readonly placeholder="0%">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-sm font-extrabold text-purple-600 dark:text-purple-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-sliders-h mr-2"></i> 5. Variabel Adjustment & Parameter (Dapat Diubah)
                    </h6>
                    <div class="p-5 bg-purple-50 dark:bg-purple-900/20 rounded-2xl border border-purple-200 dark:border-purple-800 grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Buffer Min (%)</label>
                            <input type="number" step="0.1" id="buffer_input" name="buffer_setup" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-colors" value="5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Threshold Stok</label>
                            <input type="number" id="threshold_input" name="threshold_setup" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-colors" value="20">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rule: HPP Naik (%)</label>
                            <input type="number" step="0.1" id="adj_hpp_input" name="adj_hpp_setup" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-colors" value="3">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rule: Demand (%)</label>
                            <input type="number" step="0.1" id="adj_demand_input" name="adj_demand_setup" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-colors" value="3">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rule: Stok Min (%)</label>
                            <input type="number" step="0.1" id="adj_stok_input" name="adj_stok_setup" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none transition-colors" value="2">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h6 class="text-sm font-extrabold text-green-600 dark:text-green-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-magic mr-2"></i> 6. Recommendation Selling Price (Auto)
                    </h6>
                    <div class="p-6 bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-200 dark:border-green-800 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                        <div>
                            <label class="block text-sm font-bold text-amber-700 mb-2">Floor Price</label>
                            <input type="text" id="display_floor" class="w-full px-4 py-3 bg-amber-100 border border-amber-300 text-amber-800 rounded-xl text-sm font-bold" readonly placeholder="Rp 0">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-blue-700 mb-2">Total Adjustment</label>
                            <input type="text" id="display_adj" class="w-full px-4 py-3 bg-blue-100 border border-blue-300 text-blue-800 rounded-xl text-sm font-bold" readonly placeholder="Rp 0 (0%)">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-purple-700 mb-2">Real Margin</label>
                            <input type="text" id="display_margin" class="w-full px-4 py-3 bg-purple-100 border border-purple-300 text-purple-800 rounded-xl text-lg font-extrabold shadow-inner" readonly placeholder="0%">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-green-800 mb-2">Final Price</label>
                            <input type="text" id="display_final" class="w-full px-4 py-3 bg-green-200 border border-green-400 text-green-900 rounded-xl text-lg font-extrabold shadow-inner" readonly placeholder="Rp 0">
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="w-full {{ $isEdit ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/30' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/30' }} text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all hover:-translate-y-1 focus:ring-4 flex justify-center items-center gap-2 text-lg">
                        <i class="fas {{ $isEdit ? 'fa-save' : 'fa-check-circle' }}"></i> 
                        {{ $isEdit ? 'Update & Simpan Perubahan' : 'Konfirmasi & Simpan Harga Rekomendasi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let ts = new TomSelect("#select-produk",{
            create: false,
            maxOptions: null,
            sortField: { field: "text", direction: "asc" },
            placeholder: "-- Ketik nama produk... --"
        });

        function calculatePricing() {
            let selectEl = document.getElementById('select-produk');
            let selectedOpt = selectEl.options[selectEl.selectedIndex];
            
            // 1. Ambil Yield Default
            let yieldPercent = parseFloat(selectedOpt.getAttribute('data-yield')) || 95;
            let defaultMarkup = parseFloat(selectedOpt.getAttribute('data-markup')) || 20;

            // 2. Ambil Input Biaya Ekstra 🔥
            let yieldHarian = parseFloat(document.getElementById('yield_harian').value);
            let taxPercent = parseFloat(document.getElementById('tax_percent').value) || 0;
            let shippingCost = parseFloat(document.getElementById('shipping_cost').value) || 0;
            
            // Gunakan Yield Harian kalau diisi
            let finalYield = yieldPercent;
            if (!isNaN(yieldHarian) && yieldHarian > 0) {
                finalYield = yieldHarian;
            }

            let hpp = parseFloat(document.getElementById('hpp_hari_ini').value) || 0;
            let hppKemarin = parseFloat(document.getElementById('hpp_kemarin').value) || 0;
            
            let inputMargin = document.getElementById('margin_target').value;
            let marginTarget = (inputMargin !== "") ? parseFloat(inputMargin) : defaultMarkup;
            
            let c1 = parseFloat(document.getElementById('kompetitor_1').value) || 0;
            let c2 = parseFloat(document.getElementById('kompetitor_2').value) || 0;
            let c3 = parseFloat(document.getElementById('kompetitor_3').value) || 0;
            
            let stok = parseFloat(document.getElementById('stok_fisik').value) || 0;
            let demand = document.getElementById('demand').value || 'normal';

            let buffer = parseFloat(document.getElementById('buffer_input').value) || 0;
            let threshold = parseFloat(document.getElementById('threshold_input').value) || 0;
            
            let ruleHppNaik = parseFloat(document.getElementById('adj_hpp_input').value) || 0;
            let ruleDemandTinggi = parseFloat(document.getElementById('adj_demand_input').value) || 0;
            let ruleStokRendah = parseFloat(document.getElementById('adj_stok_input').value) || 0;

            if (hpp === 0) return;

            // 🔥 LOGIKA MENGHITUNG HPP REAL (TERMASUK PAJAK & ONGKIR) SINKRON SAMA PHP 🔥
            let yieldDecimal = finalYield / 100;
            let hppKotor = hpp / yieldDecimal;
            let taxCost = (taxPercent > 0) ? (hppKotor * (taxPercent / 100)) : 0;
            let hppReal = hppKotor + taxCost + shippingCost;

            let bufferDecimal = buffer / 100;
            let floorPrice = hppReal * (1 + bufferDecimal);

            let marginDecimal = marginTarget / 100;
            let basePrice = marginDecimal < 1 ? (hppReal / (1 - marginDecimal)) : hppReal;

            let adjustment = 0;
            
            if (hpp > hppKemarin && hppKemarin > 0) {
                adjustment += (ruleHppNaik / 100); 
            }
            
            if (demand === 'tinggi') {
                adjustment += (ruleDemandTinggi / 100); 
            } else if (demand === 'rendah') {
                adjustment -= (ruleDemandTinggi / 100); 
            }
            
            if (stok < threshold && threshold > 0) {
                adjustment += (ruleStokRendah / 100); 
            }

            let adjustedPrice = basePrice * (1 + adjustment);

            let comps = [c1, c2, c3].filter(c => c > 0).sort((a, b) => a - b);
            let tempPrice = 0;
            
            if(comps.length > 0) {
                let medianMarket = 0;
                let mid = Math.floor(comps.length / 2);
                medianMarket = comps.length % 2 !== 0 ? comps[mid] : (comps[mid - 1] + comps[mid]) / 2;
                tempPrice = (adjustedPrice + medianMarket) / 2;
            } else {
                tempPrice = adjustedPrice;
            }

            let finalPrice = tempPrice > floorPrice ? tempPrice : floorPrice;

            let marginReal = 0;
            if (finalPrice > 0) {
                marginReal = ((finalPrice - hppReal) / finalPrice) * 100;
            }

            // 🔥 PERBAIKAN FORMAT DESIMAL INDONESIA (KOMA) & RUPIAH 🔥
            let nominalAdjustment = adjustedPrice - basePrice;
            let adjPercentFormat = (adjustment * 100).toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 1});
            let marginFormat = marginReal.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            document.getElementById('display_yield').value = finalYield.toLocaleString('id-ID') + '%';
            document.getElementById('display_floor').value = 'Rp ' + Math.round(floorPrice).toLocaleString('id-ID');
            document.getElementById('display_adj').value = 'Rp ' + Math.round(nominalAdjustment).toLocaleString('id-ID') + ' (' + adjPercentFormat + '%)';
            document.getElementById('display_final').value = 'Rp ' + Math.round(finalPrice).toLocaleString('id-ID');
            document.getElementById('display_margin').value = marginFormat + '%';
        }

        ts.on('change', function() {
            let selectEl = document.getElementById('select-produk');
            let selectedOpt = selectEl.options[selectEl.selectedIndex];
            
            document.getElementById('buffer_input').value = parseFloat(selectedOpt.getAttribute('data-buffer')) || 5;
            document.getElementById('threshold_input').value = parseFloat(selectedOpt.getAttribute('data-threshold')) || 20;

            calculatePricing();
        });
        
        const inputs = [
            'hpp_hari_ini', 'hpp_kemarin', 'margin_target', 'kompetitor_1', 'kompetitor_2', 'kompetitor_3', 'stok_fisik', 'demand',
            'buffer_input', 'threshold_input', 'adj_hpp_input', 'adj_demand_input', 'adj_stok_input',
            'tax_percent', 'yield_harian', 'shipping_cost' // 🔥 BIAYA EKSTRA DIDAFTARKAN BIAR LIVE HITUNG 🔥
        ];
        
        inputs.forEach(id => {
            let el = document.getElementById(id);
            if(el) {
                el.addEventListener('input', calculatePricing);
                el.addEventListener('change', calculatePricing); 
            }
        });

        calculatePricing();
    });
</script>
@endsection