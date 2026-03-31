<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPricing extends Model
{
    use HasFactory;

    // Sesuaikan dengan nama kolom di file Migration aslimu
    protected $fillable = [
        'product_id',
        'date_input',
        'hpp',
        'c1',
        'c2',
        'c3',
        'yield_applied',
        'stock',
        'demand',
        'hpp_prev',
        'buffer_percent',
        'markup_base',
        'threshold_stock',
        'final_price',
        'margin_percent',
        'status_margin',
        'supplier_contract_id',
        'active_contract_version',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
