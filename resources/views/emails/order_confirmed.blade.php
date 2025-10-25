<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đơn hàng đã xác nhận</title>
</head>
<body>
    <h2>Đơn hàng #{{ $order->id }} đã được xác nhận!</h2>
    <p>Cảm ơn bạn đã đặt hàng.</p>

    <p><strong>Tổng: {{ number_format($order->total, 0, ',', '.') }} ₫</strong></p>

    <p>Bạn có thể tải hóa đơn PDF của đơn hàng tại đây:</p>
    <p><a href="{{ $pdfLink }}" target="_blank">{{ $pdfLink }}</a></p>
</body>
</html>
