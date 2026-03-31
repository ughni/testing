<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSystem extends Model
{
    use HasFactory;

    // 🔥 INI KUNCI GEMBOKNYA BREYY! (Whitelist kolom yang boleh diisi) 🔥
    protected $fillable = [
        'type',
        'title',
        'message',
        'icon',
        'is_read',
    ];
}