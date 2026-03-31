<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog; // <--- WAJIB TAMBAHIN INI BIAR CCTV NYALA

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

  public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- 🎥 CCTV LOGIN START ---
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGIN',
                'module' => 'Authentication',
                'description' => Auth::user()->name . ' berhasil login ke dalam sistem.',
                'ip_address' => $request->ip()
            ]);
            // --- 🎥 CCTV LOGIN END ---

            // AMBIL ROLE USER YANG BARU AJA BERHASIL LOGIN
            $role = Auth::user()->role;

            // LOGIKA PINTAR: Arahin ke halaman yang sesuai kastanya
            if ($role === 'administrator' || $role === 'manager') {
                // Kalau Bos / Manager, silakan masuk ke Dashboard
                return redirect()->intended('/');
            } else {
                // Kalau Staff Admin biasa, langsung suruh kerja ke Input Harian!
                return redirect()->intended('/daily-inputs');
            }
        }

        return back()->withErrors(['email' => 'Waduh, email atau password kamu nggak cocok nih!'])->withInput();
    }

    public function logout(Request $request) {
        
        // --- 🎥 CCTV LOGOUT START (Tangkep dulu sebelum user beneran di-logout) ---
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGIN', // Pakai tag LOGIN aja tapi deskripsinya logout
                'module' => 'Authentication',
                'description' => Auth::user()->name . ' keluar (logout) dari sistem.',
                'ip_address' => $request->ip()
            ]);
        }
        // --- 🎥 CCTV LOGOUT END ---

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function changePassword(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // Pastikan di UI ada input 'new_password_confirmation'
        ], [
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok, Breyy!',
            'new_password.min' => 'Password minimal 6 karakter ya.'
        ]);

        // 2. Cek apakah password lama bener 
        if(!\Illuminate\Support\Facades\Hash::check($request->old_password, Auth::user()->password)){
            return back()->with('error', 'Password lama salah!');
        }

        // 3. Update ke password baru 
        \App\Models\User::whereId(Auth::id())->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        // --- 🎥 CCTV GANTI PASSWORD START ---
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE',
            'module' => 'Authentication',
            'description' => Auth::user()->name . ' mengubah password akunnya.',
            'ip_address' => $request->ip()
        ]);
        // --- 🎥 CCTV GANTI PASSWORD END ---

        return back()->with('success', 'Mantap! Password berhasil diganti.');
    }
}


// Izin Pak Youdhi, untuk sistem pendaftarannya sengaja saya buat tertutup (by Admin) demi keamanan data internal perusahaan. Jadi akun hanya bisa dibuat oleh Administrator melalui dashboard, bukan daftar sendiri secara publik. Hal ini untuk mencegah pihak luar mengakses data harga dan margin perusahaan.  pesan ini kirim sekarang apa nanti aja apa ngerjakan yang lain dulu baru tanyaain untuk mehemat waktu