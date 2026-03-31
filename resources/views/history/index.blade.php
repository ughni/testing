@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Perubahan Harga</h2>
        <p class="text-sm text-gray-500">Log pencatatan harga harian berdasarkan hasil kalkulasi sistem.</p>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-[200px]">
            <input type="text" placeholder="Cari produk..." class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <select class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none">
            <option>Semua Kategori</option>
            <option>Dynamic</option>
            <option>HET</option>
        </select>
        <button class="bg-slate-800 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-slate-700 transition">
            <i class="fas fa-filter mr-2"></i> Filter
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Tanggal & Jam</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Nama Produk</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">HPP</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Harga Jual</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Margin (%)</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($histories as $h)
                    <tr class="hover:bg-gray-50 transition text-sm">
                        <td class="p-4 text-gray-500">
                            {{ $h->created_at->format('d M Y') }}
                            <span class="block text-[10px]">{{ $h->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="p-4 font-bold text-gray-800">{{ $h->product->name }}</td>
                        <td class="p-4 text-gray-600">Rp {{ number_format($h->hpp, 0, ',', '.') }}</td>
                        <td class="p-4 font-bold text-blue-600">Rp {{ number_format($h->suggested_price, 0, ',', '.') }}</td>
                        <td class="p-4">
                            <span class="text-green-600 font-semibold">{{ $h->margin_percentage }}%</span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-md text-[10px] font-bold">
                                <i class="fas fa-caret-up mr-1"></i> OPTIMAL
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-400 italic">Belum ada riwayat kalkulasi harga.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection