<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdjustmentRule;
use App\Models\AuditLog;
use App\Models\NotificationSystem;
use Illuminate\Support\Facades\Auth;

class AdjustmentRuleController extends Controller
{
    public function index()
    {
        // Panggil data, kalau kosong buatin default sesuai dokumen klien
        $rule = AdjustmentRule::first() ?? AdjustmentRule::create([
            'hpp_increase_threshold' => 0.03,
            'hpp_adjustment' => 0.03,
            'demand_high_adjustment' => 0.03,
            'demand_low_adjustment' => -0.03,
            'stock_low_adjustment' => 0.02,
        ]);

        return view('pricing.rules', compact('rule'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hpp_increase_threshold' => 'required|numeric|min:0|max:100',
            'hpp_adjustment' => 'required|numeric|min:0|max:100',
            'demand_high_adjustment' => 'required|numeric|min:0|max:100',
            'demand_low_adjustment' => 'required|numeric|min:-100|max:0', // Ini minus karena diskon
            'stock_low_adjustment' => 'required|numeric|min:0|max:100',
        ]);

        $rule = AdjustmentRule::first();

        // Dibagi 100 lagi biar aman di database jadi desimal
        $rule->update([
            'hpp_increase_threshold' => $request->hpp_increase_threshold / 100,
            'hpp_adjustment' => $request->hpp_adjustment / 100,
            'demand_high_adjustment' => $request->demand_high_adjustment / 100,
            'demand_low_adjustment' => $request->demand_low_adjustment / 100,
            'stock_low_adjustment' => $request->stock_low_adjustment / 100,
        ]);

        // Nyalain CCTV
        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'UPDATE',
            'module' => 'Auto Adjustment Rules',
            'description' => 'Mengubah persentase aturan otomatis (Demand/Stok/HPP).',
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Aturan Penyesuaian Otomatis berhasil diperbarui!');
    }
}