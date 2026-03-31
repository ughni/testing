@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto pb-10">
    
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20 relative">
                <i class="fas fa-subscript text-lg"></i>
            </div>
            Formula Engine Settings
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 ml-13">
            Atur variabel utama yang menjadi "Otak" perhitungan harga dinamis (Dynamic Pricing) dan produksi di seluruh sistem.
        </p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/50 rounded-xl p-4 flex gap-4 items-start">
        <div class="text-emerald-500 mt-0.5"><i class="fas fa-check-circle text-lg"></i></div>
        <div>
            <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-400 mb-1">Berhasil Disimpan!</h4>
            <p class="text-sm text-emerald-700/80 dark:text-emerald-500/80">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        
        <form action="{{ route('formula.update') }}" method="POST">
            @csrf
            
            <div class="p-6 md:p-8 space-y-8">
                
                <div class="flex flex-col md:flex-row gap-6 items-start pb-8 border-b border-slate-100 dark:border-slate-800/50">
                    <div class="md:w-1/3">
                        <label class="font-bold text-slate-800 dark:text-white text-base">Buffer Percent <span class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Batas aman persentase yang ditambahkan ke HPP untuk membentuk <b>Floor Price (Harga Minimal)</b>. Default standar adalah 5%.
                        </p>
                    </div>
                    <div class="md:w-2/3 w-full">
                        <div class="relative flex items-center max-w-xs">
                            <input type="number" step="0.1" name="buffer_percent" required 
                                value="{{ old('buffer_percent', $setting->buffer_percent * 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-bold text-lg" 
                                placeholder="5">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                        @error('buffer_percent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 items-start pb-8 border-b border-slate-100 dark:border-slate-800/50">
                    <div class="md:w-1/3">
                        <label class="font-bold text-slate-800 dark:text-white text-base">Markup Base <span class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Target keuntungan/markup dasar yang diinginkan dari HPP. Nilai ini membentuk <b>Base Price</b> sebelum perhitungan harga dinamis. Default adalah 20%.
                        </p>
                    </div>
                    <div class="md:w-2/3 w-full">
                        <div class="relative flex items-center max-w-xs">
                            <input type="number" step="0.1" name="markup_base" required 
                                value="{{ old('markup_base', $setting->markup_base * 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-bold text-lg" 
                                placeholder="20">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                        @error('markup_base') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 items-start pb-8 border-b border-slate-100 dark:border-slate-800/50">
                    <div class="md:w-1/3">
                        <label class="font-bold text-slate-800 dark:text-white text-base">Threshold Stock <span class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Batas jumlah stok produk. Jika sisa stok produk di gudang berada di bawah angka ini, sistem otomatis menaikkan harga sebesar 2%.
                        </p>
                    </div>
                    <div class="md:w-2/3 w-full">
                        <div class="relative flex items-center max-w-xs">
                            <input type="number" name="threshold_stock" required 
                                value="{{ old('threshold_stock', $setting->threshold_stock) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-bold text-lg" 
                                placeholder="20">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold text-sm">Pcs / Unit</div>
                        </div>
                        @error('threshold_stock') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="md:w-1/3">
                        <label class="font-bold text-slate-800 dark:text-white text-base">Yield (Rendemen) <span class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Persentase hasil jadi produk setelah melewati proses produksi/penyusutan. Default adalah 100% (tidak ada penyusutan bahan baku).
                        </p>
                    </div>
                    <div class="md:w-2/3 w-full">
                        <div class="relative flex items-center max-w-xs">
                            <input type="number" step="0.1" name="yield_percent" required 
                                value="{{ old('yield_percent', isset($setting->yield_percent) ? ($setting->yield_percent * 100) : 100) }}"
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-bold text-lg" 
                                placeholder="100">
                            <div class="absolute right-0 pr-4 text-slate-400 font-bold">%</div>
                        </div>
                        @error('yield_percent') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <div class="px-6 py-5 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30 flex items-center justify-between">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-500"></i>
                    Perubahan akan langsung dicatat di Audit Trail.
                </div>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-600/30 flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Formula
                </button>
            </div>

        </form>

    </div>
</div>
@endsection