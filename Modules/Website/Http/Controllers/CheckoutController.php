<?php

namespace Modules\Website\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Website\Models\Cart;
use Modules\Website\Models\Order;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang thanh toán.
     * 
     * GET /website/checkout
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Kiểm tra giỏ hàng có sản phẩm không
        $cart = Cart::getCurrentCart();
        
        if ($cart->isEmpty()) {
            return redirect()
                ->route('website.cart.index')
                ->with('warning', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        return view('Website::checkout.index');
    }

    /**
     * Xử lý đặt hàng.
     * 
     * POST /website/checkout
     * 
     * ⚠️ Logic xử lý chính nằm trong Livewire CheckoutForm
     * Route này dùng cho fallback hoặc non-JS form submission
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function process(Request $request): RedirectResponse
    {
   
        // Validate dữ liệu
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'customer_email.email' => 'Email không đúng định dạng.',
            'customer_address.required' => 'Vui lòng nhập địa chỉ giao hàng.',
        ]);

        // Lấy giỏ hàng hiện tại
        $cart = Cart::getCurrentCart();
      
        if ($cart->isEmpty()) {
            return redirect()
                ->route('website.cart.index')
                ->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Tạo đơn hàng từ giỏ hàng
        $order = Order::createFromCart($cart, $validated);

        // Xóa giỏ hàng sau khi đặt hàng thành công
        $cart->clearCart();

        // Chuyển đến trang thành công
        return redirect()
            ->route('website.order.success', $order->order_code)
            ->with('success', 'Đặt hàng thành công!');
    }

    /**
     * Hiển thị trang đặt hàng thành công.
     * 
     * GET /website/order-success/{code}
     *
     * @param string $code
     * @return View
     */
    public function success(string $code): View
    {
        // Tìm đơn hàng theo mã
        $order = Order::where('order_code', $code)
            ->with('items.product')
            ->firstOrFail();

        return view('Website::checkout.success', [
            'order' => $order,
        ]);
    }
}