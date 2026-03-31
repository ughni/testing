<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'markup_base', 
        'markup_demand_high', 
        'markup_demand_low', 
        'competitor_weight'
    ];
}
