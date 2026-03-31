@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4 shrink-0">
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                        <i class="fas fa-tasks text-lg"></i>
                    </div>
                    Purchase Plan (Rencana Pembelian)
                </h1>
                <p class="text-sm text-slate-500 dark:text-gray-400 mt-2 lg:ml-13 tracking-tight">
                    Kendali penuh admin untuk validasi order otomatis maupun ACC manual.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                <a href="{{ route('pricing.index') }}" class="px-5 py-2.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white font-bold rounded-xl shadow-sm transition-all flex items-center text-sm border border-indigo-200">
                    <i class="fas fa-calculator mr-2"></i> Ke Kalkulator Pricing Engine
                </a>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3 w-full lg:w-auto justify-end">
            
            <form action="{{ route('process_plan.index') }}" method="GET" class="w-full sm:w-48 m-0">
                <div class="relative w-full">
                    <select name="order_type" onchange="this.form.submit()" class="w-full pl-4 pr-10 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm appearance-none cursor-pointer">
                        <option value="all" {{ request('order_type') == 'all' ? 'selected' : '' }}>Semua Tipe Order</option>
                        <option value="otomatis" {{ request('order_type') == 'otomatis' ? 'selected' : '' }}>Order Otomatis (Urgent)</option>
                        <option value="manual" {{ request('order_type') == 'manual' ? 'selected' : '' }}>Order Manual (Aman)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </form>

            <div class="relative w-full sm:w-56">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="liveSearch" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm" placeholder="Cari produk / toko...">
            </div>

            <form action="{{ route('process_plan.auto_termurah') }}" method="POST" onsubmit="return confirm('Sistem akan menyeleksi harga termurah secara otomatis. Lanjutkan?');" class="w-full sm:w-auto m-0">
                @csrf
                <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm shadow-blue-600/30 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 transform">
                    <i class="fas fa-robot text-blue-200"></i> Auto Termurah
                </button>
            </form>

            <a href="{{ route('purchase-plan.print') }}" target="_blank" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 transform">
                <i class="fas fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-r-2xl shadow-sm flex items-center animate-pulse-short">
            <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
            <p class="font-bold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-2xl shadow-sm">
            <div class="flex items-center mb-2">
                <i class="fas fa-times-circle text-red-500 mr-3"></i>
                <p class="font-bold text-red-800 dark:text-red-300">Gagal menyimpan data:</p>
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 ml-7 font-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-3">
            <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 flex items-center">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center mr-3 border border-emerald-200">
                    <i class="fas fa-file-pdf text-sm"></i>
                </div>
                Supplier Terpilih (Siap Cetak PO)
            </h3>
            
            @if($approvedOffers->count() > 0)
                <form action="{{ route('process_plan.archive_all') }}" method="POST" onsubmit="return confirm('Yakin ingin menyelesaikan & mengarsipkan SEMUA barang di tabel ini? Pastikan Anda sudah mencetak PDF-nya!');">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-sm w-full sm:w-auto">
                        <i class="fas fa-archive"></i> Arsipkan Semua (Sapu Bersih)
                    </button>
                </form>
            @endif
        </div>
        
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-500/30 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-emerald-50/50 dark:bg-emerald-900/20 border-b border-emerald-100 dark:border-emerald-500/30">
                        <tr>
                            <th class="p-4 text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Tanggal</th>
                            <th class="p-4 text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Produk</th>
                            <th class="p-4 text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Supplier</th>
                            <th class="p-4 text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider text-center">Qty & Satuan</th>
                            <th class="p-4 text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider text-right">Total Biaya</th>
                            <th class="p-4 text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider text-center">Aksi (Setelah Cetak)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50 text-sm">
                        @forelse($approvedOffers as $s)
                        <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors">
                            <td class="p-4 text-gray-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($s->offer_date)->format('d M Y') }}</td>
                            <td class="p-4 font-bold text-indigo-600 dark:text-indigo-400">{{ $s->product_name }}</td>
                            <td class="p-4 font-bold text-gray-800 dark:text-slate-200">
                                <i class="fas fa-building mr-1.5 text-slate-400"></i> {{ $s->supplier_name }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-black border border-blue-100 dark:border-blue-800 shadow-sm">
                                    {{ $s->qty ?? 1 }} {{ $s->unit ?? 'Pcs' }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-black text-emerald-600 dark:text-emerald-400 text-base">
                                Rp {{ number_format((float)$s->price, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('process_plan.archive', $s->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 px-3 py-2 rounded-xl text-[10px] font-bold transition-all shadow-sm flex items-center gap-1.5 hover:-translate-y-0.5 transform" title="Tandai Selesai & Arsipkan">
                                            <i class="fas fa-check-double text-xs"></i> Selesai
                                        </button>
                                    </form>
                                    <form action="{{ route('process_plan.cancel', $s->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-white hover:bg-red-50 text-slate-600 hover:text-red-600 border border-slate-200 hover:border-red-200 dark:bg-slate-700 dark:hover:bg-red-900/30 dark:text-slate-300 px-3 py-2 rounded-xl text-[10px] font-bold transition-all shadow-sm flex items-center gap-1.5 hover:-translate-y-0.5 transform" title="Kembalikan ke antrean">
                                            <i class="fas fa-undo-alt text-xs"></i> Batal
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                <i class="fas fa-check-circle text-emerald-300 text-4xl mb-3 block opacity-60"></i>
                                <p class="text-gray-500 dark:text-slate-400 font-medium">Tabel bersih! Belum ada PO yang menunggu dicetak.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($approvedOffers, 'hasPages') && $approvedOffers->hasPages())
                <div class="px-6 py-4 border-t border-emerald-100 dark:border-emerald-500/30 bg-emerald-50/30 dark:bg-emerald-900/10 rounded-b-2xl">
                    {{ $approvedOffers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <div>
        <h3 class="text-lg font-bold text-amber-600 dark:text-amber-400 mb-4 flex items-center">
            <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/40 rounded-lg flex items-center justify-center mr-3 border border-amber-200">
                <i class="fas fa-hourglass-half text-sm"></i>
            </div>
            Antrean Menunggu Validasi Manager
        </h3>
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col">
            <div class="overflow-x-auto custom-scrollbar flex-1">
                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b dark:border-slate-700">
                        <tr>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal Masuk</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Produk & Urgensi</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Supplier</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Qty & Satuan</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Harga Total</th>
                            <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Aksi / Keputusan</th>
                        </tr>
                    </thead>
     <tbody id="pendingTableBody" class="divide-y divide-gray-50 dark:divide-slate-700/50 text-sm">
                        @forelse($pendingOffers as $s)
                            @php
                                $product = \App\Models\Product::where('product_name', $s->product_name)
                                            ->with(['dailyPricings' => function($q) {
                                                $q->orderBy('date_input', 'desc');
                                            }])->first();
                                
                                $latestInput = $product ? $product->dailyPricings->first() : null;
                                $isCritical = $latestInput && $latestInput->stock < 20;
                                $isHold = $s->status === 'hold';

                                // 🔥 LOGIKA SATPAM KONTRAK (SUDAH SEMPURNA 10.000%) 🔥
                                $namaSupplierBersih = trim($s->supplier_name);
                                $isContractExpired = true; // DEFAULT: TUDUH ILEGAL
                                
                                $supplierData = \App\Models\Supplier::where('nama_supplier', 'LIKE', '%' . $namaSupplierBersih . '%')->first();

                                if ($supplierData) {
                                    $latestContract = \App\Models\SupplierContract::where('supplier_id', $supplierData->id)
                                                                                  ->orderBy('created_at', 'desc')
                                                                                  ->first();
                                    
                                    if ($latestContract && !empty($latestContract->valid_until)) {
                                        $tanggalHabis = \Carbon\Carbon::parse($latestContract->valid_until)->endOfDay();
                                        if ($tanggalHabis->isFuture() || $tanggalHabis->isToday()) {
                                            $isContractExpired = false; // TERBUKTI AMAN & MASIH HIDUP!
                                        }
                                    }
                                }
                            @endphp
                            
                            <tr class="searchable-row transition-colors {{ $isCritical ? 'bg-red-50/20 dark:bg-red-900/10' : ($isHold ? 'bg-amber-50/30 dark:bg-amber-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50') }}"
                                data-name="{{ strtolower($s->product_name . ' ' . $s->supplier_name) }}">
                                
                                <td class="p-4 text-gray-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($s->offer_date)->format('d M Y') }}</td>
                                
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400 text-base">{{ $s->product_name }}</span>
                                        @if($isCritical)
                                            <span class="text-[10px] font-black text-red-500 flex items-center gap-1.5 mt-1.5 animate-pulse uppercase tracking-tighter">
                                                <i class="fas fa-exclamation-triangle text-red-400"></i> RE-STOCK SEGERA
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-4 font-bold text-gray-800 dark:text-slate-200">
                                    <i class="fas fa-building mr-1.5 text-slate-400 text-xs"></i> {{ $s->supplier_name }}
                                    @if($isHold)
                                        <br><span class="text-[10px] text-amber-500 uppercase tracking-widest font-black mt-1 inline-block"><i class="fas fa-clock mr-1 text-amber-400"></i> Ditunda</span>
                                    @endif
                                </td>

                                <td class="p-4 text-center">
                                    <span class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-black border border-blue-100 dark:border-blue-800 shadow-sm">
                                        {{ $s->qty ?? 1 }} {{ $s->unit ?? 'Pcs' }}
                                    </span>
                                </td>
                                
                                <td class="p-4 text-right font-black text-slate-700 dark:text-gray-300 text-base">
                                    Rp {{ number_format((float)$s->price, 0, ',', '.') }}
                                </td>
                                
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        
                                        @if($isContractExpired)
                                            <span class="bg-red-100 text-red-700 px-3 py-2 rounded-xl text-[10px] font-extrabold uppercase flex items-center gap-1 shadow-sm border border-red-200 cursor-not-allowed" title="Kontrak bermasalah/habis">
                                                <i class="fas fa-ban"></i> Kontrak Habis
                                            </span>
                                        @else
                                            <form action="{{ route('process_plan.approve', $s->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-xl text-[10px] font-bold transition-all shadow-md flex items-center gap-1.5 hover:-translate-y-0.5 transform">
                                                    <i class="fas fa-check text-xs"></i> Pilih / ACC
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" data-id="{{ $s->id }}" data-name="{{ $s->product_name }}" data-price="{{ $s->price ?? 0 }}" data-qty="{{ $s->qty ?? 1 }}" onclick="openModalEdit(this)" class="bg-amber-50 hover:bg-amber-100 text-amber-600 hover:text-amber-700 border border-amber-200 px-3 py-2 rounded-xl text-[10px] font-bold transition-all shadow-sm flex items-center gap-1.5">
                                            <i class="fas fa-edit text-xs"></i> Edit
                                        </button>

                                        <form action="{{ route('process_plan.reject', $s->id) }}" method="POST" onsubmit="return confirm('Hapus penawaran?');">
                                            @csrf
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-xl text-[10px] font-bold transition-all shadow-sm flex items-center gap-1.5">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-12 text-center text-gray-500 italic">Tabel Bersih</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($pendingOffers, 'hasPages') && $pendingOffers->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30 rounded-b-2xl">
                    {{ $pendingOffers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div> 

<div id="modalEdit" class="fixed inset-0 z-[9999] items-center justify-center bg-slate-900/80 hidden backdrop-blur-sm transition-opacity p-4">    
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-amber-50 dark:bg-amber-950/50">
            <h3 class="font-bold text-lg text-amber-900 dark:text-amber-200 flex items-center">
                <i class="fas fa-edit mr-2 text-amber-500"></i> Edit Pesanan Manager
            </h3>
            <button type="button" onclick="closeModalEdit()" class="text-gray-400 hover:text-red-500 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="formEditOffer" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Produk</label>
                <div id="edit_product_name" class="font-bold text-slate-800 dark:text-white text-lg bg-slate-50 dark:bg-slate-900 p-3 rounded-xl border border-slate-200 dark:border-slate-700"></div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Harga Total (Rp)</label>
                <input type="number" id="edit_price" name="price" required class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-white font-bold text-lg">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Quantity (Qty)</label>
                <input type="number" id="edit_qty" name="qty" required class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-white font-bold text-lg">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModalEdit()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all text-sm">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold shadow-lg shadow-amber-500/30 transition-all flex items-center text-sm">
                    <i class="fas fa-save mr-2"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearch');
        const rows = document.querySelectorAll('.searchable-row');
        function filterData() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                row.style.display = name.includes(query) ? '' : 'none';
            });
        }
        if(searchInput) searchInput.addEventListener('keyup', filterData);
    });

    function openModalEdit(btn) {
        const id = btn.getAttribute('data-id');
        const productName = btn.getAttribute('data-name');
        const price = btn.getAttribute('data-price');
        const qty = btn.getAttribute('data-qty');
        
        document.getElementById('edit_product_name').innerText = productName;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_qty').value = qty;
        
        let baseUrl = "{{ url('/process-plan/update-offer') }}";
        document.getElementById('formEditOffer').action = baseUrl + '/' + id;
        
        const modal = document.getElementById('modalEdit');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalEdit() {
        const modal = document.getElementById('modalEdit');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection