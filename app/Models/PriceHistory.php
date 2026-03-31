<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
protected $fillable = ['product_id', 'hpp', 'suggested_price', 'margin_percentage', 'status'];

    // Relasi balik ke produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
