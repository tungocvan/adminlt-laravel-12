@extends('Website::layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'Thanh toán')


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
    
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
                <a href="{{ route('website.index') }}">Trang chủ</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('website.cart.index') }}">Giỏ hàng</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
        </ol>
    </nav>

    {{-- Page Title --}}
    <h1 class="h3 mb-4">Thanh toán đơn hàng</h1>

    {{-- Alert Messages --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Checkout Form --}}
        <div class="col-lg-7 mb-4">
            @livewire('website.checkout.checkout-form')
        </div>

        {{-- Order Summary --}}
        <div class="col-lg-5">
            @livewire('website.checkout.order-summary')
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
 