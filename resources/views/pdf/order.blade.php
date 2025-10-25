<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đơn hàng #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <h2>HÓA ĐƠN ĐƠN HÀNG #{{ $order->id }}</h2>

    <div class="info">
        <p><strong>Khách hàng:</strong> {{ $order->user->name ?? 'Khách lẻ' }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        @if(!empty($order->order_note))
            <p><strong>Ghi chú khách hàng:</strong> {{ $order->order_note }}</p>
        @endif
        @if(!empty($order->admin_note))
            <p><strong>Ghi chú admin:</strong> {{ $order->admin_note }}</p>
        @endif
        <p><strong>Trạng thái:</strong> 
            {{ $order->status === 'pending' ? 'Chờ xử lý' : ($order->status === 'confirmed' ? 'Đã xác nhận' : 'Đã hủy') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['title'] ?? 'N/A' }}</td>
                    <td>{{ $item['quantity'] ?? 0 }}</td>
                    <td>{{ number_format($item['price'] ?? $item['total'], 0, ',', '.') }} ₫</td>
                    <td>{{ number_format($item['total'], 0, ',', '.') }} ₫</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Tổng tiền: {{ number_format($order->total, 0, ',', '.') }} ₫</p>

    <p style="text-align:center; font-size:11px; margin-top:30px;">
        Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!
    </p>

</body>
</html>
