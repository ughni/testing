@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Penyesuaian agar Tom Select mirip dengan desain Tailwind lu */
    .ts-control { padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background-color: #f8fafc; font-weight: 500; }
    .dark .ts-control { background-color: rgba(15, 23, 42, 0.5); border-color: #334155; color: #f1f5f9; }
    .dark .ts-dropdown { background-color: #1e293b; border-color: #334155; color: #f1f5f9; }
    .dark .ts-dropdown .option:hover, .dark .ts-dropdown .active { background-color: #334155; color: #fff; }
    .ts-wrapper.single .ts-control:after { border-color: #94a3b8 transparent transparent transparent; }
</style>
@php
    $isEdit = isset($history);
@endphp

<div class="max-w-4xl mx-auto pb-10">
    
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

    @if ($errors->any())
        <div class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-2xl shadow-sm relative pr-12">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2 text-lg"></i>
                <h3 class="text-sm font-bold text-red-800 dark:text-red-300 uppercase tracking-wider">Kalkulasi Gagal Diproses!</h3>
            </div>
            <ul class="list-disc list-inside text-sm font-medium text-red-700 dark:text-red-400 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        @php
            $bgClass = 'bg-blue-50 dark:bg-blue-900/20 border-blue-500 text-blue-800 dark:text-blue-300';
            $iconBtn = 'text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-800';
            $icon = 'ℹ️';
            
            if (str_contains(session('success'), 'Naikkan Harga!')) {
                $bgClass = 'bg-red-50 dark:bg-red-900/20 border-red-500 text-red-800 dark:text-red-300';
                $iconBtn = 'text-red-500 hover:bg-red-100 dark:hover:bg-red-800';
                $icon = '🔴';
            } elseif (str_contains(session('success'), 'Sesuaikan Harga')) {
                $bgClass = 'bg-amber-50 dark:bg-amber-900/20 border-amber-500 text-amber-800 dark:text-amber-300';
                $iconBtn = 'text-amber-500 hover:bg-amber-100 dark:hover:bg-amber-800';
                $icon = '🟡';
            } elseif (str_contains(session('success'), 'Pertahankan Harga')) {
                $bgClass = 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500 text-emerald-800 dark:text-emerald-300';
                $iconBtn = 'text-emerald-500 hover:bg-emerald-100 dark:hover:bg-emerald-800';
                $icon = '🟢';
            }
        @endphp
        
        <div class="mb-8 flex items-start p-4 border-l-4 rounded-r-2xl shadow-sm transition-all relative pr-12 {{ $bgClass }}">
            <div class="text-xl mr-3 mt-0.5">{{ $icon }}</div>
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider opacity-80 mb-1">Hasil Kalkulasi Sistem</h3>
                <p class="font-medium text-base sm:text-lg">{{ session('success') }}</p>
            </div>
        </div>
    @endif

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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Produk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="select-produk" name="product_id" class="w-full text-slate-800 dark:text-slate-200 font-medium" required>
                                <option value="">-- Ketik untuk mencari produk... --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', $isEdit ? $history->product_id : '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->product_name }} (Tipe: {{ strtoupper($p->price_type) }})
                                    </option>
                                @endforeach
                            </select>
                            </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Input <span class="text-red-500">*</span></label>
                        <input type="date" name="date_input" max="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-colors font-medium" value="{{ old('date_input', $isEdit ? \Carbon\Carbon::parse($history->date_input)->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-xs font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-tags mr-2"></i> Data Harga & Margin Target
                    </h6>
                    <div class="p-5 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">HPP Hari Ini (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold">Rp</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" name="hpp" value="{{ old('hpp', $isEdit ? (float)$history->hpp : '') }}" class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" placeholder="20000" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">HPP Kemarin (Rp)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold">Rp</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" name="hpp_prev" value="{{ old('hpp_prev', ($isEdit && $history->hpp_prev) ? (float)$history->hpp_prev : '') }}" class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" placeholder="Opsional">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target Margin (%)</label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0" name="manual_margin" value="{{ old('manual_margin', ($isEdit && $history->margin_percent) ? ($history->margin_percent * 100) : '') }}" class="w-full px-4 py-3 bg-amber-50 dark:bg-amber-900/10 border border-amber-300 dark:border-amber-700/50 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" placeholder="Otomatis (Kosongi)">
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold">%</span>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1 leading-tight">Isi jika ingin mematok profit sendiri. Kosongi agar sistem menghitung otomatis.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-xs font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-store-alt mr-2"></i> Harga Pasar / Kompetitor
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 1</label>
                            <input type="number" step="0.01" min="0" name="c1" value="{{ old('c1', ($isEdit && $history->c1) ? (float)$history->c1 : '') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" placeholder="Rp (Opsional)">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 2</label>
                            <input type="number" step="0.01" min="0" name="c2" value="{{ old('c2', ($isEdit && $history->c2) ? (float)$history->c2 : '') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" placeholder="Rp (Opsional)">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 3</label>
                            <input type="number" step="0.01" min="0" name="c3" value="{{ old('c3', ($isEdit && $history->c3) ? (float)$history->c3 : '') }}"class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" placeholder="Rp (Opsional)">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h6 class="text-xs font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2"></i> Kondisi Lapangan
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Sisa Stok Fisik <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" min="0" name="stock" value="{{ old('stock', $isEdit ? $history->stock : '') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 transition-colors placeholder-slate-400" placeholder="Contoh: 50" required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-slate-400 text-sm">Pcs</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Demand (Permintaan) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="demand" class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 dark:text-slate-200 appearance-none transition-colors font-medium" required>
                                    <option value="normal" {{ old('demand', $isEdit ? $history->demand : '') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="tinggi" {{ old('demand', $isEdit ? $history->demand : '') == 'tinggi' ? 'selected' : '' }}>Tinggi (Pasar Ramai)</option>
                                    <option value="rendah" {{ old('demand', $isEdit ? $history->demand : '') == 'rendah' ? 'selected' : '' }}>Rendah (Pasar Sepi)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="w-full {{ $isEdit ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/30 focus:ring-indigo-500/50' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/30 focus:ring-blue-500/50' }} text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all hover:-translate-y-1 focus:ring-4 flex justify-center items-center gap-2 text-lg">
                        <i class="fas {{ $isEdit ? 'fa-save' : 'fa-magic' }}"></i> 
                        {{ $isEdit ? 'Update & Simpan Perubahan' : 'Hitung & Simpan Harga Rekomendasi' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Nyalain si Tom Select di tag ID "select-produk"
        new TomSelect("#select-produk",{
            create: false,
            maxOptions: null, // <--- 🔥 INI OBATNYA BIAR SEMUA PRODUK MUNCUL 🔥
            sortField: {
                field: "text",
                direction: "asc" // Otomatis ngurutin produk dari A-Z
            },
            placeholder: "-- Ketik nama produk... --"
        });
    });
</script>
@endsection