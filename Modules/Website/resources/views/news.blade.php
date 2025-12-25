@extends('Website::layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'HOME PAGE')

@section('content_header')
    {{-- <h1 id="page-header">HOME PAGE1</h1> --}}
@stop

@section('header')
    @include('Website::partials.header')
@endsection
   

@section('content')
    
    <div class="container">                   
        {{-- <livewire:slider /> --}}
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-success" style="border-radius:12px;">
        
                {{-- Badge giảm giá --}}
                <span class="badge badge-danger position-absolute"
                      style="top:10px;left:10px;font-size:14px;">
                    -4%
                </span>
        
                {{-- Yêu thích --}}
                <span class="position-absolute"
                      style="top:10px;right:10px;font-size:22px;color:#ccc;cursor:pointer;">
                    ♡
                </span>
        
                {{-- Ảnh sản phẩm --}}
                <div class="text-center pt-4">
                    <img src="https://gcs.buymed.com/thuocsi-live/images/2024d52b50ff9f68cb3e2363bca5787b"
                         class="img-fluid"
                         style="max-height:160px;"
                         alt="">
                </div>
        
                {{-- Nhãn --}}
                <div class="px-3 mt-2">
                    <span class="badge badge-danger">KHUYẾN MÃI</span>
                    <span class="badge badge-primary">COMBO</span>
                </div>
        
                {{-- Giá --}}
                <div class="px-3 mt-3">
                    <h5 class="text-success font-weight-bold mb-1">
                        15.232.800đ
                    </h5>
                    <small class="text-muted">
                        <del>16.288.000đ</del>
                    </small>
                </div>
        
                {{-- Tên sản phẩm --}}
                <div class="px-3 mt-2">
                    <strong>
                        Combo 160 Magne B6 Corbiere Sanofi (Hộp/50 viên)
                    </strong>
                </div>
        
                {{-- Quy cách --}}
                <div class="px-3 mt-1 text-muted" style="font-size:14px;">
                    Hộp 5 Vỉ x 10 Viên Nén Bao Phim
                </div>
        
                {{-- Nhà cung cấp --}}
                <div class="px-3 mt-2">
                    <i class="fa fa-store text-primary"></i>
                    <strong>Opella (Sanofi) Việt Nam</strong>
                </div>
        
                {{-- Trạng thái --}}
                <div class="px-3 mt-2">
                    <span class="badge badge-pill badge-danger px-3 py-1">
                        Đang Bán Chạy
                    </span>
                </div>
        
                <div class="px-3 text-muted mt-1" style="font-size:13px;">
                    Đặt tối đa 1000 sản phẩm
                </div>
        
                {{-- Điều khiển số lượng --}}
                <div class="card-footer bg-white border-0">
                    <div class="d-flex align-items-center justify-content-between
                                border border-success rounded"
                         style="height:48px;">
                
                        {{-- Nút giảm --}}
                        <button class="btn btn-link text-secondary px-3 border-right "
                                style="font-size:24px;text-decoration:none;">
                            −
                        </button>
                
                        {{-- Giỏ hàng + số lượng --}}
                        <div class="d-flex align-items-center">
                            <i class="fa fa-shopping-cart text-muted mr-2"></i>
                            <span class="font-weight-bold">0</span>
                        </div>
                
                        {{-- Nút tăng --}}
                        <button class="btn btn-link text-success px-3 border-left "
                                style="font-size:24px;text-decoration:none;">
                            +
                        </button>
                
                    </div>
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
 