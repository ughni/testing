<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiCredential;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 🔥 INI YANG GUE PERBAIKI: Satpam sekarang pinter nyari Bearer Token
        $apiKey = $request->bearerToken() ?? $request->header('x-api-key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses Ditolak! API Key tidak ditemukan.'
            ], 401);
        }

        // 2. Satpam ngecek ke database: "Kunci ini asli dan masih aktif nggak?"
        $isValid = ApiCredential::where('api_key', $apiKey)
                                ->where('is_active', true)
                                ->exists();

        if (!$isValid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses Ditolak! API Key tidak valid atau sudah di-Revoke.'
            ], 403);
        }

        // 3. Kalau aman, silakan masuk!
        return $next($request);
    }
}