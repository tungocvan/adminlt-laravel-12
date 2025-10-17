@extends('adminlte::page')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Danh sách đơn hàng</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Email</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th width="160">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->email }}</td>
                <td>{{ number_format($order->total, 0, ',', '.') }}đ</td>
                <td>
                    <span class="badge badge-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'confirmed' ? 'success' : 'danger') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('order.show', $order) }}" class="btn btn-sm btn-info">Xem</a>
                    <a href="{{ route('order.edit', $order) }}" class="btn btn-sm btn-primary">Sửa</a>
                    <form action="{{ route('order.destroy', $order) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Bạn có chắc muốn xóa?')" class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Chưa có đơn hàng</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $orders->links('pagination::bootstrap-4') }}

    </div>
    
</div>
@endsection
