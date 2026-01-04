<?php

namespace Modules\Website\Livewire\Checkout;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Modules\Website\Models\Cart;

class OrderSummary extends Component
{
    /**
     * Lắng nghe sự kiện cart-updated
     */
    #[On('cart-updated')]
    public function refreshSummary(): void
    {
        // Component tự động re-render
    }

    /**
     * Lấy giỏ hàng hiện tại
     */
    #[Computed]
    public function cart()
    {
        return Cart::getCurrentCart()->load('items.product');
    }

    /**
     * Lấy danh sách items
     */
    #[Computed]
    public function items()
    {
        return $this->cart->items;
    }

    /**
     * Tính tổng tiền hàng
     */
    #[Computed]
    public function subtotal(): float
    {
        return $this->cart->subtotal;
    }

    /**
     * Tính phí vận chuyển
     */
    #[Computed]
    public function shippingFee(): float
    {
        if ($this->subtotal >= 500000) {
            return 0;
        }
        return 30000;
    }

    /**
     * Tính số tiền cần mua thêm để miễn phí ship
     */
    #[Computed]
    public function amountForFreeShipping(): float
    {
        if ($this->subtotal >= 500000) {
            return 0;
        }
        return 500000 - $this->subtotal;
    }

    /**
     * Tổng thanh toán
     */
    #[Computed]
    public function total(): float
    {
        return $this->subtotal + $this->shippingFee;
    }

    /**
     * Tổng số lượng sản phẩm
     */
    #[Computed]
    public function totalQuantity(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Format giá tiền
     */
    public function formatPrice($price): string
    {
        return number_format($price, 0, ',', '.') . 'đ';
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('Website::livewire.checkout.order-summary');
    }
}