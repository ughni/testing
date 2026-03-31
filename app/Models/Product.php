<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
   use HasFactory, HasUuids;

    protected $fillable = [
        'supplier_id',
        'product_name',
        'category',
        'unit',
        'description',
        'is_active',
        'price_type',
        'het_price',
        'consignment_margin',
        'selling_price_fixed',
        
        // --- NAMA KAMAR LAMA (Biarin aja buat jaga-jaga) ---
        'buffer_percent',
        'markup_base',
        'threshold_stock',
        
        // 🔥 INI NAMA KAMAR BARU YANG DITAMBAHIN BIAR NGGAK AMNESIA 🔥
        'markup',
        'buffer',
        'threshold',
        'yield_percent',
    ];

  // Relasi ke hasil hitungan mesin (INI YANG BENER BREYY!)
    public function dailyPricings()
    {
        return $this->hasMany(\App\Models\DailyPricing::class, 'product_id', 'id')->orderBy('date_input', 'desc');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

// kemunitu tabrakan code yang ini bener yang lain salah dan sebaliknya