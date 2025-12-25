<?php

//namespace App\Models;
namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Categories\Models\Categories;
class WpProduct extends Model
{
    use HasFactory;
    protected $table = 'wp_products';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',       
        'image',
        'gallery',
        'tags',
    ];

    protected $casts = [
        'gallery' => 'array',
        'tags'    => 'array',
        'regular_price' => 'decimal:2',
        'sale_price'    => 'decimal:2',
    ];

    // Quan hệ với Category
    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'category_product', 'product_id', 'category_id')
            ->withTimestamps();
    }
}
