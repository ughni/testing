@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                📦 Process Plan (Rencana Pembelian)
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Daftar produk dengan stok di bawah batas aman (Threshold: 20) beserta rekomendasi kuantitas restock.
            </p>
        </div>
        
        <div>
            <a href="{{ route('purchase-plan.print') }}" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-600/20 transition-all hover:-translate-y-0.5">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF untuk ACC
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs uppercase font-bold tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Rekomendasi Supplier</th>
                        <th class="px-6 py-4 text-center">Sisa Stok</th>
                        <th class="px-6 py-4 text-right">Harga Modal (HPP)</th>
                        <th class="px-6 py-4 text-center text-blue-600 dark:text-blue-400">Rekomendasi Beli</th>
                        <th class="px-6 py-4 text-right text-emerald-600 dark:text-emerald-400">Estimasi Anggaran</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/60">
                    @php $totalAnggaran = 0; @endphp
                    @forelse($purchasePlanItems as $item)
                        @php $totalAnggaran += $item->estimated_cost; @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                                {{ $item->product_name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                <i class="fas fa-truck text-slate-400 mr-1"></i> {{ $item->supplier->supplier_name ?? 'Belum Ada Supplier' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                    {{ $item->current_stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600 dark:text-slate-300">
                                Rp {{ number_format($item->dailyInputs->first()->hpp ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center font-black text-blue-600 dark:text-blue-400 text-lg">
                                +{{ $item->recommended_buy_qty }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($item->estimated_cost, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-check-circle text-5xl text-emerald-400 mb-4"></i>
                                    <h3 class="font-bold text-slate-800 dark:text-white text-lg">Semua Stok Aman!</h3>
                                    <p class="text-sm text-slate-500 mt-1">Tidak ada produk yang perlu dibeli saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($purchasePlanItems->count() > 0)
                <tfoot class="bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right font-bold text-slate-700 dark:text-slate-300">Total Kebutuhan Dana:</td>
                        <td class="px-6 py-4 text-right font-black text-emerald-600 dark:text-emerald-400 text-lg">
                            Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection