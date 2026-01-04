<?php

namespace Modules\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /**
     * The table associated with the model.
     * 
     * ⚠️ ĐÃ ĐỔI TỪ 'orders' THÀNH 'wp_orders'
     *
     * @var string
     */
    protected $table = 'wp_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'note',
        'subtotal',
        'discount',
        'total',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'user_id' => 'integer',
    ];

    /**
     * Order status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_SHIPPING => 'Đang giao hàng',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the status label in Vietnamese.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::getStatuses()[$this->status] ?? $this->status,
        );
    }

    /**
     * Get the status badge class for Bootstrap.
     */
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->status) {
                self::STATUS_PENDING => 'badge-warning',
                self::STATUS_CONFIRMED => 'badge-info',
                self::STATUS_SHIPPING => 'badge-primary',
                self::STATUS_COMPLETED => 'badge-success',
                self::STATUS_CANCELLED => 'badge-danger',
                default => 'badge-secondary',
            },
        );
    }

    /**
     * Get total items count in order.
     */
    protected function totalItems(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum('quantity'),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the items in the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Generate unique order code.
     */
    public static function generateOrderCode(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(substr(uniqid(), -4));
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Create order from cart.
     */
    public static function createFromCart(Cart $cart, array $customerData): self
    {
        $subtotal = $cart->subtotal;
        $discount = $customerData['discount'] ?? 0;
        $total = $subtotal - $discount;
        // Debug data trước khi create
        //dd(self::generateOrderCode());
        $order = self::create([
            'user_id' => auth()->id(),
            'order_code' => self::generateOrderCode(),
            'customer_name' => $customerData['customer_name'],
            'customer_phone' => $customerData['customer_phone'],
            'customer_email' => $customerData['customer_email'] ?? null,
            'customer_address' => $customerData['customer_address'],
            'note' => $customerData['note'] ?? null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'status' => self::STATUS_PENDING,
        ]);
        //\Log::info('Order created:', ['order_id' => $order->id]);
        // Create order items from cart items
        foreach ($cart->items as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->title,
                'price' => $cartItem->price,
                'quantity' => $cartItem->quantity,
                'total' => $cartItem->total,
            ]);
        }

        return $order;
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ]);
    }

    /**
     * Cancel the order.
     */
    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update(['status' => self::STATUS_CANCELLED]);
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to search by order code or customer info.
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (!$keyword) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('order_code', 'like', '%' . $keyword . '%')
              ->orWhere('customer_name', 'like', '%' . $keyword . '%')
              ->orWhere('customer_phone', 'like', '%' . $keyword . '%')
              ->orWhere('customer_email', 'like', '%' . $keyword . '%');
        });
    }
}