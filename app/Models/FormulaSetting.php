<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulaSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'buffer_percent', 
        'markup_base', 
        'threshold_stock',
        'yield_percent',
    ];
}