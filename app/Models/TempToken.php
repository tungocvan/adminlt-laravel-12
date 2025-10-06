<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempToken extends Model
{
    protected $fillable = ['token', 'expires_at', 'ip_address'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public $timestamps = true;
}
