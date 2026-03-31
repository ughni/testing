@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto pb-10">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-800 dark:bg-slate-700 rounded-xl flex items-center justify-center text-emerald-400 shadow-lg relative">
                    <i class="fas fa-network-wired text-lg"></i>
                </div>
                API Integration & Webhooks
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 ml-13">
                Kelola API Key untuk menghubungkan Pricing Engine dengan aplikasi pihak ketiga (ERP Gudang, POS Kasir, atau E-Commerce).
            </p>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/50 rounded-xl p-4 flex gap-4 items-start">
        <div class="text-emerald-500 mt-0.5"><i class="fas fa-check-circle text-lg"></i></div>
        <div>
            <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-400 mb-1">Berhasil!</h4>
            <p class="text-sm text-emerald-700/80 dark:text-emerald-500/80">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="mb-6 bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800/50 rounded-xl p-4 flex gap-4 items-start">
        <div class="text-rose-500 mt-0.5"><i class="fas fa-exclamation-triangle text-lg"></i></div>
        <div>
            <h4 class="text-sm font-bold text-rose-800 dark:text-rose-400 mb-1">Akses Diputus</h4>
            <p class="text-sm text-rose-700/80 dark:text-rose-500/80">{{ session('warning') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Form Generate -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-800/20">
                    <h2 class="font-bold text-slate-800 dark:text-white"><i class="fas fa-key text-indigo-500 mr-2"></i> Generate API Key Baru</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('api.generate') }}" method="POST">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Aplikasi Klien <span class="text-red-500">*</span></label>
                            <input type="text" name="app_name" required placeholder="Contoh: POS Kasir Cabang 1" 
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Berikan nama yang deskriptif agar mudah diidentifikasi saat audit.</p>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-slate-800 dark:bg-indigo-600 hover:bg-slate-900 dark:hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-magic"></i> Buat Token Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar API Keys -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-800/20 flex justify-between items-center">
                    <h2 class="font-bold text-slate-800 dark:text-white"><i class="fas fa-shield-alt text-emerald-500 mr-2"></i> Daftar Active API Keys</h2>
                    <span class="text-xs font-bold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-1 rounded-md">{{ $credentials->count() }} Kunci</span>
                </div>
                
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <th class="p-4 font-bold">Aplikasi Klien</th>
                                <th class="p-4 font-bold">Secret Token (API Key)</th>
                                <th class="p-4 font-bold">Status</th>
                                <th class="p-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($credentials as $cred)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $cred->app_name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1">Dibuat: {{ $cred->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded text-xs font-mono text-slate-600 dark:text-slate-300 flex items-center justify-between gap-2 max-w-[200px]">
                                        <span class="truncate">{{ $cred->is_active ? $cred->api_key : '••••••••••••••••••••' }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($cred->is_active)
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Revoked
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @if($cred->is_active)
                                    <form action="{{ route('api.revoke', $cred->id) }}" method="POST" onsubmit="return confirm('Yakin ingin memutus akses aplikasi ini?');">
                                        @csrf
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 font-bold text-xs bg-rose-50 dark:bg-rose-500/10 px-3 py-1.5 rounded transition-colors">
                                            Revoke Akses
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs text-slate-400 italic">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                    <div class="mb-3 text-slate-300 dark:text-slate-600"><i class="fas fa-key text-4xl"></i></div>
                                    Belum ada API Key yang digenerate.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection