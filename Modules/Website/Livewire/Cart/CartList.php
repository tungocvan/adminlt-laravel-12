<?php

namespace Modules\Website\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Modules\Website\Models\Cart;
use Modules\Website\Models\CartItem;

class CartList extends Component
{
    /**
     * Lắng nghe sự kiện cart-updated
     */
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Livewire tự động re-render
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
     * Lấy danh sách items trong giỏ
     */
    #[Computed]
    public function items()
    {
        return $this->cart->items;
    }

    /**
     * Tính tổng tiền
     */
    #[Computed]
    public function subtotal(): float
    {
        return $this->items->sum('total');
    }

    /**
     * Tính tổng số lượng
     */
    #[Computed]
    public function totalQuantity(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Kiểm tra giỏ hàng trống
     */
    #[Computed]
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateQuantity(int $itemId, int $quantity): void
    {
        if ($quantity < 1) {
            $quantity = 1;
        }

        if ($quantity > 99) {
            $quantity = 99;
        }

        $item = CartItem::find($itemId);

        if ($item && $item->cart_id === $this->cart->id) {
            $item->updateQuantity($quantity);
            $this->dispatch('cart-updated');
        }
    }

    /**
     * Tăng số lượng
     */
    public function increment(int $itemId): void
    {
        $item = CartItem::find($itemId);

        if ($item && $item->cart_id === $this->cart->id && $item->quantity < 99) {
            $item->incrementQuantity();
            $this->dispatch('cart-updated');
        }
    }

    /**
     * Giảm số lượng
     */
    public function decrement(int $itemId): void
    {
        $item = CartItem::find($itemId);

        if ($item && $item->cart_id === $this->cart->id && $item->quantity > 1) {
            $item->decrementQuantity();
            $this->dispatch('cart-updated');
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ
     */
    public function removeItem(int $itemId): void
    {
        $item = CartItem::find($itemId);

        if ($item && $item->cart_id === $this->cart->id) {
            $productName = $item->product->title ?? 'Sản phẩm';
            $item->delete();

            $this->dispatch('cart-updated');
            $this->dispatch('show-toast', [
                'type' => 'info',
                'message' => "Đã xóa \"{$productName}\" khỏi giỏ hàng."
            ]);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart(): void
    {
        $this->cart->clearCart();
        $this->dispatch('cart-updated');
        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => 'Đã xóa toàn bộ giỏ hàng.'
        ]);
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
        return view('Website::livewire.cart.cart-list');
    }
}