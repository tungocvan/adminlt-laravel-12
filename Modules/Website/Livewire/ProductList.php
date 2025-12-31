<?php

namespace Modules\Website\Livewire;

use App\Models\WpProduct;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPage = 12;

    // Ready for future filter expansion
    public $minPrice;
    public $maxPrice;
    public $selectedTags = [];

    public function render()
    {
        $products = WpProduct::query()
            ->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        return view('Website::livewire.product-list', [
            'products' => $products
        ]);
    }

    /**
     * Format price for display
     */
    public function formatPrice($price): string
    {
        if (is_null($price)) {
            return 'Liên hệ';
        }
        return number_format($price, 0, ',', '.') . ' ₫';
    }
}
