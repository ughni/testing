<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentRule extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'hpp_increase_threshold',
        'hpp_adjustment',
        'demand_high_adjustment',
        'demand_low_adjustment',
        'stock_low_adjustment'
    ];
}