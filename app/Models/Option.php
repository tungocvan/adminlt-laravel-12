<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'optionable_id',
        'optionable_type',
        'option_name',
        'option_value',
        'autoload',
    ];

    protected $casts = [
        'option_value' => 'array',
    ];

    public function optionable()
    {
        return $this->morphTo();
    }
}
