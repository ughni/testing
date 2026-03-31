@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-{{ $pageData['color'] ?? 'blue' }}-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-{{ $pageData['color'] ?? 'blue' }}-600/20">
                    <i class="fas {{ $pageData['icon'] ?? 'fa-tags' }} text-lg"></i>
                </div>
                {{ $pageData['title'] ?? 'Tipe Harga' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                {{ $pageData['desc'] ?? 'Daftar produk berdasarkan aturan harga yang diterapkan.' }}
            </p>
        </div>
        <div class="text-left md:text-right bg-white dark:bg-slate-800 px-6 py-3 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <span class="text-3xl font-black text-slate-800 dark:text-white">{{ number_format($products->total(), 0, ',', '.') }}</span>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Total Item</p>
        </div>
    </div>

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('pricing.type', $type) }}" method="GET" class="w-full md:w-1/2 relative">
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Produk atau Kategori..." 
                class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-{{ $pageData['color'] ?? 'blue' }}-500 focus:border-{{ $pageData['color'] ?? 'blue' }}-500 text-slate-700 dark:text-slate-300 shadow-sm transition-all placeholder-slate-400 font-medium">
            @if(request('search'))
                <a href="{{ route('pricing.type', $type) }}" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 hover:text-red-700 font-bold text-xs">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Kode / SKU</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-right">Aturan Harga / Margin</th>
                        <th class="px-6 py-4 text-center">Status Aturan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-sm">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                {{ $product->sku ?? 'SKU-'.$product->id }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $product->product_name }}</div>
                            </td>

                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 text-xs font-medium">
                                {{ $product->category ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-right font-medium text-slate-700 dark:text-slate-300">
                                @if($type == 'HET')
                                    <span class="text-xs text-slate-400 block mb-1">Max HET:</span>
                                    <span class="font-bold">Rp {{ number_format($product->het_price ?? 0, 0, ',', '.') }}</span>
                                @elseif($type == 'consignment')
                                    <span class="text-xs text-slate-400 block mb-1">Margin / Fixed:</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($product->consignment_margin ?? 0, 0) }}%</span> / Rp {{ number_format($product->selling_price_fixed ?? 0, 0, ',', '.') }}
                                @else
                                    <span class="text-xs text-slate-500 italic flex items-center justify-end gap-1.5">
                                        <i class="fas fa-chart-line text-blue-500"></i> Ikut HPP Harian
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-{{ $pageData['color'] ?? 'blue' }}-50 text-{{ $pageData['color'] ?? 'blue' }}-700 dark:bg-{{ $pageData['color'] ?? 'blue' }}-900/20 dark:text-{{ $pageData['color'] ?? 'blue' }}-400 text-[10px] font-extrabold rounded-md uppercase tracking-wider border border-{{ $pageData['color'] ?? 'blue' }}-200 dark:border-{{ $pageData['color'] ?? 'blue' }}-800/50 shadow-sm">
                                    {{ $type }} AKTIF
                                </span>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-500">
                                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 shadow-inner">
                                    <i class="fas fa-box-open text-3xl"></i>
                                </div>
                                <h3 class="font-black text-lg text-slate-700 dark:text-slate-300 mb-1">Tidak Ada Data</h3>
                                <p class="text-sm">Belum ada produk yang diset menggunakan tipe harga <b>{{ $pageData['title'] ?? $type }}</b> atau pencarian tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($products) && $products->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#1e293b]">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection