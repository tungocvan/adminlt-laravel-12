@component('mail::message')
# Cảm ơn bạn đã đặt hàng 🎉

Xin chào **{{ $order->email }}**,  
Đơn hàng của bạn đã được tạo thành công.

**Mã đơn hàng:** #{{ $order->id }}  
**Tổng tiền:** {{ number_format($order->total, 0, ',', '.') }} ₫  

@component('mail::table')
| Sản phẩm | SL | Giá | Thành tiền |
|:---------|:--:|----:|-----------:|
@foreach ($order->order_detail as $item)
| {{ $item['title'] }} | {{ $item['quantity'] }} | {{ number_format($item['price'], 0, ',', '.') }} | {{ number_format($item['total'], 0, ',', '.') }} |
@endforeach
@endcomponent

Cảm ơn bạn đã mua hàng tại CÔNG TY TNHH INAFO VIỆT NAM chúng tôi ❤️  

@endcomponent
