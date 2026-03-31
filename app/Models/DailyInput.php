<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyInput extends Model
{
    use HasFactory;

    // Kumpulan kolom yang diizinkan untuk diisi dari form atau sistem
    protected $fillable = [
        'product_id', 
        'input_date', // Konsisten pakai input_date
        'hpp', 
        'c1', 
        'c2', 
        'c3', 
        'yield_applied',
        'stock', 
        'demand', 
        'hpp_prev',
        'user_id'     // Wajib ada untuk mencatat siapa yang input
    ];

    // Relasi balik ke produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}