@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* UI/UX Select2 Mode Cerah & Malam yang Super Rapih */
        .select2-container--default .select2-selection--single {
            height: 48px !important; border-radius: 0.75rem !important; border: 1px solid #cbd5e1 !important;
            display: flex !important; align-items: center !important; padding-left: 0.5rem !important;
            font-weight: 700 !important; background-color: #f8fafc !important; color: #0f172a !important; transition: all 0.3s ease;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #4f46e5 !important; box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; right: 10px !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { color: #0f172a !important; padding-left: 0.5rem !important; }

        /* Dark Mode Select2 */
        .dark .select2-container--default .select2-selection--single {
            background-color: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important;
        }
        .dark .select2-container--default .select2-selection--single:focus,
        .dark .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #6366f1 !important; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #f8fafc !important; }
        .dark .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #94a3b8 transparent transparent transparent !important; }
        .dark .select2-container--default .select2-dropdown {
            background-color: #1e293b !important; border: 1px solid #334155 !important; border-radius: 0.75rem !important;
            overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }
        .dark .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #0f172a !important; color: #f8fafc !important; border: 1px solid #475569 !important;
            border-radius: 0.5rem !important; padding: 0.75rem 1rem !important;
        }
        .dark .select2-container--default .select2-results__option {
            color: #cbd5e1 !important; background-color: transparent !important; padding: 0.75rem 1rem !important; border-bottom: 1px solid #334155;
        }
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected],
        .dark .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #4f46e5 !important; color: white !important;
        }
    </style>

    <div class="max-w-7xl mx-auto pb-10">
        <div class="mb-8 flex items-center gap-3">
            <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-600/20 animate-pulse">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">Radar Gudang Kritis</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Alarm otomatis untuk barang yang stoknya menipis (Di bawah 20). Segera ajukan pembelian ke Manager!</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500 rounded-r-xl font-bold flex items-center">
                <i class="fas fa-check-circle mr-3 text-xl"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-yellow-50 text-yellow-700 border-l-4 border-yellow-500 rounded-r-xl font-bold flex items-center">
                <i class="fas fa-info-circle mr-3 text-xl"></i> {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-500 rounded-bl-full z-0 opacity-10"></div>

            <div class="bg-black px-6 py-5 border-b border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
                <div>
                    <h3 class="font-bold text-white flex items-center text-lg"><i class="fas fa-radar mr-2 text-red-400"></i> Pantauan Sisa Stok Kritis</h3>
                    <p class="text-xs text-gray-400 mt-1">Halaman ini akan KOSONG BERSIH jika semua stok gudang Anda aman.</p>
                </div>

                <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto items-center">
                    <form action="{{ route('restock.index') }}" method="GET" class="flex relative w-full md:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang kritis..." class="px-4 py-2.5 bg-slate-800 border border-slate-600 rounded-l-xl text-white text-sm focus:ring-2 focus:ring-red-500 w-full md:w-56 outline-none placeholder-slate-400 transition-all">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-r-xl transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                        @if (request('search'))
                            <a href="{{ route('restock.index') }}" class="absolute right-14 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-white">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto relative z-10">
                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-xs uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Nama Produk Kritis</th>
                            <th class="px-6 py-4 text-center">Sisa Stok</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi / Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($products as $prod)
                            @php
                                $stok = $prod->stock ?? 0;
                                $nama = $prod->product_name;

                                // 1. DATA DEFAULT AWAL (Dari Master Produk)
                                $satuanProduk = $prod->unit ?? 'Pcs';
                                $supplierName = '';
                                if (!empty($prod->supplier_id)) {
                                    $sup = \App\Models\Supplier::find($prod->supplier_id);
                                    if ($sup) {
                                        $supplierName = $sup->nama_supplier;
                                    }
                                }

                                // 2. SEDOT SEMUA JEJAK DARI RIWAYAT PEMBELIAN TERAKHIR!
                                $lastOffer = \App\Models\SupplierOffer::where('product_name', $nama)
                                    ->where('status', '!=', 'rejected') 
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                                if ($lastOffer) {
                                    // Tarik Harga Terakhir
                                    $lastPrice = $lastOffer->price;

                                    // Tarik Supplier Terakhir (Kalau ada)
                                    if (!empty($lastOffer->supplier_name)) {
                                        $supplierName = $lastOffer->supplier_name;
                                    }

                                    // Tarik Satuan Terakhir! 
                                    if (!empty($lastOffer->unit)) {
                                        $satuanProduk = $lastOffer->unit;
                                    }
                                } else {
                                    // Kalau belum pernah beli sama sekali
                                    $lastPrice = $prod->harga_beli ?? ($prod->price ?? ($prod->hpp ?? 0));
                                }
                            @endphp
                            <tr class="hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-800 dark:text-slate-200 text-base">{{ $nama }}</div>
                                    <div class="text-xs text-slate-500 mt-1"><i class="fas fa-info-circle"></i> Butuh segera direstock</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-extrabold text-2xl text-red-600 dark:text-red-400 animate-pulse">{{ $stok }}</span>
                                    <span class="text-xs font-bold text-slate-500 uppercase ml-1">{{ $satuanProduk }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-4 py-1.5 bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 text-[11px] font-extrabold uppercase rounded-full shadow-sm"><i class="fas fa-siren-on mr-1"></i> DARURAT</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                        onclick="openRestockModal('{{ addslashes($nama) }}', '{{ addslashes($supplierName) }}', '{{ $lastPrice }}', '{{ addslashes($satuanProduk) }}')"
                                        class="bg-slate-800 hover:bg-slate-900 text-white dark:bg-indigo-600 dark:hover:bg-indigo-700 text-xs font-bold px-5 py-2.5 rounded-xl shadow-md transition-all flex items-center justify-center mx-auto hover:-translate-y-0.5">
                                        Ajukan Pembelian <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/20">
                                    <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-500 shadow-inner">
                                        <i class="fas fa-shield-check text-4xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-1">Gudang Aman Terkendali!</h3>
                                    <p class="text-sm">Tidak ada barang yang menyentuh batas kritis (Di bawah 20). Anda bisa bersantai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#1e293b]">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="modalRestock" style="z-index: 9999;" class="fixed inset-0 items-center justify-center bg-slate-900/80 hidden backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 relative">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-indigo-50 dark:bg-indigo-900/20">
                <div>
                    <h3 class="font-extrabold text-lg text-indigo-900 dark:text-indigo-300 flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i> Form Pengajuan Pembelian
                    </h3>
                    <p class="text-xs text-indigo-600/70 dark:text-indigo-400 mt-1">Data akan dikirim ke antrean Manager (Purchase Plan)</p>
                </div>
                <button type="button" onclick="closeRestockModal()" class="text-slate-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-700 rounded-full shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('restock.process') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Barang yang Diajukan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-box text-slate-400"></i>
                        </div>
                        <input type="text" id="restock_product_name" name="product_name" class="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-900/50 border-0 rounded-xl font-bold text-lg cursor-not-allowed text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 focus:outline-none" readonly>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Supplier (Bisa Diganti) <span class="text-red-500">*</span></label>
                    <select id="restock_supplier" name="supplier_name" class="searchable-select w-full" required>
                        <option value="" disabled selected>-- Pilih Supplier --</option>
                        @foreach ($suppliers as $sup)
                            <option value="{{ $sup->nama_supplier }}">{{ $sup->nama_supplier }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1"><i class="fas fa-lightbulb text-amber-500"></i> Sistem otomatis memilih supplier terakhir. Silakan ubah jika perlu.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Estimasi Harga <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-bold">Rp</div>
                            <input type="number" id="restock_price" name="price" class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-0 rounded-xl text-slate-800 dark:text-white font-bold ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 transition-all" placeholder="0" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Jumlah Beli <span class="text-red-500">*</span></label>
                        <input type="number" name="qty" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-0 rounded-xl text-slate-800 dark:text-white font-bold ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-center transition-all" value="20" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">Satuan <span class="text-red-500">*</span></label>
                        <select id="restock_unit" name="unit" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-0 rounded-xl text-slate-800 dark:text-white font-bold ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-indigo-600 transition-all cursor-pointer" required>
                            <option value="" disabled selected>Pilih</option>
                            <option value="Kilogram (Kg)">Kilogram (Kg)</option>
                            <option value="Gram (g)">Gram (g)</option>
                            <option value="Liter (L)">Liter (L)</option>
                            <option value="Pieces (Pcs)">Pieces (Pcs)</option>
                            <option value="Karton / Dus">Karton / Dus</option>
                            <option value="Karung">Karung</option>
                            <option value="Paket">Paket</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" onclick="closeRestockModal()" class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center hover:-translate-y-0.5">
                        Kirim ke Manager <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Select2 dengan dropdownParent biar gak ngumpet di balik Modal!
            $('.searchable-select').select2({
                placeholder: "-- Ketik Nama Supplier --",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalRestock') // INI KUNCI BIAR DROPDOWN MUNCUL DI ATAS MODAL
            });
        });

        // FUNGSI SAKTI AUTO-FILL DATA LAMA
        function openRestockModal(productName, oldSupplier, oldPrice, oldUnit) {
            const modal = document.getElementById('modalRestock');

            // 1. Isi Text Input (Nama Barang & Harga)
            document.getElementById('restock_product_name').value = productName;
            document.getElementById('restock_price').value = oldPrice;

            // 2. Isi Select Option Biasa (Satuan)
            if (oldUnit) {
                document.getElementById('restock_unit').value = oldUnit;
            }

            // 3. Isi Select2 (Supplier Lama)
            if (oldSupplier) {
                // Cek apakah supplier lama ada di list
                if ($('#restock_supplier').find("option[value='" + oldSupplier + "']").length) {
                    $('#restock_supplier').val(oldSupplier).trigger('change');
                } else {
                    // Kalau supplier udah dihapus dari database tapi historinya ada, tambahin otomatis!
                    var newOption = new Option(oldSupplier, oldSupplier, true, true);
                    $('#restock_supplier').append(newOption).trigger('change');
                }
            } else {
                // Kalau belum pernah ada supplier, kosongin
                $('#restock_supplier').val(null).trigger('change');
            }

            // Tampilkan Modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRestockModal() {
            const modal = document.getElementById('modalRestock');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection