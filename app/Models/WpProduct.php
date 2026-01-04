<?php
// app/Models/WpProduct.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WpProduct extends Model
{
    protected $table = 'wp_products';

    protected $fillable = ['title', 'slug', 'short_description', 'description', 'regular_price', 'sale_price', 'image', 'gallery', 'tags'];

    protected $casts = [
        'gallery' => 'array',
        'tags' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    /**
     * Accessor: Lấy giá cuối cùng (ưu tiên sale_price)
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::make(get: fn() => $this->sale_price ?? $this->regular_price);
    }

    /**
     * Accessor: Tính % giảm giá
     */
    protected function discountPercent(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->sale_price || !$this->regular_price || $this->regular_price == 0) {
                    return 0;
                }
                return round((1 - $this->sale_price / $this->regular_price) * 100);
            },
        );
    }

    /**
     * Scope: Sắp xếp mới nhất
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Lấy ảnh với fallback
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ?? '/images/no-image.jpg';
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id')->withTimestamps();
    }
}
