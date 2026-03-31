<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormulaSetting;
use App\Models\AuditLog;
use App\Models\NotificationSystem;
use Illuminate\Support\Facades\Auth;

class FormulaSettingController extends Controller
{
    public function index()
    {
        // Panggil data setting, kalau belum ada sama sekali di database, buatin default-nya otomatis
        $setting = FormulaSetting::first() ?? FormulaSetting::create([
            'buffer_percent' => 0.05,
            'markup_base' => 0.20,
            'threshold_stock' => 20,
            'yield_percent' => 1.00 // 🔥 DEFAULT 100% BUAT YIELD 🔥
        ]);

        return view('pricing.formula', compact('setting'));
    }

    public function update(Request $request)
    {
        // Validasi ditambahin untuk yield_percent
        $request->validate([
            'buffer_percent' => 'required|numeric|min:0|max:100',
            'markup_base' => 'required|numeric|min:0|max:100',
            'threshold_stock' => 'required|integer|min:0',
            'yield_percent' => 'required|numeric|min:1', // 🔥 VALIDASI YIELD 🔥 (Minimal 1%)
        ]);

        $setting = FormulaSetting::first();

        // Bagi 100 karena dari UI Bos ngetiknya angka bulat (misal 5, disimpen jadi 0.05)
        $setting->update([
            'buffer_percent' => $request->buffer_percent / 100,
            'markup_base' => $request->markup_base / 100,
            'threshold_stock' => $request->threshold_stock,
            'yield_percent' => $request->yield_percent / 100, // 🔥 DIBAGI 100 BIAR JADI DESIMAL 🔥
        ]);

        // --- 🎥 CCTV UPDATE FORMULA ---
        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Formula Engine Settings',
            'description' => 'Mengubah variabel utama mesin harga. Buffer: '.$request->buffer_percent.'%, Markup: '.$request->markup_base.'%, Threshold Stok: '.$request->threshold_stock.', Yield: '.$request->yield_percent.'%',
            'ip_address' => $request->ip()
        ]);

        // --- 🔔 NOTIFIKASI KE BOS ---
        NotificationSystem::create([
            'type' => 'warning',
            'title' => 'Perubahan Formula Harga',
            'message' => 'Variabel utama Pricing Engine telah diubah. Harga produk yang dihitung otomatis akan terpengaruh mulai saat ini.',
            'icon' => 'fas fa-calculator'
        ]);

        return redirect()->back()->with('success', 'Sistem Otak Harga (Formula Engine) berhasil diperbarui!');
    }
}