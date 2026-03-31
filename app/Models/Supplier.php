<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Supplier extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'suppliers';

    // Beritahu Laravel kalau ID kita itu String (UUID), bukan angka berurutan
    public $incrementing = false;
    protected $keyType = 'string';

    // 👇🔥 INI YANG DIPERBAIKI & DITAMBAHKAN: Daftar Kolom KTP Supplier Baru 🔥👇
    protected $fillable = [
        'no_supplier',
        'nama_supplier',
        'kualifikasi',
        'alamat',
        'kontak_person',
        'email',
        'is_active',
        'is_contract'
    ];
    // 👆 ============================================================== 👆

    /**
     * Relasi ke tabel products (One to Many)
     * Satu supplier bisa punya banyak produk
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id', 'id');
    }
}