@extends('adminlte::page')

@section('content')
<div class="container mt-4">
    <h4>Cập nhật trạng thái đơn hàng #{{ $order->id }}</h4>

    <form method="POST" action="{{ route('order.update', $order) }}">
        @csrf @method('PUT')

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Lưu thay đổi</button>
        <a href="{{ route('order.index') }}" class="btn btn-secondary mt-3">Hủy</a>
    </form>
</div>
@endsection
