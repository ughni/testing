@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-{{ $pageData['color'] }}-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-{{ $pageData['color'] }}-600/20">
                    <i class="fas {{ $pageData['icon'] }} text-lg"></i>
                </div>
                {{ $pageData['title'] }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                {{ $pageData['desc'] }}
            </p>
        </div>
        <div class="text-right">
            <span class="text-3xl font-black text-slate-800 dark:text-white">{{ $contracts->total() }}</span>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Total Dokumen</p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs text-slate-500 dark:text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Nama Supplier</th>
                        <th class="px-6 py-4 text-center">Versi</th>
                        <th class="px-6 py-4 text-center">Status File</th>
                        <th class="px-6 py-4 text-center">Tanggal Upload</th>
                        <th class="px-6 py-4 text-center bg-amber-50 dark:bg-amber-900/10 text-amber-700 dark:text-amber-500">Berlaku Sampai</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-sm">
                    @forelse($contracts as $contract)
                        @php
                            // Cek apakah ini versi tertinggi
                            $isActiveVersion = isset($maxVersions[$contract->supplier_id]) && $maxVersions[$contract->supplier_id] == $contract->contract_version;
                            
                            $validDate = $contract->valid_until ?? $contract->end_date ?? null; 
                            
                            $isNearExpiry = false;
                            $isExpired = false;

                            if($validDate) {
                                $parsedDate = \Carbon\Carbon::parse($validDate);
                                $isExpired = $parsedDate->isPast();
                                $isNearExpiry = !$isExpired && $parsedDate->diffInDays(now()) <= 30; 
                            }
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">
                                    <i class="fas fa-building mr-2 text-slate-400"></i> {{ $contract->supplier->nama_supplier ?? 'Supplier Dihapus' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-lg text-xs border border-slate-200 dark:border-slate-600">
                                    V-{{ $contract->contract_version }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($isActiveVersion)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-check-circle mr-1"></i> LATEST
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="fas fa-archive mr-1"></i> OLD VERSION
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-medium text-xs">
                                {{ $contract->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4 text-center border-l border-slate-100 dark:border-slate-800">
                                @if($validDate)
                                    <div class="font-black text-sm {{ $isExpired ? 'text-red-500' : ($isNearExpiry ? 'text-amber-500' : 'text-slate-700 dark:text-slate-300') }}">
                                        {{ \Carbon\Carbon::parse($validDate)->format('d M Y') }}
                                    </div>
                                    @if($isExpired)
                                        <span class="text-[9px] text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-2 py-0.5 rounded font-bold uppercase tracking-wider mt-1 inline-block">Habis Masa Berlaku</span>
                                    @elseif($isNearExpiry)
                                        <span class="text-[9px] text-amber-600 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-0.5 rounded font-bold uppercase tracking-wider mt-1 inline-block animate-pulse">Hampir Habis</span>
                                    @else
                                        <span class="text-[9px] text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 px-2 py-0.5 rounded font-bold uppercase tracking-wider mt-1 inline-block">Aman</span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">Tidak Ditentukan</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white dark:bg-blue-900/30 dark:text-blue-400 font-bold text-xs transition-all shadow-sm">
                                    <i class="fas fa-eye mr-2"></i> Lihat File
                                </a>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <i class="fas fa-folder-open text-4xl mb-3 text-slate-300 dark:text-slate-600 block opacity-50"></i>
                                Belum ada dokumen kontrak di kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#1e293b]">
                {{ $contracts->links() }}
            </div>
        @endif

    </div>
</div>
@endsection