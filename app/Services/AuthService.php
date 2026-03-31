<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

trait AuthService
{
    public function AuthLogin($request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

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
}
