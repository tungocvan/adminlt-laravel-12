<?php

namespace Modules\Website\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\Website\Models\Cart;
use Modules\Website\Models\WpProduct;

class AddToCart extends Component
{
    /**
     * ID sản phẩm
     */
    public int $productId;

    /**
     * Số lượng thêm vào giỏ
     */
    public int $quantity = 1;

    /**
     * Hiển thị dạng button hay icon
     */
    public string $type = 'button'; // 'button' | 'icon'

    /**
     * Kích thước button
     */
    public string $size = 'md'; // 'sm' | 'md' | 'lg'

    /**
     * Trạng thái loading
     */
    public bool $loading = false;

    /**
     * Hiển thị text trên button
     */
    public bool $showText = true;

    /**
     * Mount component
     */
    public function mount(
        int $productId,
        int $quantity = 1,
        string $type = 'button',
        string $size = 'md',
        bool $showText = true
    ): void {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->type = $type;
        $this->size = $size;
        $this->showText = $showText;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(): void
    {
        $this->loading = true;

        try {
            // Lấy sản phẩm
            $product = WpProduct::findOrFail($this->productId);

            // Lấy hoặc tạo giỏ hàng
            $cart = Cart::getCurrentCart();

            // Thêm sản phẩm vào giỏ
            $cart->addProduct($product, $this->quantity);

            // Dispatch event để cập nhật CartIcon
            $this->dispatch('cart-updated');

            // Thông báo thành công
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => "Đã thêm \"{$product->title}\" vào giỏ hàng!"
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại!'
            ]);
        }

        $this->loading = false;
    }

    /**
     * Lấy class cho button
     */
    public function getButtonClass(): string
    {
        $sizeClass = match ($this->size) {
            'sm' => 'btn-sm',
            'lg' => 'btn-lg',
            default => '',
        };

        return "btn btn-primary {$sizeClass}";
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('Website::livewire.cart.add-to-cart');
    }
}