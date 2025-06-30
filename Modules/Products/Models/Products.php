<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    // protected $connection = 'wordpress';
    protected $table = 'wp_posts';
    // protected $primaryKey = 'ID';
    // protected $fillable = [];
    // protected $hidden = [];
    // public $timestamps = true;
    // const CREATED_AT ="created_at";
    // const UPDATED_AT ="updated_at";
}
