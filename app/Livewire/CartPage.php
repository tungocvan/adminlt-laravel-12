<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;

class CartPage extends Component
{
    public $cartItems = [];
    public $cartTotal = 0;

    protected $listeners = ['cart-updated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $cartService = app(CartService::class);
        $this->cartItems = $cartService->getCartItems();
        $this->cartTotal = $cartService->getCartTotal();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        $cartService = app(CartService::class);
        $cartService->updateQuantity($cartItemId, $quantity);
        
        $this->loadCart();
        $this->dispatch('cart-updated');
        
        session()->flash('success', 'Đã cập nhật giỏ hàng.');
    }

    public function removeItem($cartItemId)
    {
        $cartService = app(CartService::class);
        $cartService->removeFromCart($cartItemId);
        
        $this->loadCart();
        $this->dispatch('cart-updated');
        
        session()->flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function clearCart()
    {
        $cartService = app(CartService::class);
        $cartService->clearCart();
        
        $this->loadCart();
        $this->dispatch('cart-updated');
        
        session()->flash('success', 'Đã xóa toàn bộ giỏ hàng.');
    }

    public function formatPrice($price): string
    {
        return number_format($price, 0, ',', '.') . ' ₫';
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}
