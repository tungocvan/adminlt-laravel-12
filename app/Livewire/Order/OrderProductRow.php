<?php

namespace App\Livewire\Order;

use Livewire\Component;

class OrderProductRow extends Component
{
    public $product;
    public $selected = false;
    public $quantity = 1;

    protected $listeners = ['refreshTotal' => '$refresh'];

    public function mount($product, $selected = false, $quantity = 1)
    {
        $this->product = $product;
        $this->selected = $selected;
        $this->quantity = $quantity;
    }

    public function toggleSelected()
    {
        $this->selected = !$this->selected;
        $this->emit('productSelectionUpdated', $this->product->id, $this->selected, $this->quantity);
    }

    public function increment()
    {
        $this->quantity++;
        if($this->selected) $this->emit('productQuantityUpdated', $this->product->id, $this->quantity);
    }

    public function decrement()
    {
        if($this->quantity > 1) {
            $this->quantity--;
            if($this->selected) $this->emit('productQuantityUpdated', $this->product->id, $this->quantity);
        }
    }

    public function render()
    {
        return view('livewire.order.order-product-row');
    }
}
