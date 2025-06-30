<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpOption extends Model
{
    protected $table = 'wp_options';
    protected $primaryKey = 'option_id';
    public $timestamps = false;

    protected $fillable = [
        'option_name',
        'option_value',
        'autoload',
    ];
}