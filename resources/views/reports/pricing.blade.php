@extends('layouts.app')

@section('content')
    <style>
        /* Styling Scrollbar biar elegan dan nggak kaku */
        .custom-scrollbar::-webkit-scrollbar {
            height: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Efek bayangan buat Sticky Column biar keliatan misah pas di-scroll */
        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 20;
            box-shadow: 4px 0 10px -2px rgba(0, 0, 0, 0.1);
        }

        .dark .sticky-col {
            box-shadow: 4px 0 10px -2px rgba(0, 0, 0, 0.5);
        }
    </style>

    <div class="max-w-[100%] mx-auto pb-10 px-4 sm:px-6 lg:px-8">

        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                    Laporan Pricing Engine & Data Pasar
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-16">
                    Rekapitulasi otomatis pergerakan HPP, stok, harga kompetitor, dan rekomendasi harga jual harian.
                </p>
            </div>

            <div class="flex flex-wrap justify-end gap-3">

                <a href="{{ route('process_plan.index') }}"
                    class="px-5 py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white font-bold rounded-xl shadow-sm transition-all flex items-center text-sm border border-blue-200">
                    <i class="fas fa-clipboard-check mr-2"></i> Ke Rencana Pembelian
                </a>

                <button onclick="toggleFilter()"
                    class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-xl shadow-sm transition-all flex items-center text-sm">
                    <i class="fas fa-filter mr-2 text-indigo-500"></i> Filter Data
                </button>

                <a href="{{ route('laporan.pricing', array_merge(request()->all(), ['export' => 'excel'])) }}"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center text-sm">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>

        <div id="filterBox"
            class="{{ request()->hasAny(['search', 'start_date', 'status']) ? 'block' : 'hidden' }} bg-white dark:bg-[#1e293b] p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 mb-8 transition-all">
            <form action="{{ route('laporan.pricing') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Cari Nama Produk</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama..."
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Status Marjin</label>
                    <select name="status"
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm dark:text-white">
                        <option value="">-- Semua Status --</option>
                        <option value="Aman" {{ request('status') == 'Aman' ? 'selected' : '' }}>Aman (Untung)</option>
                        <option value="Rugi" {{ request('status') == 'Rugi' ? 'selected' : '' }}>Rugi (Minus)</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex justify-end gap-2 mt-2">
                    <a href="{{ route('laporan.pricing') }}"
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-lg text-sm hover:bg-slate-200">Reset</a>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg text-sm shadow-sm hover:bg-indigo-700"><i
                            class="fas fa-search mr-1"></i> Terapkan</button>
                </div>
            </form>
        </div>

        <div
            class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">

            <div class="overflow-x-auto custom-scrollbar pb-3">
                <table class="w-full text-left whitespace-nowrap text-sm">
                    <thead>
                        <tr
                            class="bg-slate-100 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-extrabold text-[10px] uppercase tracking-wider text-center">
                            <th colspan="4"
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 sticky-col bg-slate-100 dark:bg-slate-900 z-30">
                                Informasi Produk & Gudang</th>
                            <th colspan="2"
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-500">
                                Pergerakan HPP</th>
                            <th colspan="4"
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-500">
                                Kondisi Pasar</th>
                            <th colspan="2"
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-purple-50 dark:bg-purple-900/20 text-purple-800 dark:text-purple-500">
                                Variabel Pabrik</th>
                            <th colspan="3"
                                class="py-3 px-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-500">
                                Hasil Kalkulasi Engine</th>
                        </tr>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-[11px] uppercase font-bold text-center shadow-sm">
                            <th
                                class="py-3 px-4 sticky-col bg-slate-50 dark:bg-slate-800 z-20 text-left border-r border-slate-200 dark:border-slate-700">
                                Nama Barang & Tanggal</th>
                            <th class="py-3 px-4 text-left border-r border-slate-200 dark:border-slate-700">Kategori</th>
                            <th class="py-3 px-4 border-r border-slate-200 dark:border-slate-700">Sisa Stok</th>
                            <th class="py-3 px-4 border-r border-slate-200 dark:border-slate-700">Batas Kritis</th>

                            <th
                                class="py-3 px-4 bg-amber-50/50 dark:bg-amber-900/10 border-r border-slate-200 dark:border-slate-700">
                                HPP Kemarin</th>
                            <th
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-amber-50/50 dark:bg-amber-900/10">
                                HPP Hari Ini</th>

                            <th
                                class="py-3 px-4 bg-blue-50/50 dark:bg-blue-900/10 border-r border-slate-200 dark:border-slate-700">
                                Demand</th>
                            <th
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-blue-50/50 dark:bg-blue-900/10">
                                Komp 1</th>
                            <th
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-blue-50/50 dark:bg-blue-900/10">
                                Komp 2</th>
                            <th
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-blue-50/50 dark:bg-blue-900/10">
                                Komp 3</th>

                            <th
                                class="py-3 px-4 bg-purple-50/50 dark:bg-purple-900/10 border-r border-slate-200 dark:border-slate-700">
                                Yield (%)</th>
                            <th
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-purple-50/50 dark:bg-purple-900/10">
                                Target Marjin (Default)</th>

                            {{-- 🔥 PERBAIKAN TYPO HEADER DI SINI 🔥 --}}
                            <th
                                class="py-3 px-4 bg-emerald-50/50 dark:bg-emerald-900/10 border-r border-slate-200 dark:border-slate-700">
                                Harga Rekomendasi
                            </th>
                            <th
                                class="py-3 px-4 border-r border-slate-200 dark:border-slate-700 bg-emerald-50/50 dark:bg-emerald-900/10">
                                Marjin Aktual</th>
                            <th class="py-3 px-4 bg-emerald-50/50 dark:bg-emerald-900/10">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($pricings as $row)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">

                                <td
                                    class="py-3 px-4 sticky-col bg-white dark:bg-[#1e293b] group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50 border-r border-slate-200 dark:border-slate-700 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    <div class="font-bold text-slate-800 dark:text-slate-200 text-sm">
                                        {{ $row->product->product_name ?? 'N/A' }}</div>
                                    <div class="text-[10px] font-semibold text-slate-400 mt-0.5"><i
                                            class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($row->date_input)->format('d M Y') }}</div>
                                </td>
                                <td
                                    class="py-3 px-4 text-slate-600 dark:text-slate-400 border-r border-slate-100 dark:border-slate-800 text-xs">
                                    {{ $row->product->category ?? '-' }}</td>
                                <td
                                    class="py-3 px-4 text-center font-bold text-indigo-600 dark:text-indigo-400 border-r border-slate-100 dark:border-slate-800">
                                    {{ $row->stock }} <span
                                        class="text-[10px] text-slate-500 uppercase">{{ $row->product->unit ?? 'Pcs' }}</span>
                                </td>
                                <td
                                    class="py-3 px-4 text-center border-r border-slate-200 dark:border-slate-700 text-slate-500 text-xs">
                                    {{ $row->threshold_stock ?? 20 }}
                                </td>

                                <td
                                    class="py-3 px-4 text-right font-mono text-slate-400 line-through border-r border-slate-100 dark:border-slate-800 text-xs">
                                    Rp {{ number_format($row->hpp_prev, 0, ',', '.') }}
                                </td>
                                <td
                                    class="py-3 px-4 text-right border-r border-slate-200 dark:border-slate-700 font-mono font-bold text-amber-600 dark:text-amber-400 bg-amber-50/20 dark:bg-transparent">
                                    Rp {{ number_format($row->hpp, 0, ',', '.') }}
                                </td>

                                <td class="py-3 px-4 text-center border-r border-slate-100 dark:border-slate-800">
                                    @if (strtolower($row->demand) == 'tinggi')
                                        <span
                                            class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-[9px] font-extrabold uppercase tracking-wider"><i
                                                class="fas fa-fire mr-1"></i> Tinggi</span>
                                    @elseif(strtolower($row->demand) == 'rendah')
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-extrabold uppercase tracking-wider"><i
                                                class="fas fa-arrow-down mr-1"></i> Rendah</span>
                                    @else
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-[9px] font-extrabold uppercase tracking-wider"><i
                                                class="fas fa-minus mr-1"></i> Normal</span>
                                    @endif
                                </td>
                                <td
                                    class="py-3 px-4 text-right font-mono text-slate-500 border-r border-slate-100 dark:border-slate-800 text-xs">
                                    {{ $row->c1 ? 'Rp ' . number_format($row->c1, 0, ',', '.') : '-' }}</td>
                                <td
                                    class="py-3 px-4 text-right font-mono text-slate-500 border-r border-slate-100 dark:border-slate-800 text-xs">
                                    {{ $row->c2 ? 'Rp ' . number_format($row->c2, 0, ',', '.') : '-' }}</td>
                                <td
                                    class="py-3 px-4 text-right border-r border-slate-200 dark:border-slate-700 font-mono text-slate-500 text-xs">
                                    {{ $row->c3 ? 'Rp ' . number_format($row->c3, 0, ',', '.') : '-' }}</td>

                                <td
                                    class="py-3 px-4 text-center font-bold text-slate-600 border-r border-slate-100 text-[11px]">
                                    @if (!empty($row->yield_applied))
                                        <span class="text-blue-600 font-extrabold" title="Yield Aktual dari Sistem">
                                            {{ (float) $row->yield_applied * 100 }}% <i
                                                class="fas fa-bolt text-[9px] ml-1"></i>
                                        </span>
                                    @else
                                        <span title="Yield Master Produk">
                                            {{ isset($row->product->yield_percent) ? (float) $row->product->yield_percent * 100 : 100 }}%
                                        </span>
                                    @endif
                                </td>

                                {{-- 🔥 PERBAIKAN LOGIKA DESIMAL TARGET MARJIN DI SINI 🔥 --}}
                                <td
                                    class="py-3 px-4 text-center border-r border-slate-200 dark:border-slate-700 font-bold text-slate-600 text-[11px]">
                                    @php
                                        // Tarik setting global dan jinakkan desimalnya
                                        $globalSetting = \App\Models\FormulaSetting::first();
                                        $globalMargin = $globalSetting ? (float) $globalSetting->markup_base : 0.2;
                                        $displayGlobalMargin = $globalMargin < 1 ? $globalMargin * 100 : $globalMargin;

                                        // Tarik setting produk dan jinakkan desimalnya
                                        $productMargin = isset($row->product->markup)
                                            ? (float) $row->product->markup
                                            : 0;
                                        $displayProductMargin =
                                            $productMargin > 0 && $productMargin < 1
                                                ? $productMargin * 100
                                                : $productMargin;
                                    @endphp

                                    @if ($productMargin > 0)
                                        <span title="Target Margin dari Master Produk">
                                            {{ $displayProductMargin }}%
                                        </span>
                                    @else
                                        <span title="Target Margin dari Global Setting"
                                            class="text-purple-600 font-extrabold">
                                            {{ $displayGlobalMargin }}% <i class="fas fa-globe text-[9px] ml-1"></i>
                                        </span>
                                    @endif
                                </td>

                                <td
                                    class="py-3 px-4 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-base border-r border-slate-100 dark:border-slate-800 bg-emerald-50/40 dark:bg-emerald-900/20">
                                    Rp {{ number_format($row->final_price, 0, ',', '.') }}
                                </td>
                                <td
                                    class="py-3 px-4 text-center font-bold border-r border-slate-100 dark:border-slate-800 bg-emerald-50/40 dark:bg-emerald-900/20 text-sm">
                                    <span
                                        class="{{ $row->margin_percent < 0 ? 'text-red-500' : 'text-emerald-600' }}">{{ $row->margin_percent }}%</span>
                                </td>

                                <td class="py-3 px-4 text-center bg-emerald-50/40 dark:bg-emerald-900/20">
                                    @if ($row->status_margin === 'RED' || strtolower($row->status_margin) == 'rugi')
                                        <div
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-extrabold uppercase tracking-wider border border-red-200 dark:border-red-800/50 shadow-sm">
                                            <i class="fas fa-exclamation-triangle"></i> Darurat
                                        </div>
                                    @elseif ($row->status_margin === 'YELLOW')
                                        <div
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-lg text-[10px] font-extrabold uppercase tracking-wider border border-amber-200 dark:border-amber-800/50 shadow-sm">
                                            <i class="fas fa-balance-scale"></i> Sesuaikan
                                        </div>
                                    @else
                                        <div
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-extrabold uppercase tracking-wider border border-emerald-200 dark:border-emerald-800/50 shadow-sm">
                                            <i class="fas fa-check-circle"></i> Aman
                                        </div>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="15"
                                    class="py-16 text-center text-slate-500 bg-slate-50/50 dark:bg-slate-900/20">
                                    <div
                                        class="w-20 h-20 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 shadow-inner">
                                        <i class="fas fa-search-dollar text-3xl"></i>
                                    </div>
                                    <h3 class="font-black text-xl text-slate-700 dark:text-slate-300 mb-1">Belum Ada Data
                                        Laporan</h3>
                                    <p class="text-sm">Silakan input data pasar dan HPP harian di menu "Input Harian"
                                        terlebih dahulu.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($pricings) && $pricings->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#1e293b]">
                    {{ $pricings->links() }}
                </div>
            @endif

        </div>
    </div>

    <script>
        function toggleFilter() {
            const box = document.getElementById('filterBox');
            if (box.classList.contains('hidden')) {
                box.classList.remove('hidden');
                box.classList.add('block');
            } else {
                box.classList.remove('block');
                box.classList.add('hidden');
            }
        }
    </script>
@endsection
