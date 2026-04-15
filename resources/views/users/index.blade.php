@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                    <i class="fas fa-users-cog text-lg"></i>
                </div>
                User & Role Management
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 md:ml-13">
                Kelola hak akses karyawan, tambah admin, atau atur ulang kata sandi sistem.
            </p>
        </div>
        <button onclick="openModal('add')" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all hover:-translate-y-0.5">
            <i class="fas fa-user-plus mr-2"></i> Tambah User Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-xl font-bold"><i class="fas fa-users"></i></div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Total Pengguna</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalUsers }} <span class="text-xs font-medium text-slate-400">Akun</span></h3>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center text-xl font-bold"><i class="fas fa-user-shield"></i></div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Administrator</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $adminCount }} <span class="text-xs font-medium text-slate-400">Akun</span></h3>
            </div>
        </div>
        <div class="bg-white dark:bg-[#1e293b] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center text-xl font-bold"><i class="fas fa-user-tie"></i></div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Manager Operasional</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $managerCount }} <span class="text-xs font-medium text-slate-400">Akun</span></h3>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <h5 class="font-bold text-slate-700 dark:text-slate-200 text-sm flex items-center">
                <i class="fas fa-list-ul mr-2 text-slate-400"></i> Daftar Akses Sistem
            </h5>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-800/30 text-xs text-slate-500 dark:text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Profil Karyawan</th>
                        <th class="px-6 py-4">Email Login</th>
                        <th class="px-6 py-4 text-center">Hak Akses (Role)</th>
                        <th class="px-6 py-4 text-center">Tgl Terdaftar</th>
                        <th class="px-6 py-4 text-center">Kendali</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-sm">
                    @forelse($users as $u)
                        @php
                            // Bikin Inisial Nama (Misal: Budi Santoso -> BS)
                            $words = explode(' ', $u->name);
                            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            
                            // Pewarnaan Role Pintar
                            $roleColors = [
                                'administrator' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-400', 'icon' => 'fa-crown'],
                                'super_admin' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'icon' => 'fa-bolt'],
                                'manager' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'icon' => 'fa-briefcase'],
                                'staff' => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-300', 'icon' => 'fa-user'],
                            ];
                            $rc = $roleColors[$u->role] ?? $roleColors['staff'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ $initials }}
                                    </div>
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $u->name }}
                                        @if(auth()->id() == $u->id)
                                            <span class="ml-2 text-[9px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded uppercase tracking-wider">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $rc['bg'] }} {{ $rc['text'] }}">
                                    <i class="fas {{ $rc['icon'] }} mr-1.5"></i> {{ str_replace('_', ' ', $u->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 text-xs">
                                {{ $u->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                 <button type="button" data-user="{{ json_encode($u) }}" onclick="openModal('edit', this.getAttribute('data-user'))" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-500 dark:hover:bg-amber-900/40 flex items-center justify-center transition-colors" title="Edit User">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                    @if(auth()->id() != $u->id)
                                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus akses karyawan ini permanen?');" class="inline-block m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-500 dark:hover:bg-red-900/40 flex items-center justify-center transition-colors" title="Hapus Permanen">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fas fa-users-slash text-4xl mb-3 text-slate-300 dark:text-slate-600 block opacity-50"></i>
                                Belum ada data pengguna lainnya.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalUser" style="z-index: 99999;" class="fixed inset-0 items-center justify-center bg-slate-900/80 hidden backdrop-blur-sm transition-opacity p-4">
    <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 relative">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-indigo-50 dark:bg-indigo-900/20">
            <h3 id="modalTitle" class="font-extrabold text-lg text-indigo-900 dark:text-indigo-300 flex items-center">
                <i class="fas fa-user-shield mr-2"></i> Tambah User Baru
            </h3>
            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="userForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
      <div class="space-y-4">
    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
        <input type="text" name="name" id="inp_name" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm text-slate-800 dark:text-white transition-all">
    </div>
    
    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Login *</label>
        <input type="email" name="email" id="inp_email" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm text-slate-800 dark:text-white transition-all">
    </div>
    
    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Hak Akses (Role) *</label>
        <div class="relative">
            <select name="role" id="inp_role" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-bold text-slate-800 dark:text-white appearance-none transition-all cursor-pointer">
                <option value="administrator">Administrator (Full Akses)</option>
                <option value="manager">Manager Operasional</option>
                <option value="staff">Staff Biasa</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400"><i class="fas fa-chevron-down text-xs"></i></div>
        </div>
    </div>

    <div id="wrap_add_pass" class="pt-2 border-t border-slate-100 dark:border-slate-700">
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password Baru *</label>
        <input type="password" name="password" id="inp_password" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm text-slate-800 dark:text-white transition-all">
        <p class="text-[10px] text-slate-500 mt-1.5 font-medium"><i class="fas fa-info-circle text-indigo-400"></i> Minimal 6 karakter.</p>
    </div>

    <div id="wrap_edit_pass" class="hidden pt-2 border-t border-slate-100 dark:border-slate-700 space-y-3">
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password Saat Ini (Password Lama)</label>
            <input type="password" name="password_lama" id="inp_password_lama" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm text-slate-800 dark:text-white transition-all" placeholder="Ketik sandi saat ini jika ingin diubah">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password Baru</label>
            <input type="password" name="password_baru" id="inp_password_baru" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm text-slate-800 dark:text-white transition-all" placeholder="Ketik sandi baru">
            <p class="text-[10px] text-amber-500 mt-1.5 font-medium"><i class="fas fa-exclamation-triangle"></i> Biarkan kedua kotak password kosong jika tidak ingin mengubah sandi.</p>
        </div>
    </div>
</div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-600 dark:hover:bg-slate-700 dark:text-slate-300 text-slate-700 rounded-xl font-bold transition-all text-xs">Batal</button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/30 transition-all text-xs flex items-center hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(mode, rawData = null) {
        const modal = document.getElementById('modalUser');
        const form = document.getElementById('userForm');
        const title = document.getElementById('modalTitle');
        const method = document.getElementById('formMethod');
        
        // Panggil elemen wrapper password
        const wrapAdd = document.getElementById('wrap_add_pass');
        const wrapEdit = document.getElementById('wrap_edit_pass');
        const inpPass = document.getElementById('inp_password');

        if(mode === 'add') {
            title.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Tambah User Baru';
            form.action = "{{ route('users.store') }}";
            method.value = "POST";
            form.reset();
            
            // Atur tampilan kotak password
            wrapAdd.classList.remove('hidden');
            wrapEdit.classList.add('hidden');
            inpPass.required = true;
        } else {
            // Mode Edit: Buka bungkusan JSON
            const data = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;

            title.innerHTML = '<i class="fas fa-user-edit mr-2"></i> Edit Data User';
            form.action = `/users/${data.id}`;
            method.value = "PUT";
            
            // Masukin data ke kotak
            document.getElementById('inp_name').value = data.name;
            document.getElementById('inp_email').value = data.email;
            
            // Trik jitu: pastikan role huruf kecil biar match sama <option>
            if(data.role) {
                document.getElementById('inp_role').value = data.role.toLowerCase();
            }
            
            // Atur tampilan kotak password Edit
            wrapAdd.classList.add('hidden');
            wrapEdit.classList.remove('hidden');
            inpPass.required = false;
            
            // Kosongkan isian password lama/baru
            document.getElementById('inp_password_lama').value = '';
            document.getElementById('inp_password_baru').value = '';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        document.getElementById('modalUser').classList.add('hidden');
        document.getElementById('modalUser').classList.remove('flex');
    }
</script>
@endsection