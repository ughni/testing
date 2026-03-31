@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto pb-10">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/20 relative">
                <i class="fas fa-bolt text-lg"></i>
            </div>
            Auto Adjustment Rules
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 ml-13">
            Atur persentase penyesuaian harga otomatis saat terjadi lonjakan HPP dari supplier, perubahan permintaan (Demand), atau stok menipis.
        </p>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/50 rounded-xl p-4 flex gap-4 items-start">
        <div class="text-emerald-500 mt-0.5"><i class="fas fa-check-circle text-lg"></i></div>
        <div>
            <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-400 mb-1">Aturan Berhasil Disimpan!</h4>
            <p class="text-sm text-emerald-700/80 dark:text-emerald-500/80">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <form action="{{ route('rules.update') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- KARTU 1: Aturan HPP Supplier -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-800/20 flex items-center gap-3">
                    <i class="fas fa-truck-loading text-slate-400"></i>
                    <h2 class="font-bold text-slate-800 dark:text-white">Lonjakan HPP Supplier</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 block">Batas HPP Dianggap Naik (Threshold)</label>
                        <div class="relative flex items-center">
                            <input type="number" step="0.1" name="hpp_increase_threshold" required 
                                value="{{ old('hpp_increase_threshold', $rule->hpp_increase_threshold * 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="3">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">Jika HPP hari ini naik lebih dari persentase ini dibanding kemarin, maka sistem akan bereaksi.</p>
                    </div>
                    <div>
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 block">Aksi: Tambahkan Harga Sebesar</label>
                        <div class="relative flex items-center">
                            <input type="number" step="0.1" name="hpp_adjustment" required 
                                value="{{ old('hpp_adjustment', $rule->hpp_adjustment * 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="3">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KARTU 2: Aturan Demand & Stok -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-800/20 flex items-center gap-3">
                    <i class="fas fa-fire text-slate-400"></i>
                    <h2 class="font-bold text-slate-800 dark:text-white">Kondisi Pasar & Stok Gudang</h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Demand Tinggi -->
                    <div>
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 block">Jika Demand "Tinggi" (Barang Viral)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-0 pl-4 text-emerald-500 font-bold">+</span>
                            <input type="number" step="0.1" name="demand_high_adjustment" required 
                                value="{{ old('demand_high_adjustment', $rule->demand_high_adjustment * 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg pl-8 pr-8 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                    </div>
                    
                    <!-- Demand Rendah -->
                    <div>
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 block">Jika Demand "Rendah" (Barang Sepi)</label>
                        <div class="relative flex items-center">
                            <input type="number" step="0.1" max="0" name="demand_low_adjustment" required 
                                value="{{ old('demand_low_adjustment', $rule->demand_low_adjustment * 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="-3">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                        <p class="text-xs text-red-500/80 mt-1.5">*Gunakan angka minus (contoh: -3) karena ini adalah diskon penurunan harga.</p>
                    </div>

                    <!-- Stok Rendah -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 block">Jika Stok Menipis (< Threshold)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-0 pl-4 text-emerald-500 font-bold">+</span>
                            <input type="number" step="0.1" name="stock_low_adjustment" required 
                                value="{{ old('stock_low_adjustment', $rule->stock_low_adjustment * 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg pl-8 pr-8 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-slate-800 dark:bg-indigo-600 hover:bg-slate-900 dark:hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-lg flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan Aturan Otomatis
            </button>
        </div>

    </form>
</div>
@endsection