@extends('adminlte::page')

@section('content')
<div class="container mt-4">
    <h4>Chi tiết đơn hàng #{{ $order->id }}</h4>
    <p><strong>Email:</strong> {{ $order->email }}</p>
    <p><strong>Trạng thái:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Tổng tiền:</strong> {{ number_format($order->total, 0, ',', '.') }}đ</p>
    <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>

    <h5 class="mt-4">Sản phẩm trong đơn hàng:</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên SP</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->order_detail as $item)
            <tr>
                <td>{{ $item['name'] ?? '-' }}</td>
                <td>{{ $item['qty'] ?? 1 }}</td>
                <td>{{ number_format($item['price'] ?? 0, 0, ',', '.') }}đ</td>
                <td>{{ number_format(($item['qty'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">← Quay lại</a>
</div>
@endsection
