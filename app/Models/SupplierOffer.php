<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SupplierOffer extends Model
{
    use HasUuids; 

    protected $table = 'supplier_offers';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'supplier_name',
        'product_name', 
        'price',
        'status',
        'qty',
        'unit', 
        'offer_date',
        'hold_until'
    ];
}