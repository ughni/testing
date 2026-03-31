@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-chart-pie text-lg"></i>
                </div>
                Analytics & Insights
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                Pantau visualisasi pergerakan harga dan komposisi Master Data secara Real-Time.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                <i class="fas fa-circle text-[8px] mr-2 animate-pulse"></i> LIVE DATA
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md">
            <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-2xl font-bold"><i class="fas fa-boxes"></i></div>
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Total Master Produk</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white">{{ $totalProducts }} <span class="text-sm font-medium text-slate-400">Item</span></h3>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md">
            <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center text-2xl font-bold"><i class="fas fa-robot"></i></div>
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Sistem Dynamic</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white">{{ $tipeDynamic }} <span class="text-sm font-medium text-slate-400">Produk</span></h3>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md">
            <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center text-2xl font-bold"><i class="fas fa-shield-alt"></i></div>
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Sistem HET Limit</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white">{{ $tipeHET }} <span class="text-sm font-medium text-slate-400">Produk</span></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h4 class="font-extrabold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-3">Komposisi Rules Pricing</h4>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="priceTypeChart" 
                        data-dynamic="{{ $tipeDynamic }}" 
                        data-consignment="{{ $tipeConsignment }}" 
                        data-het="{{ $tipeHET }}">
                </canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
            <h4 class="font-extrabold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-3">Pergerakan Rata-rata HPP (7 Hari Terakhir)</h4>
            <div class="relative h-64 w-full">
                <div id="lineChartData" class="hidden" 
                     data-labels="{{ json_encode($labelTanggal ?? []) }}" 
                     data-hpp="{{ json_encode($dataHPP ?? []) }}">
                </div>
                <canvas id="trendChart"></canvas>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil Mode Tema (Dark/Light) buat warna teks grafik
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#475569';
        const gridColor = isDark ? '#334155' : '#f1f5f9';

        // ==========================================
        // 1. RENDER CHART DONAT (TIPE HARGA)
        // ==========================================
        const canvasPie = document.getElementById('priceTypeChart');
        const valDynamic = parseInt(canvasPie.getAttribute('data-dynamic')) || 0;
        const valCons = parseInt(canvasPie.getAttribute('data-consignment')) || 0;
        const valHet = parseInt(canvasPie.getAttribute('data-het')) || 0;

        new Chart(canvasPie.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Dynamic Pricing', 'Consignment', 'HET Limit'],
                datasets: [{
                    data: [valDynamic, valCons, valHet],
                    backgroundColor: ['#3b82f6', '#a855f7', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: { weight: 'bold' } } }
                }
            }
        });

        // ==========================================
        // 2. RENDER CHART GARIS (TREN HPP DARI DATABASE)
        // ==========================================
        const dataContainer = document.getElementById('lineChartData');
        let labelsDariDB = [];
        let dataHppDariDB = [];

        try {
            // Parsing JSON dari atribut HTML
            labelsDariDB = JSON.parse(dataContainer.getAttribute('data-labels'));
            dataHppDariDB = JSON.parse(dataContainer.getAttribute('data-hpp'));
        } catch (e) {
            console.error("Gagal parsing data HPP:", e);
        }

        const ctxLine = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                // Kalau array kosong, kasih label Default
                labels: labelsDariDB.length > 0 ? labelsDariDB : ['Belum Ada Input Harian'],
                datasets: [{
                    label: 'Rata-rata HPP (Rp)',
                    data: dataHppDariDB.length > 0 ? dataHppDariDB : [0],
                    borderColor: '#10b981', // Emerald
                    backgroundColor: 'rgba(16, 185, 129, 0.1)', // Transparan
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Bikin melengkung elegan
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        grid: { color: gridColor }, 
                        ticks: { 
                            color: textColor,
                            callback: function(value) {
                                // Format angka jadi Rupiah (misal: 15.000)
                                if(value === 0) return 0;
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        } 
                    },
                    x: { grid: { display: false }, ticks: { color: textColor } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection