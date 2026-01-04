<?php

namespace Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'carts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_id',
        'user_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'total_items',
        'subtotal',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get total number of items in cart.
     */
    protected function totalItems(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum('quantity'),
        );
    }

    /**
     * Get cart subtotal.
     */
    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum('total'),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the items in the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Get or create cart for current session.
     */
    public static function getCurrentCart(): self
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        // Tìm cart theo user_id nếu đã đăng nhập
        if ($userId) {
            $cart = self::where('user_id', $userId)->first();
            
            if ($cart) {
                // Cập nhật session_id mới
                $cart->update(['session_id' => $sessionId]);
                return $cart;
            }
        }

        // Tìm hoặc tạo cart theo session_id
        return self::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => $userId]
        );
    }

    /**
     * Add product to cart.
     */
    public function addProduct(WpProduct $product, int $quantity = 1): CartItem
    {
        $existingItem = $this->items()
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'total' => ($existingItem->quantity + $quantity) * $existingItem->price,
            ]);
            return $existingItem->fresh();
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'price' => $product->final_price,
            'quantity' => $quantity,
            'total' => $product->final_price * $quantity,
        ]);
    }

    /**
     * Clear all items in cart.
     */
    public function clearCart(): void
    {
        $this->items()->delete();
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }
}