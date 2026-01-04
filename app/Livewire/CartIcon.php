<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;

class CartIcon extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cart-updated' => 'updateCartCount'];

    public function mount()
    {
        $this->updateCartCount();
    }

    public function updateCartCount()
    {
        $cartService = app(CartService::class);
        $this->cartCount = $cartService->getCartCount();
    }

    public function render()
    {
        return view('livewire.cart-icon');
    }
}
