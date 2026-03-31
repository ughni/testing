@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto pb-10">

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                        <i class="fas fa-edit text-lg"></i>
                    </div>
                    Edit Master Produk
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 ml-13">
                    Perbarui detail barang, kategori, pemetaan supplier, atau ubah aturan Tipe Harga & Formula.
                </p>
            </div>
            <a href="{{ route('products.index') }}"
                class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold py-2.5 px-5 rounded-xl transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div
            class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 px-8 py-5 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-800 dark:text-slate-200">Form Edit Produk: <span
                        class="text-indigo-600 dark:text-indigo-400">{{ $product->product_name }}</span></h3>
            </div>

            <div class="p-8">
                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Produk <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-slate-200 font-medium"
                            required>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pemetaan Supplier
                            <span class="text-amber-500 font-normal text-xs">(Opsional)</span></label>
                        <div class="relative">
                            <select name="supplier_id"
                                class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-slate-200 appearance-none font-medium">
                                <option value="">-- Belum Dipetakan (Master Global) --</option>
                                @if (isset($suppliers))
                                    @foreach ($suppliers as $sup)
                                        <option value="{{ $sup->id }}"
                                            {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->nama_supplier }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i class="fas fa-chevron-down text-xs"></i></div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1.5 font-medium">Pilih nama supplier resmi jika barang ini
                            sudah terverifikasi asalnya. Jika belum, biarkan kosong.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="category"
                                    class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-slate-200 appearance-none font-medium"
                                    required>
                                    <option value="Produk Beli"
                                        {{ old('category', $product->category) == 'Produk Beli' ? 'selected' : '' }}>
                                        Produk Beli</option>
                                    <option value="Produk Jual"
                                        {{ old('category', $product->category) == 'Produk Jual' ? 'selected' : '' }}>
                                        Produk Jual</option>
                                    <option value="Bahan Olahan"
                                        {{ old('category', $product->category) == 'Bahan Olahan' ? 'selected' : '' }}>
                                        Bahan Olahan</option>
                                    <option value="Lainnya"
                                        {{ old('category', $product->category) == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                    </option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Satuan <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="unit"
                                    class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-slate-200 appearance-none font-medium"
                                    required>
                                    @foreach (['Kg', 'Gram', 'Liter', 'Pcs', 'Dus', 'Karung', 'Paket'] as $satuan)
                                        <option value="{{ $satuan }}"
                                            {{ old('unit', $product->unit) == $satuan ? 'selected' : '' }}>
                                            {{ $satuan }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tipe Harga (Rules)
                            <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="price_type" id="priceTypeSelect" onchange="togglePriceRules()"
                                class="w-full pl-4 pr-10 py-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800/50 rounded-xl focus:ring-2 focus:ring-indigo-500 text-indigo-800 dark:text-indigo-300 appearance-none font-bold"
                                required>
                                <option value="dynamic"
                                    {{ old('price_type', $product->price_type) == 'dynamic' ? 'selected' : '' }}>Dynamic
                                    Pricing (Fluktuatif)</option>
                                <option value="consignment"
                                    {{ old('price_type', $product->price_type) == 'consignment' ? 'selected' : '' }}>
                                    Consignment (Konsinyasi)</option>
                                <option value="HET"
                                    {{ old('price_type', $product->price_type) == 'HET' ? 'selected' : '' }}>HET (Harga
                                    Eceran Tertinggi)</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-indigo-500">
                                <i class="fas fa-chevron-down text-xs"></i></div>
                        </div>
                    </div>

                    <div id="het_wrapper"
                        class="hidden mb-6 p-5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl">
                        <label class="block text-sm font-bold text-amber-800 dark:text-amber-400 mb-2">
                            <i class="fas fa-shield-alt mr-1"></i> Batas Harga HET (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="het_price" id="het_price"
                            value="{{ old('het_price', $product->het_price) }}"
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-xl focus:ring-2 focus:ring-amber-500 font-bold text-amber-700 dark:text-amber-500">
                    </div>

                    <div id="consignment_wrapper"
                        class="hidden mb-6 p-5 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/50 rounded-xl">
                        <label class="block text-sm font-bold text-purple-800 dark:text-purple-400 mb-2">
                            <i class="fas fa-handshake mr-1"></i> Margin Titipan Supplier (%) <span
                                class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="consignment_margin" id="consignment_margin"
                            value="{{ old('consignment_margin', $product->consignment_margin) }}"
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-purple-300 dark:border-purple-700 rounded-xl focus:ring-2 focus:ring-purple-500 font-bold text-purple-700 dark:text-purple-500">
                    </div>

                    <div
                        class="mb-6 p-5 bg-slate-100 dark:bg-slate-800/50 border border-slate-300 dark:border-slate-700 rounded-xl shadow-inner">
                        <h4 class="text-sm font-black text-slate-800 dark:text-slate-300 mb-1 flex items-center"><i
                                class="fas fa-cogs mr-2 text-indigo-500"></i> Setting Harga Khusus</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-tight">Biarkan kosong jika ingin
                            menggunakan Formula Global. Jika diisi, sistem akan memprioritaskan angka ini.</p>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Buffer
                                    (%)</label>
                                <input type="number" step="1" name="buffer"
                                    value="{{ old('buffer', isset($product->buffer) && (float) $product->buffer < 1 ? (float) $product->buffer * 100 : $product->buffer) }}"
                                    placeholder="Cth: 5"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Markup
                                    Target (%)</label>
                                <input type="number" step="1" name="markup"
                                    value="{{ old('markup', isset($product->markup) && (float) $product->markup < 1 ? (float) $product->markup * 100 : $product->markup) }}"
                                    placeholder="Cth: 20"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Threshold
                                    (Unit)</label>
                                <input type="number" name="threshold"
                                    value="{{ old('threshold', $product->threshold) }}" placeholder="Cth: 20"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Yield
                                    (%)</label>
                                <input type="number" step="1" name="yield_percent"
                                    value="{{ old('yield_percent', isset($product->yield_percent) && (float) $product->yield_percent <= 1 ? (float) $product->yield_percent * 100 : $product->yield_percent) }}"
                                    placeholder="Cth: 100"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('products.index') }}"
                            class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all">Batal</a>
                        <button type="submit"
                            class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center">
                            <i class="fas fa-save mr-2"></i> Update Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePriceRules() {
            const val = document.getElementById('priceTypeSelect').value;
            const hetWrapper = document.getElementById('het_wrapper');
            const consWrapper = document.getElementById('consignment_wrapper');
            const hetInput = document.getElementById('het_price');
            const consInput = document.getElementById('consignment_margin');

            if (val === 'HET') {
                hetWrapper.classList.remove('hidden');
                hetInput.required = true;

                consWrapper.classList.add('hidden');
                consInput.required = false;
            } else if (val === 'consignment') {
                consWrapper.classList.remove('hidden');
                consInput.required = true;

                hetWrapper.classList.add('hidden');
                hetInput.required = false;
            } else {
                hetWrapper.classList.add('hidden');
                consWrapper.classList.add('hidden');

                hetInput.required = false;
                consInput.required = false;
            }
        }

        // Panggil saat halaman pertama kali diload biar inputnya nyesuain data DB
        document.addEventListener('DOMContentLoaded', function() {
            togglePriceRules();
        });
    </script>
@endsection
