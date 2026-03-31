@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20 relative">
                    <i class="fas fa-bell text-lg"></i>
                    @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white dark:border-slate-900"></span>
                    </span>
                    @endif
                </div>
                Notification Center
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                Pusat informasi dan peringatan sistem. Pantau masa berlaku kontrak dan aktivitas penting di sini.
            </p>
        </div>
        
        <div class="md:ml-auto">
            {{-- Tombol Tandai Semua Dibaca --}}
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-check-double text-indigo-500"></i> Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        
        <div class="flex border-b border-slate-100 dark:border-slate-800 px-6 pt-4 gap-6">
            <button class="pb-3 text-sm font-extrabold text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400">
                Semua Peringatan 
                @if($unreadCount > 0)
                <span class="ml-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 py-0.5 px-2 rounded-full text-[10px]">{{ $unreadCount }} Baru</span>
                @endif
            </button>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800/50">
            
            @forelse($notifications as $notif)
                @php
                    // Logika pewarnaan UI berdasarkan Tipe Notifikasi
                    $bgClass = 'bg-slate-50/50 dark:bg-slate-800/10';
                    $iconBgClass = 'bg-blue-100 dark:bg-blue-900/30';
                    $iconTextClass = 'text-blue-600 dark:text-blue-400';
                    $dotColor = 'bg-blue-500';

                    if($notif->type == 'danger') {
                        $bgClass = 'bg-red-50/50 dark:bg-red-900/10';
                        $iconBgClass = 'bg-red-100 dark:bg-red-900/30';
                        $iconTextClass = 'text-red-600 dark:text-red-400';
                        $dotColor = 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]';
                    } elseif($notif->type == 'warning') {
                        $bgClass = 'bg-amber-50/30 dark:bg-amber-900/5';
                        $iconBgClass = 'bg-amber-100 dark:bg-amber-900/30';
                        $iconTextClass = 'text-amber-600 dark:text-amber-400';
                        $dotColor = 'bg-amber-500';
                    } elseif($notif->type == 'success') {
                        $bgClass = 'bg-emerald-50/30 dark:bg-emerald-900/5';
                        $iconBgClass = 'bg-emerald-100 dark:bg-emerald-900/30';
                        $iconTextClass = 'text-emerald-600 dark:text-emerald-400';
                        $dotColor = 'bg-emerald-500';
                    }

                    // Kalau udah dibaca, redupkan opacity & hilangkan titik notif
                    $readOpacity = $notif->is_read ? 'opacity-60 bg-white dark:bg-[#1e293b]' : $bgClass;
                    $dotVisibility = $notif->is_read ? 'bg-transparent shadow-none' : $dotColor;
                @endphp

                <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors flex gap-4 items-start relative {{ $readOpacity }}">
                    <div class="w-2 h-2 {{ $dotVisibility }} rounded-full mt-2 shrink-0"></div>
                    
                    <div class="w-10 h-10 rounded-full {{ $iconBgClass }} {{ $iconTextClass }} flex items-center justify-center shrink-0">
                        <i class="{{ $notif->icon }}"></i>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm">{!! $notif->title !!}</h4>
                            <span class="text-xs text-slate-400 font-medium">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            {!! $notif->message !!}
                        </p>
                    </div>
                </div>

            @empty
                <div class="p-10 text-center flex flex-col items-center justify-center">
                    <i class="fas fa-bell-slash text-5xl text-slate-300 dark:text-slate-600 mb-4 opacity-50"></i>
                    <h4 class="text-slate-500 dark:text-slate-400 font-bold">Belum Ada Notifikasi</h4>
                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Sistem sedang memantau, kami akan memberitahu jika ada aktivitas penting.</p>
                </div>
            @endforelse

        </div>

        {{-- 🔥 KODINGAN PAGINATION DINAMIS 🔥 --}}
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</div>
@endsection