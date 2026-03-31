@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Custom CSS biar Select2 nyatu sama tema Tailwind lu */
        .select2-container .select2-selection--single {
            height: 46px !important;
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            display: flex;
            align-items: center;
            background-color: #f8fafc;
        }

        .dark .select2-container .select2-selection--single {
            background-color: rgba(15, 23, 42, 0.5) !important;
            border-color: #334155 !important;
        }

        .dark .select2-container .select2-selection__rendered {
            color: #e2e8f0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 10px !important;
        }

        .dark .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        .dark .select2-search__field {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: white !important;
        }

        .dark .select2-results__option[aria-selected="true"] {
            background-color: #334155 !important;
        }

        .dark .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb !important;
        }
    </style>

    <div class="max-w-[100%] mx-auto pb-10 px-4 sm:px-6 lg:px-8">

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                        <i class="fas fa-boxes text-lg"></i>
                    </div>
                    Master Data Produk
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                    Kelola daftar produk beli/jual, atur batas harga (HET/Konsinyasi), dan pantau stok harian.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button onclick="document.getElementById('importInput').click()"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-0.5">
                    <i class="fas fa-file-import mr-2"></i> Import Massal
                </button>

                <form id="importForm" action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data"
                    class="hidden">
                    @csrf
                    <input type="file" id="importInput" name="file_excel" accept=".xlsx, .xls, .csv"
                        onchange="document.getElementById('importForm').submit()">
                </form>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-r-2xl shadow-sm flex justify-between items-center pr-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                    <p class="font-bold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'"
                    class="text-emerald-500 hover:bg-emerald-100 dark:hover:bg-emerald-800 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-2xl shadow-sm text-red-800 dark:text-red-300">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                    <p class="font-bold uppercase text-sm tracking-wider">Gagal Menyimpan Data!</p>
                </div>
                <ul class="list-disc ml-9 text-sm font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden sticky top-6">
                    <div class="bg-black px-6 py-4 border-b border-slate-700">
                        <h5 class="font-bold text-white flex items-center"><i class="fas fa-plus mr-2 text-blue-400"></i>
                            Tambah Produk Baru</h5>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('products.store') ?? '#' }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Produk
                                    <span class="text-red-500">*</span></label>
                                <select name="product_name" class="searchable-select w-full" required>
                                    <option value="" disabled selected>-- Ketik untuk Mencari Produk --</option>
                                    @if (isset($approvedProducts) && count($approvedProducts) > 0)
                                        @foreach ($approvedProducts as $ap)
                                            <option value="{{ $ap->product_name }}">{{ $ap->product_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori
                                        <span class="text-red-500">*</span></label>
                                    @php
                                        $isHalamanBeli =
                                            request()->is('*beli*') || request('category') == 'Produk Beli';
                                        $isHalamanJual =
                                            request()->is('*jual*') || request('category') == 'Produk Jual';
                                    @endphp
                                    <div class="relative">
                                        <select name="category" required
                                            class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 appearance-none font-medium text-sm {{ $isHalamanBeli || $isHalamanJual ? 'bg-gray-200 dark:bg-slate-800 cursor-not-allowed opacity-80' : '' }}">
                                            @if ($isHalamanBeli)
                                                <option value="Produk Beli" selected>Produk Beli</option>
                                            @elseif($isHalamanJual)
                                                <option value="Produk Jual" selected>Produk Jual</option>
                                            @else
                                                <option value="" disabled selected>-- Pilih --</option>
                                                <option value="Produk Beli">Produk Beli</option>
                                                <option value="Produk Jual">Produk Jual</option>
                                                <option value="Bahan Olahan">Bahan Olahan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            @endif
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Satuan
                                        <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select name="unit"
                                            class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 appearance-none font-medium text-sm"
                                            required>
                                            <option value="" disabled selected>-- Pilih --</option>
                                            <option value="Kg">Kilogram (Kg)</option>
                                            <option value="Gram">Gram (g)</option>
                                            <option value="Liter">Liter (L)</option>
                                            <option value="Pcs">Pieces (Pcs)</option>
                                            <option value="Dus">Karton / Dus</option>
                                            <option value="Karung">Karung</option>
                                            <option value="Paket">Paket</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tipe Harga
                                    (Rules) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="price_type" id="priceTypeSelect" onchange="togglePriceRules()"
                                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 appearance-none font-bold"
                                        required>
                                        <option value="" disabled selected>-- Pilih Aturan Pricing --</option>
                                        <option value="dynamic" {{ old('price_type') == 'dynamic' ? 'selected' : '' }}>
                                            Dynamic Pricing (Fluktuatif)</option>
                                        <option value="consignment"
                                            {{ old('price_type') == 'consignment' ? 'selected' : '' }}>Consignment
                                            (Konsinyasi)</option>
                                        <option value="HET" {{ old('price_type') == 'HET' ? 'selected' : '' }}>HET (Harga
                                            Eceran Tertinggi)</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div id="het_wrapper"
                                class="hidden mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl shadow-inner transition-all duration-300">
                                <label class="block text-sm font-bold text-amber-800 dark:text-amber-400 mb-2">
                                    <i class="fas fa-shield-alt mr-1"></i> Batas Harga HET (Rp) <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="number" name="het_price" id="het_price"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-xl focus:ring-2 focus:ring-amber-500 font-bold text-amber-700 dark:text-amber-500"
                                    placeholder="Cth: 14000">
                                <p class="text-[10px] text-amber-600 dark:text-amber-500 mt-1.5 font-medium leading-tight">
                                    Sistem tidak akan menghitung harga jual di atas batas ini.</p>
                            </div>

                            <div id="consignment_wrapper"
                                class="hidden mb-6 p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/50 rounded-xl shadow-inner transition-all duration-300">
                                <label class="block text-sm font-bold text-purple-800 dark:text-purple-400 mb-2">
                                    <i class="fas fa-handshake mr-1"></i> Margin Titipan Supplier (%) <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="consignment_margin" id="consignment_margin"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-purple-300 dark:border-purple-700 rounded-xl focus:ring-2 focus:ring-purple-500 font-bold text-purple-700 dark:text-purple-500"
                                    placeholder="Cth: 0.10">
                                <p
                                    class="text-[10px] text-purple-600 dark:text-purple-500 mt-1.5 font-medium leading-tight">
                                    Ketik desimal (Contoh: 0.10 untuk margin 10%).</p>
                            </div>

                            <div
                                class="mb-6 p-4 bg-slate-100 dark:bg-slate-800/50 border border-slate-300 dark:border-slate-700 rounded-xl">
                                <h4 class="text-xs font-black text-slate-800 dark:text-slate-300 mb-1 flex items-center"><i
                                        class="fas fa-cogs mr-1.5 text-slate-500"></i> Setting Harga Khusus</h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-3 leading-tight">Biarkan kosong
                                    jika ingin menggunakan Formula Global.</p>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Buffer
                                            (%)</label>
                                        <input type="number" step="0.1" name="buffer" placeholder="Cth: 5"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Markup
                                            Target (%)</label>
                                        <input type="number" step="1" name="markup" value="{{ old('markup') }}"
                                            placeholder="Cth: 20"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Threshold
                                            (Unit)</label>
                                        <input type="number" name="threshold" placeholder="Cth: 20"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-700 dark:text-slate-400 mb-1">Yield
                                            (%)</label>
                                        <input type="number" step="0.1" name="yield_percent" placeholder="Cth: 100"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-xs font-bold">
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:-translate-y-0.5 mt-2">
                                <i class="fas fa-save mr-2"></i> Simpan Master Produk
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col h-full">
                    <div
                        class="bg-black px-6 py-4 border-b border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4 rounded-t-2xl">
                        <div class="flex items-center">
                            <h5 class="font-bold text-white flex items-center whitespace-nowrap"><i
                                    class="fas fa-chart-line mr-2 text-blue-500"></i> Monitor Produk</h5>
                            <span
                                class="ml-3 bg-blue-900/40 text-blue-400 text-xs font-bold px-3 py-1 rounded-full">{{ isset($products) ? $products->total() : 0 }}
                                Item</span>
                        </div>

                        <button onclick="toggleFilter()"
                            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold rounded-lg transition-all flex items-center text-xs shadow-sm border border-slate-600">
                            <i class="fas fa-filter mr-2 text-blue-400"></i> Filter Data
                        </button>
                    </div>

                    <div id="filterBox"
                        class="{{ request()->hasAny(['search', 'category']) ? 'block' : 'hidden' }} bg-slate-50 dark:bg-slate-800/80 p-5 border-b border-slate-200 dark:border-slate-700 transition-all">
                        <form action="{{ route('products.index') }}" method="GET"
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Cari Nama
                                    Produk</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i
                                            class="fas fa-search"></i></span>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Ketik nama produk..."
                                        class="w-full pl-9 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-sm dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Kategori
                                    Produk</label>
                                <div class="relative">
                                    <select name="category"
                                        class="w-full pl-4 pr-10 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-sm dark:text-white appearance-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Semua Kategori --</option>
                                        <option value="Produk Beli"
                                            {{ request('category') == 'Produk Beli' ? 'selected' : '' }}>Produk Beli
                                        </option>
                                        <option value="Produk Jual"
                                            {{ request('category') == 'Produk Jual' ? 'selected' : '' }}>Produk Jual
                                        </option>
                                        <option value="Bahan Olahan"
                                            {{ request('category') == 'Bahan Olahan' ? 'selected' : '' }}>Bahan Olahan
                                        </option>
                                        <option value="Lainnya" {{ request('category') == 'Lainnya' ? 'selected' : '' }}>
                                            Lainnya</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 mt-2 md:mt-0">
                                <a href="{{ route('products.index') }}"
                                    class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-lg text-sm hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">Reset</a>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg text-sm shadow-sm hover:bg-blue-700 transition-colors"><i
                                        class="fas fa-search mr-1"></i> Terapkan</button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar relative flex-1">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead
                                class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-xs uppercase font-bold tracking-wider border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">Nama Produk</th>
                                    <th class="px-6 py-4 text-center border-l dark:border-slate-700">Tipe Harga</th>
                                    <th class="px-6 py-4 text-center border-l dark:border-slate-700">Stok Fisik</th>
                                    <th class="px-6 py-4 text-center border-l dark:border-slate-700">Satuan</th>
                                    <th class="px-6 py-4 text-center border-l dark:border-slate-700">Trend (HPP)</th>
                                    <th class="px-6 py-4 text-center border-l dark:border-slate-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/60">
                                @if (isset($products) && count($products) > 0)
                                    @foreach ($products as $p)
                                        @php
                                            $latest = $p->dailyPricings ? $p->dailyPricings->first() : null;
                                            $previous = $p->dailyPricings ? $p->dailyPricings->skip(1)->first() : null;

                                            $trendIcon =
                                                '<span class="text-slate-400 font-bold"><i class="fas fa-minus"></i> Tetap</span>';
                                            if ($latest && $previous) {
                                                if ($latest->hpp > $previous->hpp) {
                                                    $trendIcon =
                                                        '<span class="text-red-500 font-bold"><i class="fas fa-arrow-up"></i> Naik</span>';
                                                } elseif ($latest->hpp < $previous->hpp) {
                                                    $trendIcon =
                                                        '<span class="text-emerald-500 font-bold"><i class="fas fa-arrow-down"></i> Turun</span>';
                                                }
                                            }

                                            $isCritical = $latest && $latest->stock < ($p->threshold ?? 20);
                                        @endphp

                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">

                                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                                {{ $p->product_name }}<br>
                                                <span
                                                    class="text-[10px] font-normal text-slate-400 uppercase tracking-wider bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded mt-1 inline-block">{{ $p->category ?? 'Umum' }}</span>
                                            </td>

                                            <td class="px-6 py-4 text-center border-l dark:border-slate-700/50">
                                                @if ($p->price_type == 'dynamic')
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold tracking-wider">DYNAMIC</span>
                                                @elseif($p->price_type == 'consignment')
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-[10px] font-bold tracking-wider">CONSIGNMENT</span>
                                                @elseif($p->price_type == 'HET')
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold tracking-wider">HET
                                                        LIMIT</span>
                                                @else
                                                    <span class="text-xs text-slate-400">-</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-4 text-center border-l dark:border-slate-700/50">
                                                @if ($latest)
                                                    <div
                                                        class="{{ $isCritical ? 'text-red-600 font-extrabold animate-pulse' : 'text-slate-700 dark:text-slate-300 font-bold' }}">
                                                        {{ $latest->stock }}
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">Belum Input</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-4 text-center border-l dark:border-slate-700/50">
                                                <span
                                                    class="px-3 py-1 bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 text-[11px] font-black uppercase tracking-widest rounded-lg border border-slate-200 dark:border-slate-700/50">
                                                    {{ $p->unit ?? '-' }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-center border-l dark:border-slate-700/50">
                                                @if ($latest)
                                                    {!! $trendIcon !!}
                                                    <div class="text-xs text-slate-500 font-medium mt-1">Rp
                                                        {{ number_format($latest->hpp, 0, ',', '.') }}</div>
                                                @else
                                                    <span class="text-xs text-slate-400">-</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-4 text-center border-l dark:border-slate-700/50">
                                                <div class="flex items-center justify-center gap-3">
                                                    <a href="{{ route('products.edit', $p->id) ?? '#' }}"
                                                        class="text-slate-400 hover:text-blue-500 transition-colors tooltip"
                                                        title="Edit Produk">
                                                        <i class="fas fa-edit text-lg"></i>
                                                    </a>
                                                    <form action="{{ route('products.destroy', $p->id) ?? '#' }}"
                                                        method="POST" class="inline-block"
                                                        onsubmit="return confirm('AWAS! Menghapus produk akan merusak perhitungan histori harga. Lanjutkan?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-slate-400 hover:text-red-500 transition-colors tooltip"
                                                            title="Hapus Permanen">
                                                            <i class="fas fa-trash text-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <i
                                                class="fas fa-boxes text-3xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                                            <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada data produk
                                                master.<br>Silakan tambah produk melalui form di samping.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (isset($products) && $products->hasPages())
                        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1e293b]">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Init pencarian form tambah produk
            $('.searchable-select').select2({
                placeholder: "-- Ketik untuk Mencari Produk --",
                allowClear: true,
                width: '100%',
                tags: true
            });
        });

        // Menampilkan & menyembunyikan aturan harga (HET/Konsinyasi)
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
                consInput.value = '';
            } else if (val === 'consignment') {
                consWrapper.classList.remove('hidden');
                consInput.required = true;
                hetWrapper.classList.add('hidden');
                hetInput.required = false;
                hetInput.value = '';
            } else {
                hetWrapper.classList.add('hidden');
                consWrapper.classList.add('hidden');
                hetInput.required = false;
                consInput.required = false;
                hetInput.value = '';
                consInput.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            togglePriceRules();
        });

        // Fungsi Buka Tutup Kotak Filter
        function toggleFilter() {
            const box = document.getElementById('filterBox');
            if (box.classList.contains('hidden')) {
                box.classList.remove('hidden');
                box.classList.add('block');
            } else {
                box.classList.remove('block');
                box.classList.add('hidden');
            }
        }
    </script>
@endsection
