@extends('layouts.print')

@section('title', 'Đơn hàng #' . $order->id)

@section('content')
    <h2>ĐƠN HÀNG #{{ $order->id }}</h2>

    <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Email khách hàng:</strong> {{ $order->email }}</p>
    <p><strong>Trạng thái:</strong> {{ ucfirst($order->status) }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($order->order_detail as $index => $item)
                @php $lineTotal = $item['price'] * $item['quantity']; $total += $lineTotal; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td class="text-right">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                    <td class="text-right">{{ number_format($lineTotal, 0, ',', '.') }} đ</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="4" class="text-right">Tổng cộng</th>
                <th class="text-right text-danger">{{ number_format($total, 0, ',', '.') }} đ</th>
            </tr>
        </tbody>
    </table>
@endsection
