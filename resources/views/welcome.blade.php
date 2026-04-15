@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body, table, th, td, h1, h3, p, a, span { font-family: 'Inter', system-ui, -apple-system, sans-serif !important; }
</style>
@php
    // 🔥 HACKING JENIUS: Ngitung langsung dari Database, bodo amat sama Controller! 🔥
    $totalRed = \App\Models\DailyPricing::where('status_margin', 'RED')->count();
    $totalYellow = \App\Models\DailyPricing::where('status_margin', 'YELLOW')->count();
    $totalSemua = \App\Models\DailyPricing::count();
    $totalGreen = $totalSemua - $totalRed - $totalYellow; // Sisanya pasti hijau (Aman)
@endphp

<div class="w-full max-w-[100%] px-1 sm:px-2 pb-10">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                Dashboard Preview Menyeluruh
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 lg:ml-13">
                Monitor fluktuasi harga pasar dan rekomendasi sistem secara real-time.
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('pricing.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:-translate-y-0.5 transform">
                <i class="fas fa-calculator mr-2"></i> Hitung Harga Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-r-2xl shadow-sm flex items-center">
            <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        @php
            $aktifRed = (isset($statusFilter) && $statusFilter === 'red') ? 'scale-105 ring-4 ring-red-500 ring-opacity-50' : '';
            $aktifYellow = (isset($statusFilter) && $statusFilter === 'yellow') ? 'scale-105 ring-4 ring-amber-500 ring-opacity-50' : '';
            $aktifGreen = (isset($statusFilter) && $statusFilter === 'green') ? 'scale-105 ring-4 ring-emerald-500 ring-opacity-50' : '';
        @endphp

        <a href="{{ route('dashboard', ['status' => (isset($statusFilter) && $statusFilter === 'red' ? 'all' : 'red')]) }}" class="bg-white dark:bg-[#1e293b] rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 border-l-4 border-l-red-500 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg {{ $aktifRed }}">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Darurat (Merah)</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ $totalRed }} <span class="text-sm font-semibold text-slate-500 uppercase">Item</span>
                </h3>
                <p class="text-sm text-red-500 font-bold mt-1"><i class="fas fa-arrow-up mr-1"></i> Perlu Naikkan Harga</p>
            </div>
            <div class="w-12 h-12 bg-red-50 dark:bg-red-900/30 rounded-full flex items-center justify-center text-red-500 text-xl shadow-inner">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </a>

        <a href="{{ route('dashboard', ['status' => (isset($statusFilter) && $statusFilter === 'yellow' ? 'all' : 'yellow')]) }}" class="bg-white dark:bg-[#1e293b] rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 border-l-4 border-l-amber-500 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg {{ $aktifYellow }}">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Waspada (Kuning)</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ $totalYellow }} <span class="text-sm font-semibold text-slate-500 uppercase">Item</span>
                </h3>
                <p class="text-sm text-amber-500 font-bold mt-1"><i class="fas fa-balance-scale mr-1"></i> Perlu Penyesuaian</p>
            </div>
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-500 text-xl shadow-inner">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </a>

        <a href="{{ route('dashboard', ['status' => (isset($statusFilter) && $statusFilter === 'green' ? 'all' : 'green')]) }}" class="bg-white dark:bg-[#1e293b] rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 border-l-4 border-l-emerald-500 flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg {{ $aktifGreen }}">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Aman (Hijau)</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ $totalGreen }} <span class="text-sm font-semibold text-slate-500 uppercase">Item</span>
                </h3>
                <p class="text-sm text-emerald-500 font-bold mt-1"><i class="fas fa-check-double mr-1"></i> Harga Stabil Target</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-full flex items-center justify-center text-emerald-500 text-xl shadow-inner">
                <i class="fas fa-shield-alt"></i>
            </div>
        </a>
    </div>

    @php
        $uniqueCategories = $dailyPricings->pluck('product.category')->filter()->unique();
    @endphp

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-300 flex flex-col">
        
        <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row justify-between items-center gap-4">
            <h6 class="text-sm font-bold text-slate-700 dark:text-slate-200 flex items-center whitespace-nowrap">
                <i class="fas fa-list-ul mr-2 text-indigo-500"></i> Rekapitulasi Harga & Indikator
            </h6>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="relative w-full sm:w-48">
                    <select id="categoryFilter" class="block w-full pl-4 pr-10 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl leading-5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition-colors cursor-pointer appearance-none shadow-sm">
                        <option value="all">Semua Kategori</option>
                        @foreach($uniqueCategories as $cat)
                            <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" id="liveSearch" placeholder="Cari Nama Produk / Tgl..." class="block w-full pl-10 pr-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl leading-5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition-colors shadow-sm">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar flex-1">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-white dark:bg-[#1e293b] text-slate-600 dark:text-slate-400 text-xs uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-center">Tanggal</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-center">Demand</th>
                        <th class="px-6 py-4 text-right">HPP (Rp)</th>
                        <th class="px-6 py-4 text-right text-indigo-600 dark:text-indigo-400">Harga Jual</th>
                        <th class="px-6 py-4 text-center">Trend</th>
                        <th class="px-6 py-4 text-center">Margin</th>
                        <th class="px-6 py-4 text-center">Status Aksi</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($dailyPricings as $dp)
                        @php
                            $priceDiff = $dp->hpp - $dp->hpp_prev;
                            $trendIcon = '<i class="fas fa-minus text-slate-400"></i>';
                            $trendClass = 'text-slate-500';
                            $trendText = 'Tetap';

                            if ($priceDiff > 0) {
                                $trendIcon = '<i class="fas fa-arrow-trend-up text-red-500"></i>';
                                $trendClass = 'text-red-600';
                                $trendText = 'Naik';
                            } elseif ($priceDiff < 0) {
                                $trendIcon = '<i class="fas fa-arrow-trend-down text-emerald-500"></i>';
                                $trendClass = 'text-emerald-600';
                                $trendText = 'Turun';
                            }
                            
                            $statusMarginDB = strtoupper(trim($dp->status_margin));
                            $statusFilter = 'green'; 
                            if($statusMarginDB === 'RED' || $statusMarginDB === 'RUGI') { $statusFilter = 'red'; }
                            if($statusMarginDB === 'YELLOW') { $statusFilter = 'yellow'; }
                        @endphp
                        
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group searchable-row" 
                            data-date="{{ \Carbon\Carbon::parse($dp->date_input)->format('d M Y') }}" 
                            data-name="{{ strtolower($dp->product->product_name ?? '') }}"
                            data-category="{{ strtolower($dp->product->category ?? '') }}"
                            data-status="{{ $statusFilter }}">
                            
                            <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">
                                <div class="text-sm font-bold text-slate-800 dark:text-slate-300">{{ \Carbon\Carbon::parse($dp->date_input)->format('d M') }}</div>
                                <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($dp->date_input)->format('Y') }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $dp->product->product_name ?? 'Produk Dihapus' }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if($dp->product && $dp->product->category)
                                    <span class="inline-block px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-md border border-slate-200 dark:border-slate-700">
                                        {{ $dp->product->category }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-sm">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($dp->demand === 'tinggi')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                        <i class="fas fa-fire mr-1"></i> TINGGI
                                    </span>
                                @elseif($dp->demand === 'rendah')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        <i class="fas fa-snowflake mr-1"></i> RENDAH
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        NORMAL
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right text-sm font-semibold text-slate-700 dark:text-slate-300">
                                {{ number_format($dp->hpp, 0, ',', '.') }}
                            </td>
                            
                            <td class="px-6 py-4 text-right text-sm font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50/30 dark:bg-indigo-900/10 transition-colors">
                                {{ number_format($dp->final_price, 0, ',', '.') }}
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm">{!! $trendIcon !!}</span>
                                    <span class="text-xs font-semibold {{ $trendClass }} mt-1">{{ $trendText }}</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center text-sm font-bold text-slate-700 dark:text-slate-200">
                                {{ number_format((float)$dp->margin_percent > 1 ? $dp->margin_percent : $dp->margin_percent * 100, 1) }}%
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($statusFilter === 'red')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-red-50 text-red-700 border border-red-200 shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Naikkan
                                    </span>
                                @elseif($statusFilter === 'yellow')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Sesuaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aman
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pricing.edit', $dp->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-indigo-100" title="Edit Data">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('pricing.destroy', $dp->id) }}" method="POST" onsubmit="return confirm('Hapus data histori harga ini permanen?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100" title="Hapus Data">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-folder-open text-2xl text-slate-400"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Belum Ada Riwayat Harga</h3>
                                    <p class="text-sm text-slate-500 mt-1">Lakukan Kalkulasi Input Harian pertama Anda untuk memunculkan data.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    
                    <tr id="jsEmptyState" style="display: none;">
                        <td colspan="10" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-search text-2xl text-slate-400"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 dark:text-white text-lg">Data Tidak Ditemukan</h3>
                                <p class="text-sm text-slate-500 mt-1">Tidak ada produk yang cocok dengan pencarian atau filter status Anda.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        @if(method_exists($dailyPricings, 'hasPages') && $dailyPricings->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30">
                {{ $dailyPricings->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const rows = document.querySelectorAll('.searchable-row');
        const jsEmptyState = document.getElementById('jsEmptyState');

        function filterTable() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedCategory = categoryFilter ? categoryFilter.value.toLowerCase() : 'all';
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const date = row.getAttribute('data-date').toLowerCase();
                const category = row.getAttribute('data-category');

                const matchesText = name.includes(query) || date.includes(query);
                const matchesCategory = (selectedCategory === 'all' || category === selectedCategory);

                if (matchesText && matchesCategory) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (rows.length > 0 && jsEmptyState) {
                jsEmptyState.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        if(searchInput) {
            searchInput.addEventListener('keyup', filterTable);
        }
        if(categoryFilter) {
            categoryFilter.addEventListener('change', filterTable);
        }
    });
</script>
@endsection