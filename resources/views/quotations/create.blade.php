@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Styling Select2 Default */
    .select2-container--default .select2-selection--single { height: 48px !important; border-radius: 0.75rem !important; border: 1px solid #cbd5e1 !important; display: flex !important; align-items: center !important; padding-left: 0.5rem !important; font-weight: 700 !important; background-color: white !important; color: #0f172a !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; right: 10px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #0f172a !important; padding-left: 0.5rem !important; }

    /* Styling Select2 Dark Mode */
    .dark .select2-container--default .select2-selection--single { background-color: #1e293b !important; border-color: #475569 !important; color: #e2e8f0 !important; }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #e2e8f0 !important; }
    .dark .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #e2e8f0 transparent transparent transparent !important; }
    .select2-container--default .select2-dropdown { background-color: #0f172a !important; border: 1px solid #475569 !important; border-radius: 0.75rem !important; color: #e2e8f0 !important; }
    .select2-container--default .select2-search--dropdown .select2-search__field { background-color: #1e293b !important; color: #e2e8f0 !important; border: 1px solid #475569 !important; border-radius: 0.5rem !important; padding: 0.75rem 1rem !important; }
    .select2-container--default .select2-search--dropdown { background-color: transparent !important; padding: 0.75rem !important; }
    .select2-container--default .select2-results__option { color: #e2e8f0 !important; background-color: transparent !important; padding: 0.75rem 1rem !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected], .select2-container--default .select2-results__option[aria-selected=true] { background-color: #1e40af !important; color: white !important; }
</style>

<div class="max-w-6xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="w-12 h-12 bg-blue-600 rounded-xl flex shrink-0 items-center justify-center text-white shadow-lg shadow-blue-600/20">
            <i class="fas fa-envelope-open-text text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">Input Penawaran Sales (Quotation)</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Masukan banyak harga tawaran sekaligus dari satu supplier.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-r-xl font-bold flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="bg-black px-4 md:px-6 py-4 border-b border-slate-700 flex flex-col md:flex-row justify-between items-center gap-3">
            <h3 class="font-bold text-white flex items-center w-full md:w-auto"><i class="fas fa-list mr-2 text-blue-400"></i> Form Penawaran Harga Baru</h3>
            <button type="button" id="addRowBtn" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors shadow-lg shadow-blue-600/30 flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i> Tambah Baris
            </button>
        </div>
        
        <div class="p-4 md:p-8">
            <form action="{{ route('quotations.store') }}" method="POST" id="quotationForm">
                @csrf
                
                <div class="mb-8 p-4 md:p-5 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Supplier (Pabrik) <span class="text-red-500">*</span></label>
                    <select name="supplier_name" class="searchable-select w-full" required>
                        <option value="" disabled selected>-- Ketik atau Pilih Nama Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->nama_supplier }}">{{ $sup->nama_supplier }} ({{ $sup->no_supplier }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="productRowsContainer">
                    <div class="product-row grid grid-cols-12 gap-3 mb-6 md:mb-4 items-end pb-6 md:pb-4 border-b border-slate-200 dark:border-slate-700/50">
                        
                        <div class="col-span-12 md:col-span-4">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="product_name[]" placeholder="Contoh: Agar-agar Coklat 10g" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all" required>
                        </div>
                        
                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Harga Tawaran (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price[]" placeholder="15000" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all" required>
                        </div>
                        
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Qty</label>
                            <input type="number" name="qty[]" placeholder="10" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all">
                        </div>
                        
                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <select name="unit[]" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="Kilogram (Kg)">Kilogram (Kg)</option>
                                <option value="Gram (g)">Gram (g)</option>
                                <option value="Liter (L)">Liter (L)</option>
                                <option value="Pieces (Pcs)">Pieces (Pcs)</option>
                                <option value="Karton / Dus">Karton / Dus</option>
                                <option value="Karung">Karung</option>
                                <option value="Paket">Paket</option>
                            </select>
                        </div>
                        
                        <div class="col-span-2 md:col-span-1 flex justify-center pb-1 md:pb-2">
                            <button type="button" class="remove-row-btn w-full md:w-auto py-2.5 text-slate-300 bg-slate-100 dark:bg-slate-800 md:bg-transparent rounded-lg cursor-not-allowed flex justify-center items-center" disabled>
                                <i class="fas fa-trash text-lg"></i>
                            </button>
                        </div>

                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 rounded-xl shadow-lg shadow-blue-600/30 transition-all text-sm md:text-lg mt-4">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Semua ke Antrean Bos
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Inisialisasi Select2
        $('.searchable-select').select2({
            placeholder: "-- Ketik atau Pilih Nama Supplier --",
            allowClear: true,
            width: '100%' 
        });

        const container = document.getElementById('productRowsContainer');
        const addBtn = document.getElementById('addRowBtn');

        // Fungsi Tambah Baris (Dengan Grid Responsif)
        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            // Class grid yang sama persis dengan baris pertama biar layoutnya kembar
            row.className = 'product-row grid grid-cols-12 gap-3 mb-6 md:mb-4 items-end pb-6 md:pb-4 border-b border-slate-200 dark:border-slate-700/50';
            row.innerHTML = `
                <div class="col-span-12 md:col-span-4">
                    <label class="block md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="product_name[]" placeholder="Nama Barang..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all" required>
                </div>
                
                <div class="col-span-12 md:col-span-3">
                    <label class="block md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Harga Tawaran (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price[]" placeholder="Harga (Rp)..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all" required>
                </div>
                
                <div class="col-span-4 md:col-span-2">
                    <label class="block md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Qty</label>
                    <input type="number" name="qty[]" placeholder="Qty..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all">
                </div>
                
                <div class="col-span-6 md:col-span-2">
                    <label class="block md:hidden text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Satuan <span class="text-red-500">*</span></label>
                    <select name="unit[]" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-800 dark:text-slate-200 transition-all" required>
                        <option value="" disabled selected>-- Pilih --</option>
                        <option value="Kilogram (Kg)">Kilogram (Kg)</option>
                        <option value="Gram (g)">Gram (g)</option>
                        <option value="Liter (L)">Liter (L)</option>
                        <option value="Pieces (Pcs)">Pieces (Pcs)</option>
                        <option value="Karton / Dus">Karton / Dus</option>
                        <option value="Karung">Karung</option>
                        <option value="Paket">Paket</option>
                    </select>
                </div>
                
                <div class="col-span-2 md:col-span-1 flex justify-center pb-1 md:pb-2">
                    <button type="button" class="remove-row-btn w-full md:w-auto py-2.5 text-red-500 hover:text-white hover:bg-red-500 bg-red-50 dark:bg-red-900/30 md:bg-transparent md:hover:bg-transparent md:dark:bg-transparent rounded-lg transition-all tooltip flex justify-center items-center" title="Hapus Baris">
                        <i class="fas fa-trash text-lg"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
        });

        // Fungsi Hapus Baris
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row-btn') && !e.target.closest('.remove-row-btn').disabled) {
                e.target.closest('.product-row').remove();
            }
        });
    });
</script>
@endsection