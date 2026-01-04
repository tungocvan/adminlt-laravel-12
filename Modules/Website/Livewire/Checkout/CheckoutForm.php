<?php

namespace Modules\Website\Livewire\Checkout;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Modules\Website\Models\Cart;
use Modules\Website\Models\Order;

class CheckoutForm extends Component
{
    /**
     * Form fields
     */
    #[Validate('required|string|max:255', message: 'Vui lòng nhập họ tên.')]
    public string $customer_name = '';

    #[Validate('required|string|max:20', message: 'Vui lòng nhập số điện thoại.')]
    public string $customer_phone = '';

    #[Validate('nullable|email|max:255', message: 'Email không đúng định dạng.')]
    public string $customer_email = '';

    #[Validate('required|string|max:500', message: 'Vui lòng nhập địa chỉ giao hàng.')]
    public string $customer_address = '';

    #[Validate('nullable|string|max:1000')]
    public string $note = '';

    /**
     * Trạng thái xử lý
     */
    public bool $processing = false;

    /**
     * Mount component - tự động điền thông tin user nếu đã đăng nhập
     */
    public function mount(): void
    {
        if (auth()->check()) {
            $user = auth()->user();
            $this->customer_name = $user->name ?? '';
            $this->customer_email = $user->email ?? '';
            $this->customer_phone = $user->phone ?? '';
            $this->customer_address = $user->address ?? '';
        }
    }

    /**
     * Lấy giỏ hàng hiện tại
     */
    #[Computed]
    public function cart()
    {
        return Cart::getCurrentCart()->load('items.product');
    }

    /**
     * Kiểm tra giỏ hàng trống
     */
    #[Computed]
    public function isCartEmpty(): bool
    {
        return $this->cart->isEmpty();
    }

    /**
     * Tính tổng tiền
     */
    #[Computed]
    public function subtotal(): float
    {
        return $this->cart->subtotal;
    }

    /**
     * Tính phí vận chuyển (có thể mở rộng sau)
     */
    #[Computed]
    public function shippingFee(): float
    {
        // Miễn phí vận chuyển cho đơn hàng trên 500k
        if ($this->subtotal >= 500000) {
            return 0;
        }
        return 30000;
    }

    /**
     * Tính tổng thanh toán
     */
    #[Computed]
    public function total(): float
    {
        return $this->subtotal + $this->shippingFee;
    }

    /**
     * Validation realtime cho phone
     */
    public function updatedCustomerPhone(): void
    {
        $this->validateOnly('customer_phone');
    }

    /**
     * Validation realtime cho email
     */
    public function updatedCustomerEmail(): void
    {
        if (!empty($this->customer_email)) {
            $this->validateOnly('customer_email');
        }
    }

    /**
     * Đặt hàng
     */
    public function placeOrder(): void
    {
      
        // Validate form
        $this->validate();

        // Kiểm tra giỏ hàng
        if ($this->isCartEmpty) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Giỏ hàng của bạn đang trống!'
            ]);
            return;
        }

        $this->processing = true;
     
        try {
            // Chuẩn bị dữ liệu
            $customerData = [
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'customer_email' => $this->customer_email ?: null,
                'customer_address' => $this->customer_address,
                'note' => $this->note ?: null,
            ]; 
            
            // Tạo đơn hàng
            $order = Order::createFromCart($this->cart, $customerData);
       
            // Xóa giỏ hàng
            $this->cart->clearCart();

            // Dispatch event
            $this->dispatch('cart-updated');
            $this->dispatch('order-placed', orderId: $order->id);

            // Chuyển hướng đến trang thành công
            $this->redirect(
                route('website.order.success', $order->order_code),
                navigate: true
            );

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }

        $this->processing = false;
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
        return view('Website::livewire.checkout.checkout-form');
    }
}