<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api.key')->group(function () {

    Route::get('/test-koneksi', function () {
        return response()->json([
            'status' => 'sukses',
            'message' => 'BINGO! API Key Valid. Sistem Pricing Engine siap menerima data dari luar.',
            'data' => [
                'app_version' => '1.0',
                'client' => 'Hallo Creative Agency',
            ],
        ]);
    });

    Route::get('/v1/prices', function () {
        // Mesin ngambil semua data produk beserta nama suppliernya
        $products = \App\Models\Product::with('supplier')->paginate(50);

        // Mesin ngebungkus datanya jadi format JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Data harga produk berhasil ditarik dari Pricing Engine!',
            'total_data' => $products->count(),
            'data' => $products,
        ]);
    });

});
