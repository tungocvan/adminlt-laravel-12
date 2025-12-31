<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        'tags' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    /**
     * Get final price (prioritize sale_price if exists)
     */
    public function getFinalPriceAttribute(): ?float
    {
        return $this->sale_price ?? $this->regular_price;
    }

    /**
     * Calculate discount percentage
     */
    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->regular_price || !$this->sale_price) {
            return null;
        }

        if ($this->sale_price >= $this->regular_price) {
            return null;
        }

        return (int) round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return 'https://via.placeholder.com/600x600/CCCCCC/FFFFFF?text=No+Image';
        }

        // Nếu là URL đầy đủ (http/https)
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        // Nếu là đường dẫn storage
        return Storage::url($this->image);
    }

    /**
     * Get gallery URLs
     */
    public function getGalleryUrlsAttribute(): array
    {
        if (empty($this->gallery) || !is_array($this->gallery)) {
            return [];
        }

        return array_map(function ($image) {
            // Nếu là URL đầy đủ
            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                return $image;
            }
            // Nếu là đường dẫn storage
            return Storage::url($image);
        }, $this->gallery);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id')
            ->withTimestamps();
    }
    /**
     * Auto-generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }
}

