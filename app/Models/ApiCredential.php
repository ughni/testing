<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'app_name',
        'api_key',
        'is_active',
        'last_used_at'
    ];
}