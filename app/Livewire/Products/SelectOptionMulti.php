<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\WpProduct;

class SelectOptionMulti extends Component
{
    public $options = [];
    public $selected = []; // multiple => mảng
    public $placeholder = 'Chọn sản phẩm';

    public function mount($selected = [], $placeholder = null)
    {
        $this->options = WpProduct::pluck('title', 'id')->toArray();
        $this->selected = $selected;

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
    }

    public function updatedSelected()
    {
        // Kiểm tra selected
        // dd($this->selected);
       // logger()->info('Selected products:', $this->selected);
        
        // ví dụ: [1, 5, 9]
    }

    public function render()
    {
        return view('livewire.products.select-option-multi');
    }
}
