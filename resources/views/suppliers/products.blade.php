@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { height: 42px !important; border-radius: 0.75rem !important; border: none !important; display: flex !important; align-items: center !important; padding-left: 2rem !important; font-weight: 600 !important; background-color: transparent !important; color: #334155 !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px !important; right: 10px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #334155 !important; padding-left: 0.5rem !important; }
    .dark .select2-container--default .select2-selection--single { color: #cbd5e1 !important; }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #cbd5e1 !important; }
    .dark .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #cbd5e1 transparent transparent transparent !important; }
    .select2-container--default .select2-dropdown { background-color: #ffffff !important; border: 1px solid #cbd5e1 !important; border-radius: 0.75rem !important; }
    .dark .select2-container--default .select2-dropdown { background-color: #1e293b !important; border: 1px solid #475569 !important; color: #e2e8f0 !important; }
    .dark .select2-container--default .select2-search--dropdown .select2-search__field { background-color: #0f172a !important; color: #e2e8f0 !important; border: 1px solid #475569 !important; border-radius: 0.5rem !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #4f46e5 !important; color: white !important; }
</style>

<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-handshake text-xl"></i>
                </div>
                Data Produk per Supplier
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-16">
                Kelola aturan kontrak harga jual (Dynamic, Consignment, HET, Fixed) dengan supplier.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-r-xl font-bold flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#1e293b] p-4 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 mb-8">
        <form id="filterForm" action="{{ route('suppliers.products') }}" method="GET" class="flex flex-col lg:flex-row gap-3 w-full items-center">
            
            <div class="relative w-full lg:w-1/4">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                    <i class="fas fa-tags text-rose-400"></i>
                </div>
                <select name="category" class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-rose-500 rounded-xl cursor-pointer text-slate-700 dark:text-slate-300 font-bold transition-all relative z-0">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Produk Beli" {{ request('category') == 'Produk Beli' ? 'selected' : '' }}>Produk Beli</option>
                    <option value="Produk Jual" {{ request('category') == 'Produk Jual' ? 'selected' : '' }}>Produk Jual</option>
                    <option value="Bahan Olahan" {{ request('category') == 'Bahan Olahan' ? 'selected' : '' }}>Bahan Olahan</option>
                </select>
            </div>

            <div class="relative w-full lg:w-1/4">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                    <i class="fas fa-building text-emerald-400"></i>
                </div>
                <select name="search_supplier" class="w-full pl-10 pr-8 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 rounded-xl cursor-pointer text-slate-700 dark:text-slate-300 font-bold transition-all relative z-0">
                    <option value="">-- Semua Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->nama_supplier }}" {{ request('search_supplier') == $sup->nama_supplier ? 'selected' : '' }}>
                            {{ Str::limit($sup->nama_supplier, 25) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative w-full lg:w-1/3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                    <i class="fas fa-box text-amber-400"></i>
                </div>
                <select name="search_product" class="searchable-select w-full" style="width: 100%;">
                    <option value="">-- Cari Nama Produk --</option>
                    @if(isset($allProductsList))
                        @foreach($allProductsList as $p)
                            <option value="{{ $p->product_name }}" {{ request('search_product') == $p->product_name ? 'selected' : '' }}>
                                {{ $p->product_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="flex items-center gap-2 w-full lg:w-auto mt-3 lg:mt-0 flex-1">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i> Cari
                </button>
                
                @if(request()->hasAny(['search_supplier', 'search_product', 'category']) && (request('search_supplier') != '' || request('search_product') != '' || request('category') != ''))
                    <a href="{{ route('suppliers.products') }}" class="px-4 py-2.5 bg-red-50 text-red-500 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-all font-bold tooltip" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="bg-indigo-50/50 dark:bg-indigo-900/20 px-6 py-4 border-b border-indigo-100 dark:border-indigo-500/30 flex justify-between items-center">
            <h3 class="font-bold text-indigo-800 dark:text-indigo-400 flex items-center"><i class="fas fa-clipboard-list mr-2"></i> Daftar Kontrak Aturan Harga</h3>
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50/80 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Info Produk</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Supplier</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Tipe Harga</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Aturan Kontrak / Harga Jual</th>
                        <th class="p-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($products as $product)
                        @php
                            $ptype = strtolower($product->price_type ?? 'dynamic');
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                            
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 shrink-0 border border-slate-200 dark:border-slate-700">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="flex flex-col items-start">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-base mb-1">{{ $product->product_name }}</h4>
                                        <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[9px] font-black rounded uppercase tracking-widest border border-rose-200 dark:border-rose-800/50">
                                            {{ $product->category ?? 'Umum' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-5">
                                @if($product->supplier)
                                    <div class="font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                        <i class="fas fa-building text-emerald-500 text-xs"></i> {{ $product->supplier->nama_supplier }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-red-200 dark:border-red-800/50">
                                        <i class="fas fa-exclamation-triangle"></i> Non-Supplier
                                    </div>
                                @endif
                            </td>

                            <td class="p-5 text-center">
                                @if($ptype == 'dynamic')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full text-[10px] font-black uppercase tracking-widest">DYNAMIC</span>
                                @elseif($ptype == 'consignment')
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full text-[10px] font-black uppercase tracking-widest">CONSIGNMENT</span>
                                @elseif($ptype == 'het')
                                    <span class="px-3 py-1 bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 rounded-full text-[10px] font-black uppercase tracking-widest">HET</span>
                                @else
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-[10px] font-black uppercase tracking-widest">FIXED</span>
                                @endif
                            </td>

                            <td class="p-5 text-center">
                                @if($ptype == 'dynamic')
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 italic">Otomatis ikut Pricing Engine</span>
                                @elseif($ptype == 'consignment')
                                    <div class="font-black text-purple-700 dark:text-purple-400 text-sm">
                                        Margin Supplier: {{ $product->consignment_margin ?? 0 }}%
                                    </div>
                                @elseif($ptype == 'het')
                                    <div class="font-black text-rose-700 dark:text-rose-400 text-sm">
                                        Max Jual: Rp {{ number_format((float)($product->het_price ?? 0), 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="font-black text-amber-700 dark:text-amber-400 text-sm">
                                        Lock Harga Jual: Rp {{ number_format((float)($product->selling_price_fixed ?? 0), 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>

                            <td class="p-5 text-center">
                                <button type="button" 
                                    onclick="openContractModal('{{ $product->id }}', '{{ addslashes($product->product_name) }}', '{{ $ptype }}', '{{ $product->het_price ?? 0 }}', '{{ $product->consignment_margin ?? 0 }}', '{{ $product->selling_price_fixed ?? 0 }}')"
                                    class="bg-slate-100 hover:bg-indigo-600 text-slate-600 hover:text-white dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-indigo-600 text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm">
                                    <i class="fas fa-edit mr-1"></i> Atur Kontrak
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center bg-slate-50/30 dark:bg-slate-900/10">
                                <i class="fas fa-search text-5xl text-slate-300 dark:text-slate-600 mb-4 block"></i>
                                <h3 class="text-lg font-black text-slate-600 dark:text-slate-300 mb-1">Data Tidak Ditemukan</h3>
                                <p class="text-slate-500 text-sm">Coba ubah filter kategori, supplier, atau produk di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/30">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<div id="modalContract" style="z-index: 9999;" class="fixed inset-0 items-center justify-center bg-slate-900/80 hidden backdrop-blur-sm transition-opacity p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md mx-auto overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 relative">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-indigo-50 dark:bg-indigo-900/20">
            <div>
                <h3 class="font-extrabold text-lg text-indigo-900 dark:text-indigo-300 flex items-center">
                    <i class="fas fa-handshake mr-2"></i> Atur Kontrak Harga
                </h3>
                <p id="contract_product_name" class="text-xs font-bold text-indigo-600/70 dark:text-indigo-400 mt-1">Nama Produk</p>
            </div>
            <button type="button" onclick="closeContractModal()" class="text-slate-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-700 rounded-full shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="contractForm" action="" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Tipe Harga (Price Type) <span class="text-red-500">*</span></label>
                <select id="inp_price_type" name="price_type" onchange="toggleInputs()" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-800 dark:text-white font-bold focus:ring-2 focus:ring-indigo-600 transition-all cursor-pointer" required>
                    <option value="dynamic">Dynamic (Ikut Pricing Engine)</option>
                    <option value="consignment">Consignment (Barang Titipan Konsinyasi)</option>
                    <option value="het">HET (Harga Eceran Tertinggi)</option>
                    <option value="fixed">Fixed (Harga Jual Dikunci Mutlak)</option>
                </select>
            </div>

            <div id="box_het" class="mb-5 hidden">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Harga Maksimal (HET) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-bold">Rp</div>
                    <input type="number" id="inp_het_price" name="het_price" class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-800 dark:text-white font-bold focus:ring-2 focus:ring-indigo-600 transition-all" placeholder="0">
                </div>
            </div>

            <div id="box_consignment" class="mb-5 hidden">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Margin Supplier <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" id="inp_consignment_margin" name="consignment_margin" step="0.1" class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-800 dark:text-white font-bold focus:ring-2 focus:ring-indigo-600 transition-all" placeholder="0">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500 font-bold">%</div>
                </div>
            </div>

            <div id="box_fixed" class="mb-5 hidden">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Harga Jual Mutlak (Fixed) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-bold">Rp</div>
                    <input type="number" id="inp_selling_price_fixed" name="selling_price_fixed" class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-800 dark:text-white font-bold focus:ring-2 focus:ring-indigo-600 transition-all" placeholder="0">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeContractModal()" class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Batal</button>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.searchable-select').select2({ placeholder: "-- Cari Nama Produk --", allowClear: true, width: '100%' });
    });

    function openContractModal(id, name, type, het, margin, fixed) {
        document.getElementById('contract_product_name').innerText = name;
        document.getElementById('contractForm').action = "/suppliers/products/" + id + "/contract";
        
        // Set Nilai Dropdown & Text Input
        document.getElementById('inp_price_type').value = type ? type : 'dynamic';
        document.getElementById('inp_het_price').value = het;
        document.getElementById('inp_consignment_margin').value = margin;
        document.getElementById('inp_selling_price_fixed').value = fixed;
        
        // Panggil fungsi toggle buat nampilin box yang bener
        toggleInputs();

        const modal = document.getElementById('modalContract');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function toggleInputs() {
        const type = document.getElementById('inp_price_type').value;
        const bHet = document.getElementById('box_het');
        const bCon = document.getElementById('box_consignment');
        const bFix = document.getElementById('box_fixed');

        // Sembunyiin semua dulu
        bHet.classList.add('hidden');
        bCon.classList.add('hidden');
        bFix.classList.add('hidden');

        // Munculin sesuai pilihan
        if(type === 'het') bHet.classList.remove('hidden');
        if(type === 'consignment') bCon.classList.remove('hidden');
        if(type === 'fixed') bFix.classList.remove('hidden');
    }

    function closeContractModal() {
        const modal = document.getElementById('modalContract');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection