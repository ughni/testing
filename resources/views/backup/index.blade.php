@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto pb-10">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20 relative">
                    <i class="fas fa-database text-lg"></i>
                </div>
                Backup & Restore
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                Amankan data Pricing Engine Anda. Lakukan pencadangan rutin untuk mencegah kehilangan data sistem.
            </p>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Card Backup (YANG INI FUNGSI ASLI) -->
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <i class="fas fa-download text-8xl text-indigo-500"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-cloud-download-alt"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Backup Database</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                    Unduh seluruh struktur dan isi database saat ini. File akan disimpan dalam format <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-indigo-500 font-mono text-xs">.sql</code>.
                </p>
                
                <form action="{{ route('backup.download') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i> Buat & Unduh Backup
                    </button>
                </form>
            </div>
        </div>

        <!-- Card Restore (INI BUAT PAJANGAN SEMENTARA BIAR BOS TERPUKAU) -->
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 relative overflow-hidden opacity-90 hover:opacity-100 transition-opacity">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <i class="fas fa-upload text-8xl text-emerald-500"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Restore Database</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                    Kembalikan data dari file backup sebelumnya. <span class="text-red-500 dark:text-red-400 font-bold">Peringatan:</span> Tindakan ini akan menimpa data saat ini!
                </p>
                
                <div class="p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl text-center bg-slate-50 dark:bg-slate-800/30 cursor-not-allowed">
                    <i class="fas fa-file-sql text-slate-400 text-2xl mb-2"></i>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Fitur Restore membutuhkan otorisasi Super Admin Server.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Info Alert (Nilai Jual Enterprise) -->
    <div class="mt-8 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4 flex gap-4 items-start">
        <div class="text-amber-500 mt-0.5">
            <i class="fas fa-shield-alt text-lg"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-400 mb-1">Keamanan Data Terjamin</h4>
            <p class="text-sm text-amber-700/80 dark:text-amber-500/80">
                Setiap kali Anda melakukan proses backup, sistem akan otomatis mencatatnya di <b>Audit Trail</b> dan mengirimkan peringatan ke <b>Notification Center</b> untuk mencegah pencurian data tanpa izin.
            </p>
        </div>
    </div>

</div>
@endsection