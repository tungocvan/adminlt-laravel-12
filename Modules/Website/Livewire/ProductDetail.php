<?php


namespace Modules\Website\Livewire;

use App\Models\WpProduct;
use Livewire\Component;

class ProductDetail extends Component
{
    public $slug;
    public $product;
    public $selectedImage;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $this->product = WpProduct::where('slug', $slug)->first();

        // Handle 404
        if (!$this->product) {
            abort(404, 'Sản phẩm không tồn tại');
        }

        // Set default selected image
        $this->selectedImage = $this->product->image;
    }

    public function selectImage($image)
    {
        $this->selectedImage = $image;
    }

    public function formatPrice($price): string
    {
        if (is_null($price)) {
            return 'Liên hệ';
        }
        return number_format($price, 0, ',', '.') . ' ₫';
    }

    public function render()
    {
        return view('Website::livewire.product-detail');
    }
}
