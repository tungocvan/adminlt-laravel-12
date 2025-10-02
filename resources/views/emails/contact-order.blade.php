@component('mail::message')
# Liên hệ mới

**Họ Tên:** {{ $order->full_name }}  
**Email:** {{ $order->email }}  
**Điện Thoại:** {{ $order->phone }}  
**Bạn là:** {{ $order->user_type }}  

**Nội dung:**  
{{ $order->message }}

@endcomponent
