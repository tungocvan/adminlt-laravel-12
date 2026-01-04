@extends('Website::layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'Giỏ hàng')


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
  

    {{-- Page Title --}}
    <h1 class="h3 mb-4">Giỏ hàng của bạn</h1>

    {{-- Alert Messages --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Cart List Component --}}
    @livewire('website.cart.cart-list')
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
 