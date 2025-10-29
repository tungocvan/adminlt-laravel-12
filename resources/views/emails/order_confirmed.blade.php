@component('mail::message')
# Đơn hàng #{{ $order->id }} đã được xác nhận 🎉

Cảm ơn bạn đã đặt hàng tại **CÔNG TY TNHH INAFO VIỆT NAM**.

**Tổng tiền:** {{ number_format($order->total, 0, ',', '.') }} ₫

---

Bạn có thể tải hóa đơn PDF của đơn hàng bằng cách nhấn vào nút dưới đây:

@component('mail::button', ['url' => $pdfLink, 'color' => 'success'])
📄 Tải hóa đơn PDF
@endcomponent

Hoặc nếu nút trên không hoạt động, bạn có thể sao chép link này và dán vào trình duyệt:
<br>
<a href="{{ $pdfLink }}">{{ $pdfLink }}</a>

---

Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi!  
**CÔNG TY TNHH INAFO VIỆT NAM**

@endcomponent

