@extends('layouts.app') 

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Penyesuaian agar Tom Select mirip dengan desain Tailwind lu */
    .ts-control { padding: 0.625rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; background-color: #f8fafc; font-weight: 500; }
    .dark .ts-control { background-color: #0f172a; border-color: #334155; color: #f1f5f9; }
    .dark .ts-dropdown { background-color: #1e293b; border-color: #334155; color: #f1f5f9; }
    .dark .ts-dropdown .option:hover, .dark .ts-dropdown .active { background-color: #334155; color: #fff; }
    .ts-wrapper.single .ts-control:after { border-color: #94a3b8 transparent transparent transparent; }
</style>
<div class="max-w-4xl mx-auto pb-10">
    
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                    <i class="fas fa-calculator text-lg"></i>
                </div>
                Pricing Engine Calculator
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 ml-14">
                Input data fluktuasi pasar harian untuk mendapatkan rekomendasi harga otomatis.
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        
        <div class="bg-black px-8 py-4">
            <h5 class="font-bold text-white flex items-center"><i class="fas fa-keyboard mr-2 text-blue-400"></i> Form Fluktuasi Pasar</h5>
        </div>
        
        <div class="p-8">
          <form action="{{ route('pricing.calculate') }}" method="POST">
                @csrf 
                <input type="hidden" name="user_id" value="{{ auth()->id() ?? 1 }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Produk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="select-produk" name="product_id" required class="w-full text-slate-800 dark:text-slate-200 font-medium">
                                <option value="" disabled selected>-- Ketik untuk mencari produk... --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        @error('product_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Input <span class="text-red-500">*</span></label>
                       <input type="date" name="date_input" value="{{ old('date_input', date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 font-medium border-slate-200 dark:border-slate-700">
                        @error('input_date') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center"><i class="fas fa-tag mr-2"></i> DATA HARGA & MARGIN TARGET</h6>
                    
                    <div class="bg-slate-50/50 dark:bg-slate-800/30 p-6 rounded-xl border border-slate-100 dark:border-slate-700/50 grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">HPP Hari Ini (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-bold">Rp</span>
                                <input type="number" name="hpp" value="{{ old('hpp') }}" min="0" required placeholder="20000" class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 font-bold text-slate-800 dark:text-white">
                            </div>
                            @error('hpp') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">HPP Kemarin (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-bold">Rp</span>
                                <input type="number" name="hpp_prev" value="{{ old('hpp_prev') }}" min="0" placeholder="Opsional" class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target Margin (%)</label>
                            <div class="relative">
                                <input type="number" name="manual_margin" step="0.01" placeholder="Otomatis (Kosongi)" class="w-full pl-4 pr-10 py-2.5 bg-amber-50/30 dark:bg-slate-800 border border-amber-300 dark:border-amber-700/50 rounded-lg focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-white transition-all">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-amber-500 font-black">%</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1.5 leading-tight">Kosongi agar sistem menghitung otomatis.</p>
                        </div>

                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-4 flex items-center"><i class="fas fa-file-invoice-dollar mr-2"></i> BIAYA EKSTRA (BIAYA SILUMAN)</h6>
                    
                    <div class="bg-rose-50/30 dark:bg-slate-800/30 p-6 rounded-xl border border-rose-100 dark:border-slate-700/50 grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pajak / PPN (%)</label>
                            <div class="relative">
                                <input type="number" name="tax_percent" value="{{ old('tax_percent', 0) }}" min="0" step="0.1" class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-white">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 font-bold">%</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1.5 leading-tight">Contoh: 11 (untuk PPN 11%)</p>
                        </div>

                        <div>
    <label class="block text-sm font-bold text-slate-700 mb-2">Yield Harian (%)</label>
    <div class="relative">
        <input type="number" step="0.1" name="yield_harian" class="w-full px-4 py-2 border rounded-lg" placeholder="Cth: 80 (Kosongi jika ikut Master)">
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">%</div>
    </div>
    <p class="text-[10px] text-slate-500 mt-1">Isi jika yield hari ini beda dengan Master Produk.</p>
</div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ongkos Kirim (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-bold">Rp</span>
                                <input type="number" name="shipping_cost" value="{{ old('shipping_cost', 0) }}" min="0" class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-rose-500 text-slate-800 dark:text-white">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1.5 leading-tight">Estimasi per Pcs barang.</p>
                        </div>

                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center"><i class="fas fa-store-alt mr-2"></i> HARGA PASAR / KOMPETITOR</h6>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 1</label>
                            <div class="relative">
                                <input type="number" name="c1" value="{{ old('c1') }}" min="0" placeholder="Rp (Opsional)" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 2</label>
                            <div class="relative">
                                <input type="number" name="c2" value="{{ old('c2') }}" min="0" placeholder="Rp (Opsional)" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kompetitor 3</label>
                            <div class="relative">
                                <input type="number" name="c3" value="{{ old('c3') }}" min="0" placeholder="Rp (Opsional)" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h6 class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4 flex items-center"><i class="fas fa-chart-line mr-2"></i> KONDISI LAPANGAN</h6>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Sisa Stok Fisik <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="stock" value="{{ old('stock') }}" min="0" required placeholder="Contoh: 50" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 text-xs">Pcs</span>
                            </div>
                            @error('stock') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Demand (Permintaan) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="demand" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 font-medium text-slate-800 dark:text-white appearance-none">
                                    <option value="normal" {{ old('demand') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="tinggi" {{ old('demand') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="rendah" {{ old('demand') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </div>
                            </div>
                            @error('demand') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-lg shadow-md shadow-blue-600/20 transition-all hover:-translate-y-0.5 flex items-center justify-center">
                        <i class="fas fa-pen mr-2"></i> Hitung & Simpan Harga Rekomendasi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect("#select-produk",{
            create: false,
            maxOptions: null, 
            sortField: {
                field: "text",
                direction: "asc" 
            },
            placeholder: "-- Ketik nama produk... --"
        });
    });
</script>
@endsection