@extends('Website::layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'Đặt hàng thành công - ' . $order->order_code)


@push('styles')
<style>
    .img-thumbnail.border-success {
        border-color: #28a745 !important;
        border-width: 3px !important;
    }
</style>
@endpush

@section('header')
    @include('Website::partials.header')
@endsection
   

@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Success Card --}}
            <div class="card border-success shadow-sm">
                <div class="card-body text-center py-5">
                    {{-- Success Icon --}}
                    <div class="mb-4">
                        <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" 
                              style="width: 80px; height: 80px; font-size: 40px;">
                            ✓
                        </span>
                    </div>

                    {{-- Success Message --}}
                    <h1 class="h3 text-success mb-3">Đặt hàng thành công!</h1>
                    <p class="text-muted mb-4">
                        Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.
                    </p>

                    {{-- Order Code --}}
                    <div class="bg-light rounded p-3 mb-4">
                        <p class="mb-1 text-muted">Mã đơn hàng:</p>
                        <h4 class="mb-0 text-primary font-weight-bold">{{ $order->order_code }}</h4>
                    </div>
                </div>
            </div>

            {{-- Order Details --}}
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Chi tiết đơn hàng</h5>
                </div>
                <div class="card-body">
                    {{-- Customer Info --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Thông tin khách hàng</h6>
                            <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                            <p class="mb-1">{{ $order->customer_phone }}</p>
                            @if($order->customer_email)
                                <p class="mb-1">{{ $order->customer_email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Địa chỉ giao hàng</h6>
                            <p class="mb-0">{{ $order->customer_address }}</p>
                        </div>
                    </div>

                    @if($order->note)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Ghi chú</h6>
                            <p class="mb-0">{{ $order->note }}</p>
                        </div>
                    @endif

                    {{-- Order Items --}}
                    <h6 class="text-muted mb-3">Sản phẩm đã đặt</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center" style="width: 100px;">Số lượng</th>
                                    <th class="text-right" style="width: 150px;">Đơn giá</th>
                                    <th class="text-right" style="width: 150px;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                         alt="{{ $item->product_name }}"
                                                         class="img-thumbnail mr-3"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center mr-3"
                                                         style="width: 50px; height: 50px;">
                                                        <span class="text-muted">📦</span>
                                                    </div>
                                                @endif
                                                <span>{{ $item->product_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                        <td class="text-right font-weight-bold">{{ number_format($item->total, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">Tạm tính:</td>
                                    <td class="text-right">{{ number_format($order->subtotal, 0, ',', '.') }}đ</td>
                                </tr>
                                @if($order->discount > 0)
                                    <tr class="text-success">
                                        <td colspan="3" class="text-right">Giảm giá:</td>
                                        <td class="text-right">-{{ number_format($order->discount, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="3" class="text-right font-weight-bold">Tổng cộng:</td>
                                    <td class="text-right font-weight-bold h5 mb-0">{{ number_format($order->total, 0, ',', '.') }}đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Order Status --}}
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Trạng thái đơn hàng:</span>
                            <span class="badge {{ $order->status_badge_class }} px-3 py-2">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="text-center mt-4">
                <a href="{{ route('website.products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag mr-2"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>

@stop

@section('footer')
@include('Website::partials.footer')
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    

    </style>

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}

    
    
@stop
 