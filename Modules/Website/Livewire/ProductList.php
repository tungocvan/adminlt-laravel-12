<?php

namespace Modules\Website\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    public array $products = [];

    public function mount()
    {
        $path = public_path('data/products.json');
        //dd($path);
        if (!file_exists($path)) {
            $this->products = [];
            return;
        }

        $this->products = json_decode(file_get_contents($path), true) ?? [];
    }

    public function render()
    {
        return view('Website::livewire.product-list');
    }
}
