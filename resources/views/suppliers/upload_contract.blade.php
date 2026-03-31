@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto pb-10">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-file-upload text-lg"></i>
                </div>
                Upload / Update Kontrak
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                Unggah dokumen kontrak baru atau perbarui kontrak lama. Sistem akan melakukan <b>Auto-Versioning</b>.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/30 border-l-4 border-emerald-500 rounded-r-xl flex items-center justify-between">
            <div class="flex items-center text-emerald-700 dark:text-emerald-400 font-medium">
                <i class="fas fa-check-circle text-lg mr-3"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-r-xl">
            <div class="flex items-center text-red-700 dark:text-red-400 font-bold mb-2">
                <i class="fas fa-exclamation-triangle text-lg mr-3"></i> Terjadi Kesalahan:
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 ml-7">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        
        <div class="p-8">
            <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Supplier <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="supplier_id" class="w-full pl-4 pr-10 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-slate-200 appearance-none font-medium" required>
                            <option value="">-- Cari dan Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i class="fas fa-chevron-down text-xs"></i></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2"><i class="fas fa-info-circle"></i> Jika supplier sudah memiliki kontrak, mengunggah di sini akan otomatis membuat <b>Versi Baru</b>.</p>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">File Kontrak (PDF/JPG) <span class="text-red-500">*</span></label>
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-xl hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors bg-slate-50 dark:bg-slate-900/50">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-400 mb-3 block"></i>
                            <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-slate-800 rounded-md font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2 py-1">
                                    <span>Pilih File</span>
                                    <input id="file-upload" name="contract_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                                </label>
                                <p class="pl-1 pt-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-500">
                                PDF, JPG, PNG maksimal 5MB.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Berlaku Sampai (Expired Date) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="date" name="valid_until" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-800 dark:text-slate-200 font-medium transition-all">
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <i class="fas fa-calendar-alt text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2"><i class="fas fa-exclamation-circle text-amber-500"></i> Cek dokumen fisik kontrak dan masukkan tanggal berakhirnya kontrak ini.</p>
                </div>

                <div class="p-4 mb-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl">
                    <h4 class="font-bold text-blue-800 dark:text-blue-400 text-sm mb-1"><i class="fas fa-magic mr-2"></i> Sistem Auto-Versioning & Archive Aktif</h4>
                    <p class="text-xs text-blue-600 dark:text-blue-300 leading-relaxed">
                        Anda tidak perlu menghapus kontrak lama. Sistem secara otomatis akan menyimpan dokumen yang Anda unggah ini sebagai versi terbaru (Active), dan memindahkan kontrak versi sebelumnya ke dalam arsip (Archived) yang dapat diakses di menu Riwayat Kontrak.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center">
                        <i class="fas fa-save mr-2"></i> Upload Kontrak & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script kecil biar pas milih file, nama filenya muncul di layar (Biar UX-nya bagus)
    document.getElementById('file-upload').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var infoText = this.parentElement.nextElementSibling;
        infoText.innerHTML = '<span class="text-indigo-600 font-bold">' + fileName + '</span> siap diupload.';
    });
</script>
@endsection