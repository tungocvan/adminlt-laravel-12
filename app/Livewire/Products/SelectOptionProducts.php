<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\WpProduct;

class SelectOptionProducts extends Component
{
    public $options = [];
    public $selected = null;
    public $placeholder = 'Chọn sản phẩm';

    public function mount($selected = null, $placeholder = null)
    {
        $this->options = WpProduct::pluck('title', 'id')->toArray();
        $this->selected = $selected;
        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
    }

    public function updatedSelected(){
        //dd($this->selected); // lấy id của product
    }
     
    public function render()
    {
        return view('livewire.products.select-option-products');
    }
}
