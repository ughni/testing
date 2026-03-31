@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto pb-10">

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-600/20">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    History & Analitik Harga
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 ml-14">
                    Pantau pergerakan harga modal (HPP) vs rekomendasi harga jual per bulan.
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>

        <div
            class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mb-8">
            <div class="bg-black px-6 py-4 border-b border-slate-700">
                <h5 class="m-0 font-bold text-white flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-purple-400"></i> Grafik Rata-Rata Pergerakan Harga (Global)
                </h5>
            </div>
            <div class="p-6">
                <div class="relative w-full h-[300px] md:h-[400px]">
                    <canvas id="pricingChart"></canvas>
                </div>
            </div>
        </div>

        @php
            // Ambil semua nama produk unik
            $uniqueProducts = $histories->pluck('product.product_name')->filter()->unique();
        @endphp

        <div
            class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">

            <div
                class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h5 class="font-bold text-slate-700 dark:text-slate-200 flex items-center">
                    <i class="fas fa-list mr-2 text-slate-400"></i> Log Histori Lengkap
                    <span
                        class="ml-3 bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Total:
                        {{ $histories->total() }} Data</span>
                </h5>

                <div class="relative w-full sm:w-72" id="custom-select-container">
                    <button type="button" id="custom-select-button"
                        class="flex justify-between items-center w-full pl-4 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-purple-500 sm:text-sm transition-colors font-medium shadow-sm">
                        <span id="custom-select-text" class="truncate flex-1 text-left">-- Cari & Filter Produk --</span>
                        <i class="fas fa-chevron-down text-xs text-slate-400 ml-2"></i>
                    </button>

                    <div id="custom-select-dropdown"
                        class="absolute z-20 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl hidden flex-col overflow-hidden">
                        <div class="p-2 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" id="custom-select-search"
                                    class="w-full pl-8 pr-3 py-1.5 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500 text-slate-700 dark:text-slate-300 placeholder-slate-400"
                                    placeholder="Ketik nama produk...">
                            </div>
                        </div>
                        <ul id="custom-select-list"
                            class="max-h-60 overflow-y-auto py-1 text-sm text-slate-700 dark:text-slate-300 custom-scrollbar">
                            <li class="custom-option px-4 py-2 hover:bg-purple-50 dark:hover:bg-purple-900/30 cursor-pointer transition-colors font-bold text-purple-600 dark:text-purple-400"
                                data-value="all">
                                <i class="fas fa-border-all mr-2"></i> Tampilkan Semua
                            </li>
                            @foreach ($uniqueProducts as $prod)
                                <li class="custom-option px-4 py-2 hover:bg-purple-50 dark:hover:bg-purple-900/30 cursor-pointer transition-colors border-t border-slate-50 dark:border-slate-800/50"
                                    data-value="{{ strtolower($prod) }}">
                                    {{ $prod }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/30 text-slate-500 dark:text-slate-400 text-xs uppercase font-bold tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-right">HPP (Modal)</th>
                            <th class="px-6 py-4 text-right">Harga Jual</th>
                            <th class="px-6 py-4 text-center">Margin</th>
                            <th class="px-6 py-4 text-center">Grafik</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="text-sm divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($histories as $h)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors history-row"
                                data-product="{{ strtolower($h->product->product_name ?? '') }}">
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($h->date_input)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $h->product->product_name ?? 'Produk Dihapus' }}
                                </td>
                                <td class="px-6 py-4 text-right text-slate-600 dark:text-slate-400">
                                    Rp {{ number_format($h->hpp, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-blue-600 dark:text-blue-400">
                                    Rp {{ number_format($h->final_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ number_format((float) $h->margin_percent > 1 ? $h->margin_percent : $h->margin_percent * 100, 1) }}%
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if (isset($h->product_id))
                                        <button type="button" onclick="openProductChart(this)"
                                            data-id="{{ $h->product_id }}" data-name="{{ $h->product->product_name }}"
                                            class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all shadow-sm"
                                            title="Lihat Grafik Penjualan {{ $h->product->product_name }}">
                                            <i class="fas fa-chart-pie text-xs pointer-events-none"></i>
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Belum ada data histori yang tersimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($histories->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>

    </div>

    <div id="productChartModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeProductChart()">
        </div>

        <div
            class="relative bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-11/12 max-w-4xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
            <div class="bg-black px-6 py-4 flex justify-between items-center">
                <h5 class="font-bold text-white flex items-center">
                    <i class="fas fa-search-dollar mr-2 text-purple-400"></i> Analitik Pergerakan Harga: <span
                        id="modalProductName" class="ml-2 text-purple-300"></span>
                </h5>
                <button onclick="closeProductChart()" class="text-slate-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div id="modalLoading" class="flex flex-col items-center justify-center h-[300px]">
                    <i class="fas fa-circle-notch fa-spin text-4xl text-purple-500 mb-3"></i>
                    <p class="text-slate-500 font-medium">Sedang memproses data kalkulasi...</p>
                </div>
                <div id="modalChartContainer" class="relative w-full h-[300px] hidden">
                    <canvas id="modalPricingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script id="chartDataLabels" type="application/json">{!! json_encode($chartLabels) !!}</script>
    <script id="chartDataHpp" type="application/json">{!! json_encode($chartHpp) !!}</script>
    <script id="chartDataPrice" type="application/json">{!! json_encode($chartPrice) !!}</script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // FITUR BARU: CUSTOM SEARCHABLE DROPDOWN
            // ==========================================
            const selectBtn = document.getElementById('custom-select-button');
            const selectDropdown = document.getElementById('custom-select-dropdown');
            const searchInput = document.getElementById('custom-select-search');
            const selectList = document.getElementById('custom-select-list');
            const selectText = document.getElementById('custom-select-text');
            const options = selectList.querySelectorAll('.custom-option');
            const historyRows = document.querySelectorAll('.history-row');

            // Buka/Tutup Dropdown
            selectBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Biar gak langsung ketutup sama event body
                const isHidden = selectDropdown.classList.contains('hidden');
                if (isHidden) {
                    selectDropdown.classList.remove('hidden');
                    selectDropdown.classList.add('flex');
                    setTimeout(() => searchInput.focus(), 100); // Otomatis kursor ke kolom search
                } else {
                    selectDropdown.classList.add('hidden');
                    selectDropdown.classList.remove('flex');
                }
            });

            // Tutup kalau klik di luar area dropdown
            document.addEventListener('click', (e) => {
                if (!selectBtn.contains(e.target) && !selectDropdown.contains(e.target)) {
                    selectDropdown.classList.add('hidden');
                    selectDropdown.classList.remove('flex');
                }
            });

            // Fitur Ketik & Cari (Filter List)
            searchInput.addEventListener('input', (e) => {
                const filterValue = e.target.value.toLowerCase().trim();
                options.forEach(option => {
                    if (option.getAttribute('data-value') === 'all') return; // Lewati opsi "Semua"

                    const text = option.textContent.toLowerCase();
                    if (text.includes(filterValue)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });

            // Waktu produk di-klik (Filter Tabel Utama)
            options.forEach(option => {
                option.addEventListener('click', () => {
                    const selectedValue = option.getAttribute('data-value');
                    const selectedText = option.textContent.trim();

                    // 1. Ganti teks di tombol
                    selectText.innerHTML = selectedValue === 'all' ?
                        '-- Cari & Filter Produk --' :
                        `<span class="text-purple-600 font-bold">${selectedText}</span>`;

                    // 2. Tutup Dropdown & Reset Pencarian
                    selectDropdown.classList.add('hidden');
                    selectDropdown.classList.remove('flex');
                    searchInput.value = '';
                    options.forEach(opt => opt.style.display = ''); // Tampilkan semua list lagi

                    // 3. Eksekusi Saring Baris Tabel
                    historyRows.forEach(row => {
                        const rowProduct = row.getAttribute('data-product');
                        if (selectedValue === 'all' || rowProduct === selectedValue) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });

            // ==========================================
            // LOGIKA GRAFIK UTAMA (GLOBAL)
            // ==========================================
            const ctx = document.getElementById('pricingChart').getContext('2d');
            const labels = JSON.parse(document.getElementById('chartDataLabels').textContent);
            const dataHpp = JSON.parse(document.getElementById('chartDataHpp').textContent);
            const dataPrice = JSON.parse(document.getElementById('chartDataPrice').textContent);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Rata-rata HPP (Modal)',
                            data: dataHpp,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Rata-rata Harga Jual',
                            data: dataPrice,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#64748b'
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context
                                            .parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                color: '#64748b',
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            },
                            grid: {
                                color: 'rgba(100, 116, 139, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#64748b'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        // ==========================================
        // LOGIKA GRAFIK SPESIFIK PER PRODUK (AJAX MODAL)
        // ==========================================
        let modalChartInstance = null;

        async function openProductChart(element) {
            const productId = element.getAttribute('data-id');
            const productName = element.getAttribute('data-name');

            const modal = document.getElementById('productChartModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('modalProductName').innerText = productName;

            document.getElementById('modalLoading').classList.remove('hidden');
            document.getElementById('modalChartContainer').classList.add('hidden');

            try {
                const response = await fetch(`/api/chart/product/${productId}`);
                const data = await response.json();

                document.getElementById('modalLoading').classList.add('hidden');
                document.getElementById('modalChartContainer').classList.remove('hidden');

                const ctxModal = document.getElementById('modalPricingChart').getContext('2d');
                if (modalChartInstance) {
                    modalChartInstance.destroy();
                }

                modalChartInstance = new Chart(ctxModal, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                                label: 'Histori HPP (Modal)',
                                data: data.hpp,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                borderWidth: 2,
                                tension: 0.2,
                                fill: true,
                                pointRadius: 4,
                                pointBackgroundColor: '#ef4444'
                            },
                            {
                                label: 'Histori Harga Jual Final',
                                data: data.price,
                                borderColor: '#a855f7',
                                backgroundColor: 'rgba(168, 85, 247, 0.05)',
                                borderWidth: 2,
                                tension: 0.2,
                                fill: true,
                                pointRadius: 4,
                                pointBackgroundColor: '#a855f7'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: '#64748b'
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat(
                                            'id-ID').format(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: '#64748b'
                                },
                                grid: {
                                    color: 'rgba(100, 116, 139, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#64748b'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error("Gagal menarik data:", error);
                alert("Maaf, terjadi kesalahan saat mengambil data grafik.");
                closeProductChart();
            }
        }

        function closeProductChart() {
            const modal = document.getElementById('productChartModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection
