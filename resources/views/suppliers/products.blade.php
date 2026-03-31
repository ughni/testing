@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { height: 42px !important; border-radius: 0.75rem !important; border: none !important; display: flex !important; align-items: center !important; padding-left: 2rem !important; font-weight: 600 !important; background-color: transparent !important; color: #334155 !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px !important; right: 10px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #334155 !important; padding-left: 0.5rem !important; }
    .dark .select2-container--default .select2-selection--single { color: #cbd5e1 !important; }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #cbd5e1 !important; }
    .dark .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #cbd5e1 transparent transparent transparent !important; }
    .select2-container--default .select2-dropdown { background-color: #ffffff !important; border: 1px solid #cbd5e1 !important; border-radius: 0.75rem !important; }
    .dark .select2-container--default .select2-dropdown { background-color: #1e293b !important; border: 1px solid #475569 !important; color: #e2e8f0 !important; }
    .dark .select2-container--default .select2-search--dropdown .select2-search__field { background-color: #0f172a !important; color: #e2e8f0 !important; border: 1px solid #475569 !important; border-radius: 0.5rem !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #4f46e5 !important; color: white !important; }
</style>

<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                Analisa Harga Harian
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-16">
                Pantau tren harga pokok (HPP) dan pergerakan stok dari seluruh Master Produk.
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1e293b] p-4 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 mb-8">
        <form id="filterForm" action="{{ route('suppliers.products') }}" method="GET" class="flex flex-col lg:flex-row gap-3 w-full items-center">
            
            <div class="relative w-full lg:w-1/5">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-calendar-alt text-indigo-400"></i>
                </div>
                <select name="period" class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-indigo-500 rounded-xl cursor-pointer text-slate-700 dark:text-slate-300 font-bold transition-all">
                    <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                </select>
            </div>

            <div class="relative w-full lg:w-1/5">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                    <i class="fas fa-tags text-rose-400"></i>
                </div>
                <select name="category" class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-rose-500 rounded-xl cursor-pointer text-slate-700 dark:text-slate-300 font-bold transition-all relative z-0">
                    <option value="">-- Kategori --</option>
                    <option value="Produk Beli" {{ request('category') == 'Produk Beli' ? 'selected' : '' }}>Produk Beli</option>
                    <option value="Produk Jual" {{ request('category') == 'Produk Jual' ? 'selected' : '' }}>Produk Jual</option>
                    <option value="Bahan Olahan" {{ request('category') == 'Bahan Olahan' ? 'selected' : '' }}>Bahan Olahan</option>
                    <option value="Lainnya" {{ request('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="relative w-full lg:w-1/5">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                    <i class="fas fa-building text-emerald-400"></i>
                </div>
                <select name="search_supplier" class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 rounded-xl cursor-pointer text-slate-700 dark:text-slate-300 font-bold transition-all relative z-0">
                    <option value="">-- Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->nama_supplier }}" {{ request('search_supplier') == $sup->nama_supplier ? 'selected' : '' }}>
                            {{ Str::limit($sup->nama_supplier, 20) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative w-full lg:w-1/5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                    <i class="fas fa-box text-amber-400"></i>
                </div>
                <select name="search_product" class="searchable-select w-full" style="width: 100%;">
                    <option value="">-- Semua Produk --</option>
                    @if(isset($allProductsList))
                        @foreach($allProductsList as $p)
                            <option value="{{ $p->product_name }}" {{ request('search_product') == $p->product_name ? 'selected' : '' }}>
                                {{ $p->product_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="flex items-center gap-2 w-full lg:w-1/5 mt-3 lg:mt-0">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i> Terapkan
                </button>
                
                @if(request()->hasAny(['period', 'search_supplier', 'search_product', 'category']) && (request('period') != 'all' || request('search_supplier') != '' || request('search_product') != '' || request('category') != ''))
                    <a href="{{ route('suppliers.products') }}" class="px-4 py-2.5 bg-red-50 text-red-500 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-all font-bold tooltip" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="bg-indigo-50/50 dark:bg-indigo-900/20 px-6 py-4 border-b border-indigo-100 dark:border-indigo-500/30 flex justify-between items-center">
            <h3 class="font-bold text-indigo-800 dark:text-indigo-400 flex items-center"><i class="fas fa-table mr-2"></i> Data Keseluruhan Master Produk</h3>
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50/80 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Update Terakhir</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Info Produk</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Supplier</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Info Kontak</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Harga Pokok (HPP)</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Stok & Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($products as $product)
                        @php
                            $latestInput = $product->dailyPricings->first();
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                            
                            <td class="p-5">
                                @if($latestInput)
                                    <div class="font-bold text-slate-700 dark:text-slate-300">
                                        {{ \Carbon\Carbon::parse($latestInput->date_input)->format('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                        <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($latestInput->created_at)->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada input</span>
                                @endif
                            </td>

                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-500 dark:text-indigo-400 shrink-0 border border-indigo-100 dark:border-indigo-800/50">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="flex flex-col items-start">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-base mb-1">{{ $product->product_name }}</h4>
                                        <div class="flex items-center gap-1">
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[9px] font-black rounded uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                                                {{ $product->price_type ?? 'DYNAMIC' }}
                                            </span>
                                            <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[9px] font-black rounded uppercase tracking-widest border border-rose-200 dark:border-rose-800/50">
                                                {{ $product->category ?? 'Umum' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="p-5">
                                @if($product->supplier)
                                    <div class="font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                        <i class="fas fa-building text-emerald-500 text-xs"></i> {{ $product->supplier->nama_supplier }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-red-200 dark:border-red-800/50">
                                        <i class="fas fa-exclamation-triangle"></i> Belum Dipetakan
                                    </div>
                                @endif
                            </td>

                            <td class="p-5">
                                @if($product->supplier)
                                    <div class="flex flex-col gap-1.5 w-fit min-w-[200px]">
                                        @if($product->supplier->kontak_person)
                                            <div class="flex items-center gap-2 text-xs">
                                                <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-phone-alt text-emerald-600 dark:text-emerald-400 text-[9px]"></i>
                                                </div>
                                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $product->supplier->kontak_person }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($product->supplier->email)
                                            <div class="flex items-center gap-2 text-xs">
                                                <div class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-[9px]"></i>
                                                </div>
                                                <span class="text-slate-600 dark:text-slate-400 truncate max-w-[150px]">{{ $product->supplier->email }}</span>
                                            </div>
                                        @endif

                                        @if($product->supplier->alamat)
                                            <div class="flex items-start gap-2 text-xs mt-0.5">
                                                <div class="w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0 mt-0.5">
                                                    <i class="fas fa-map-marker-alt text-amber-600 dark:text-amber-400 text-[9px]"></i>
                                                </div>
                                                <span class="text-slate-500 dark:text-slate-400 leading-tight max-w-[220px] whitespace-normal">{{ $product->supplier->alamat }}</span>
                                            </div>
                                        @endif
                                        
                                        @if(!$product->supplier->kontak_person && !$product->supplier->email && !$product->supplier->alamat)
                                            <span class="text-xs text-slate-400 italic">Tidak ada info kontak</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <td class="p-5 text-right">
                                @if($latestInput)
                                    <div class="font-black text-slate-800 dark:text-white text-base tracking-tight bg-slate-50 dark:bg-slate-900/50 inline-block px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                                        Rp {{ number_format((float)$latestInput->hpp, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Rp 0</span>
                                @endif
                            </td>

                            <td class="p-5 text-center">
                                @if($latestInput)
                                    @if($latestInput->stock < 20)
                                        <span class="inline-flex items-center justify-center px-4 py-2 rounded-xl border-2 border-red-500 text-red-500 dark:border-red-400 dark:text-red-400 text-sm font-black shadow-sm bg-transparent">
                                            {{ $latestInput->stock }} {{ $product->unit ?? 'Pcs' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-4 py-2 rounded-xl border-2 border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400 text-sm font-black shadow-sm bg-transparent">
                                            {{ $latestInput->stock }} {{ $product->unit ?? 'Pcs' }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center bg-slate-50/30 dark:bg-slate-900/10">
                                <i class="fas fa-search text-5xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
                                <h3 class="text-lg font-black text-slate-600 dark:text-slate-300 mb-1">Data Tidak Ditemukan</h3>
                                <p class="text-slate-500 text-sm">Coba ubah filter periode, kategori, supplier, atau produk di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/30">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Select2 buat Produk
        $('.searchable-select').select2({
            placeholder: "-- Semua Produk --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection