@extends('layouts.app')

@section('content')
<div class="max-w-[100%] mx-auto pb-10 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                <i class="fas fa-address-book text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">Master Data Supplier</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola daftar kontak, kualifikasi, dan profil supplier resmi.</p>
            </div>
        </div>
        {{-- <div>
            <button class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition-all flex items-center text-sm">
                <i class="fas fa-file-import mr-2"></i> Import Massal
            </button>
        </div> --}}
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-l-4 border-emerald-500 rounded-r-xl font-bold flex items-center">
            <i class="fas fa-check-circle mr-2 text-lg"></i> {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-l-4 border-red-500 rounded-r-xl">
            <ul class="list-disc ml-5 font-medium text-sm">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        
        <div class="xl:col-span-4">
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden sticky top-6">
                
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/50">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center text-lg">
                        <i class="fas fa-plus mr-2 text-blue-500"></i> Tambah Supplier Baru
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. Supplier / ID <span class="text-red-500">*</span></label>
                            <input type="text" name="no_supplier" placeholder="Contoh: SUP-001" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white font-medium transition-all" required>
                        </div>
                        
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Supplier / PT <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_supplier" placeholder="Contoh: PT Makmur Jaya" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white font-medium transition-all" required>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kualifikasi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="kualifikasi" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white font-medium appearance-none transition-all" required>
                                    <option value="" disabled selected>-- Pilih Kualifikasi --</option>
                                    <option value="produsen">Produsen / Pabrik</option>
                                    <option value="distributor">Distributor Utama</option>
                                    <option value="agen">Agen</option>
                                    <option value="retail">Retail</option>
                                    <option value="pasar">Pasar Tradisional</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 p-5 bg-slate-50/50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-[11px] font-black text-slate-500 dark:text-slate-400 mb-4 uppercase tracking-wider flex items-center">
                                <i class="fas fa-address-card mr-2"></i> Informasi Kontak (Opsional)
                            </p>
                            
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kontak Person / No. HP</label>
                                <input type="text" name="kontak_person" placeholder="Contoh: Budi (0812...)" class="w-full px-3 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-md focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white transition-all">
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                                <input type="email" name="email" placeholder="contoh@ptmakmur.com" class="w-full px-3 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-md focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Lengkap</label>
                                <textarea name="alamat" rows="2" placeholder="Jalan Raya..." class="w-full px-3 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-md focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-white transition-all"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-md shadow-blue-600/20 transition-all flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Simpan Supplier
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="xl:col-span-8">
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col h-full">
                
                <div class="p-5 border-b border-slate-100 dark:border-slate-700/50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-slate-50/50 dark:bg-slate-900/20">
                    
                    <div class="flex items-center gap-3">
                        <h3 class="font-extrabold text-slate-800 dark:text-white text-lg flex items-center">
                            <i class="fas fa-chart-line text-blue-500 mr-2"></i> Monitor Supplier
                        </h3>
                        <span class="px-2.5 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-full">
                            {{ $suppliers->total() ?? 0 }} Item
                        </span>
                    </div>

                    <form action="{{ route('suppliers.index') }}" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row gap-2">
                        <div class="relative min-w-[160px]">
                            <select name="kualifikasi" onchange="this.form.submit()" class="w-full pl-4 pr-10 py-2 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm text-slate-700 dark:text-slate-200 font-medium appearance-none">
                                <option value="">Semua Kualifikasi</option>
                                <option value="produsen" {{ request('kualifikasi') == 'produsen' ? 'selected' : '' }}>Produsen</option>
                                <option value="distributor" {{ request('kualifikasi') == 'distributor' ? 'selected' : '' }}>Distributor</option>
                                <option value="agen" {{ request('kualifikasi') == 'agen' ? 'selected' : '' }}>Agen</option>
                                <option value="retail" {{ request('kualifikasi') == 'retail' ? 'selected' : '' }}>Retail</option>
                                <option value="pasar" {{ request('kualifikasi') == 'pasar' ? 'selected' : '' }}>Pasar Tradisional</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>

                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-slate-400 text-sm"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / ID..." class="w-full pl-9 pr-4 py-2 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm text-slate-700 dark:text-slate-200 font-medium">
                        </div>
                        
                        <button type="submit" class="hidden sm:block px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-lg text-sm font-bold transition-all">Cari</button>
                    </form>
                </div>

                <div class="overflow-x-auto custom-scrollbar flex-1">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-100 dark:bg-slate-900/80 text-slate-500 dark:text-slate-400 text-[11px] uppercase tracking-wider font-extrabold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nama Supplier</th>
                                <th class="px-6 py-4">Kualifikasi</th>
                                <th class="px-6 py-4">Kontak & Alamat</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                            @forelse($suppliers as $sup)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $sup->nama_supplier }}</div>
                                    <div class="text-[11px] text-slate-400 mt-1 uppercase tracking-wider">{{ $sup->no_supplier }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-extrabold uppercase tracking-wider rounded border border-blue-200 dark:border-blue-800/50">
                                        {{ $sup->kualifikasi }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if($sup->kontak_person || $sup->email || $sup->alamat)
                                        <div class="flex flex-col gap-1.5">
                                            @if($sup->kontak_person) 
                                                <div class="text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                                    <i class="fas fa-phone-alt text-emerald-500 w-3"></i> 
                                                    <span class="font-bold">{{ $sup->kontak_person }}</span>
                                                </div>
                                            @endif
                                            @if($sup->email) 
                                                <div class="text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                                    <i class="fas fa-envelope text-blue-500 w-3"></i> 
                                                    <span>{{ $sup->email }}</span>
                                                </div>
                                            @endif
                                            @if($sup->alamat) 
                                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-start gap-2 max-w-[200px] whitespace-normal">
                                                    <i class="fas fa-map-marker-alt text-red-400 w-3 mt-0.5"></i> 
                                                    <span class="leading-tight">{{ Str::limit($sup->alamat, 40) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum ada info kontak</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button type="button" 
                                            onclick="openModalEdit(this)" 
                                            data-id="{{ $sup->id }}"
                                            data-no="{{ $sup->no_supplier }}"
                                            data-nama="{{ $sup->nama_supplier }}"
                                            data-kualifikasi="{{ $sup->kualifikasi }}"
                                            data-kontak="{{ $sup->kontak_person }}"
                                            data-email="{{ $sup->email }}"
                                            data-alamat="{{ $sup->alamat }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-amber-100 hover:text-amber-600 dark:hover:bg-amber-900/30 dark:hover:text-amber-500 transition-all tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" onsubmit="return confirm('Yakin hapus supplier ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-500 transition-all tooltip" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                                        <i class="fas fa-search text-2xl"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Data Tidak Ditemukan</h3>
                                    <p class="text-sm text-slate-500">Coba gunakan kata kunci atau filter yang lain.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($suppliers) && $suppliers->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-[#1e293b]">
                    {{ $suppliers->appends(request()->query())->links() }}
                </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

<div id="modalEditSupplier" style="z-index: 9999;" class="fixed inset-0 flex items-center justify-center bg-black/60 hidden backdrop-blur-sm transition-opacity">
    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all border border-slate-100 dark:border-slate-700 flex flex-col">
        
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-amber-50 dark:bg-amber-900/20 shrink-0">
            <h3 class="font-bold text-lg text-amber-800 dark:text-amber-500 flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit Data Supplier
            </h3>
            <button type="button" onclick="closeModalEdit()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="formEditSupplier" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. Supplier / ID</label>
                    <input type="text" id="edit_no_supplier" name="no_supplier" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-white font-medium" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Supplier / PT</label>
                    <input type="text" id="edit_nama_supplier" name="nama_supplier" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-white font-medium" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kualifikasi</label>
                    <select id="edit_kualifikasi" name="kualifikasi" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-white font-medium" required>
                        <option value="produsen">Produsen / Pabrik</option>
                        <option value="distributor">Distributor Utama</option>
                        <option value="agen">Agen</option>
                        <option value="retail">Retail</option>
                        <option value="pasar">Pasar Tradisional</option>
                    </select>
                </div>

                <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider"><i class="fas fa-address-card mr-1"></i> Informasi Kontak</p>
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kontak Person / No. HP</label>
                        <input type="text" id="edit_kontak" name="kontak_person" class="w-full px-3 py-2 bg-white dark:bg-[#0f172a] border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 text-sm text-slate-800 dark:text-white">
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input type="email" id="edit_email" name="email" class="w-full px-3 py-2 bg-white dark:bg-[#0f172a] border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 text-sm text-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap</label>
                        <textarea id="edit_alamat" name="alamat" rows="2" class="w-full px-3 py-2 bg-white dark:bg-[#0f172a] border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 text-sm text-slate-800 dark:text-white"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeModalEdit()" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all text-sm">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold shadow-lg shadow-amber-500/30 transition-all text-sm flex items-center">
                        <i class="fas fa-save mr-2"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModalEdit(btnElement) {
        const id = btnElement.getAttribute('data-id');
        const no_sup = btnElement.getAttribute('data-no');
        const nama_sup = btnElement.getAttribute('data-nama');
        const kualifikasi = btnElement.getAttribute('data-kualifikasi');
        
        const kontak = btnElement.getAttribute('data-kontak');
        const email = btnElement.getAttribute('data-email');
        const alamat = btnElement.getAttribute('data-alamat');

        const modal = document.getElementById('modalEditSupplier');
        modal.classList.remove('hidden');
        
        document.getElementById('edit_no_supplier').value = no_sup;
        document.getElementById('edit_nama_supplier').value = nama_sup;
        document.getElementById('edit_kualifikasi').value = kualifikasi;
        
        document.getElementById('edit_kontak').value = kontak !== 'null' ? kontak : '';
        document.getElementById('edit_email').value = email !== 'null' ? email : '';
        document.getElementById('edit_alamat').value = alamat !== 'null' ? alamat : '';
        
        const form = document.getElementById('formEditSupplier');
        form.action = `/suppliers/${id}`;
    }

    function closeModalEdit() {
        const modal = document.getElementById('modalEditSupplier');
        modal.classList.add('hidden');
    }
</script>
@endsection