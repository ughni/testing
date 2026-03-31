<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\Concerns\Ha sUuids; // Buka komen ini KALAU primary key tabel kontrak ini juga pakai UUID

class SupplierContract extends Model
{
    use HasFactory;
    // use HasUuids; // Buka komen ini juga KALAU pakai UUID

    protected $table = 'supplier_contracts';

    // Pake guarded biar Controller lu bebas nyimpen data (karena nama kolom di $fillable lama lu salah/ketinggalan zaman)
    protected $guarded = ['id'];

    // 🔥 INI OBAT PENAWARNYA! Paksa Laravel ngebaca UUID Supplier sebagai Teks (String), bukan Angka!
    protected $casts = [
        'supplier_id' => 'string',
    ];

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id', 'id');
    }
}
