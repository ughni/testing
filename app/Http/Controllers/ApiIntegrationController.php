<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiCredential;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ApiIntegrationController extends Controller
{
    public function index()
    {
        $credentials = ApiCredential::latest()->get();
        return view('settings.api', compact('credentials'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
        ]);

        // Bikin token acak 40 karakter ala Enterprise
        $newKey = 'prc_' . Str::random(40);

        ApiCredential::create([
            'app_name' => $request->app_name,
            'api_key' => $newKey,
            'is_active' => true,
        ]);

        // Nyalain CCTV
        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'GENERATE_API',
            'module' => 'API Integration',
            'description' => 'Membuat API Key baru untuk aplikasi: ' . $request->app_name,
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'API Key berhasil di-generate! Simpan baik-baik token ini.');
    }

    public function revoke($id)
    {
        $key = ApiCredential::findOrFail($id);
        $key->update(['is_active' => false]);

        return redirect()->back()->with('warning', 'Akses API Key untuk ' . $key->app_name . ' telah diputus (Revoked).');
    }
}