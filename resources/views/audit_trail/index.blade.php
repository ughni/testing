@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-white shadow-lg shadow-slate-800/20">
                    <i class="fas fa-shoe-prints text-lg"></i>
                </div>
                Audit Trail (Log Aktivitas)
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                Rekaman jejak seluruh aktivitas pengguna. Pantau siapa melakukan apa dan kapan.
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1e293b] p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
        <form action="{{ route('audit_trail.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white">
                </div>
            </div>

            <div class="w-full md:w-48">
                <select name="module" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white cursor-pointer">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="action" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white cursor-pointer">
                    <option value="">Semua Aksi</option>
                    <option value="CREATE" {{ request('action') == 'CREATE' ? 'selected' : '' }}>CREATE (Buat)</option>
                    <option value="UPDATE" {{ request('action') == 'UPDATE' ? 'selected' : '' }}>UPDATE (Ubah)</option>
                    <option value="DELETE" {{ request('action') == 'DELETE' ? 'selected' : '' }}>DELETE (Hapus)</option>
                    <option value="LOGIN" {{ request('action') == 'LOGIN' ? 'selected' : '' }}>LOGIN</option>
                </select>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold transition-all text-sm shadow-md">
                Filter Data
            </button>
            <a href="{{ route('audit_trail.index') }}" class="px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold transition-all text-sm text-center">
                Reset
            </a>
        </form>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Waktu & Tanggal</th>
                        <th class="px-6 py-4">Pengguna (User)</th>
                        <th class="px-6 py-4 text-center">Tipe Aksi</th>
                        <th class="px-6 py-4">Modul</th>
                        <th class="px-6 py-4 w-1/3">Detail Aktivitas</th>
                        <th class="px-6 py-4 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-sm">
                    @forelse($logs as $log)
                        @php
                            $actionColor = 'bg-slate-100 text-slate-600';
                            if($log->action == 'CREATE') $actionColor = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                            if($log->action == 'UPDATE') $actionColor = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                            if($log->action == 'DELETE') $actionColor = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                            if($log->action == 'LOGIN') $actionColor = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-xs font-bold text-slate-600 dark:text-slate-300">
                                {{ $log->created_at->format('d M Y') }} <br>
                                <span class="text-[10px] text-slate-400 font-normal">{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                                {{ $log->user->name ?? 'System / Dihapus' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded text-[10px] font-bold tracking-widest {{ $actionColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-600 dark:text-slate-400 text-xs">
                                <i class="fas fa-cube mr-1 text-slate-400"></i> {{ $log->module }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 whitespace-normal min-w-[250px]">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-mono text-slate-400">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <i class="fas fa-shoe-prints text-4xl mb-3 text-slate-300 dark:text-slate-600 block opacity-50"></i>
                                Belum ada rekaman aktivitas yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- 🔥 KODINGAN PAGINATION YANG UDAH DIPERBAIKI 🔥 --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</div>
@endsection