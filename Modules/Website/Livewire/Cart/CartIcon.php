<?php

namespace Modules\Website\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Modules\Website\Models\Cart;

class CartIcon extends Component
{
    /**
     * Lắng nghe sự kiện cart-updated
     */
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Livewire tự động re-render khi computed property thay đổi
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ
     */
    #[Computed]
    public function itemCount(): int
    {
        return Cart::getCurrentCart()->items->sum('quantity');
    }

    /**
     * Lấy tổng tiền giỏ hàng
     */
    #[Computed]
    public function subtotal(): float
    {
        return Cart::getCurrentCart()->subtotal;
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('Website::livewire.cart.cart-icon');
    }
}