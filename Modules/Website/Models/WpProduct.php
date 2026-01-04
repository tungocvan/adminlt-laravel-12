<?php

namespace Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WpProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gallery' => 'array',
        'tags' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'final_price',
        'discount_percent',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the final price (sale_price if available, otherwise regular_price).
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sale_price ?? $this->regular_price ?? 0,
        );
    }

    /**
     * Get the discount percentage.
     */
    protected function discountPercent(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->regular_price || !$this->sale_price) {
                    return 0;
                }

                if ($this->sale_price >= $this->regular_price) {
                    return 0;
                }

                return round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
            },
        );
    }

    /**
     * Get the product image URL with fallback.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image 
                ? asset('storage/' . $this->image) 
                : asset('images/no-image.png'),
        );
    }

    /**
     * Check if product is on sale.
     */
    protected function isOnSale(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sale_price 
                && $this->regular_price 
                && $this->sale_price < $this->regular_price,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the cart items for the product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include available products.
     */
    public function scopeAvailable($query)
    {
        return $query->whereNotNull('regular_price')
                     ->where('regular_price', '>', 0);
    }

    /**
     * Scope a query to only include products on sale.
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
                     ->whereColumn('sale_price', '<', 'regular_price');
    }

    /**
     * Scope a query to search products by title.
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (!$keyword) {
            return $query;
        }

        return $query->where('title', 'like', '%' . $keyword . '%');
    }
}