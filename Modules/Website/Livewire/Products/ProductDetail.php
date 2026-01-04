<?php

namespace Modules\Website\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Modules\Website\Models\WpProduct;

class ProductDetail extends Component
{
    /**
     * Slug của sản phẩm
     */
    public string $slug;

    /**
     * Số lượng muốn mua
     */
    public int $quantity = 1;

    /**
     * Ảnh đang được chọn xem
     */
    public int $selectedImageIndex = 0;

    /**
     * Mount component
     */
    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Lấy thông tin sản phẩm
     */
    #[Computed]
    public function product()
    {
        return WpProduct::where('slug', $this->slug)->firstOrFail();
    }

    /**
     * Lấy danh sách ảnh gallery
     */
    #[Computed]
    public function galleryImages(): array
    {
        $images = [];

        // Thêm ảnh chính
        if ($this->product->image) {
            $images[] = $this->product->image;
        }

        // Thêm ảnh gallery
        if ($this->product->gallery && is_array($this->product->gallery)) {
            $images = array_merge($images, $this->product->gallery);
        }

        // Nếu không có ảnh nào, trả về ảnh mặc định
        if (empty($images)) {
            $images[] = null;
        }

        return $images;
    }

    /**
     * Lấy sản phẩm liên quan
     */
    #[Computed]
    public function relatedProducts()
    {
        return WpProduct::where('id', '!=', $this->product->id)
            ->available()
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    /**
     * Chọn ảnh gallery
     */
    public function selectImage(int $index): void
    {
        if ($index >= 0 && $index < count($this->galleryImages)) {
            $this->selectedImageIndex = $index;
        }
    }

    /**
     * Tăng số lượng
     */
    public function incrementQuantity(): void
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    /**
     * Giảm số lượng
     */
    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
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
        return view('Website::livewire.products.product-detail');
    }
}