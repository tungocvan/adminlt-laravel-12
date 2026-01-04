<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;

class AddToCartButton extends Component
{
    public $productId;
    public $quantity = 1;
    public $buttonText = 'Thêm vào giỏ';
    public $buttonClass = 'btn-success';

    public function mount($productId, $quantity = 1)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function addToCart()
    {
        $cartService = app(CartService::class);
        
        if ($cartService->addToCart($this->productId, $this->quantity)) {
            $this->dispatch('cart-updated');
            session()->flash('success', 'Đã thêm sản phẩm vào giỏ hàng!');
            
            // Optional: Show success animation
            $this->buttonText = 'Đã thêm!';
            $this->buttonClass = 'btn-primary';
            
            // Reset after 2 seconds
            $this->dispatch('added-to-cart');
        } else {
            session()->flash('error', 'Không thể thêm sản phẩm vào giỏ hàng.');
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}
