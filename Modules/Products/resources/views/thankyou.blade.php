@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
<h2>Cảm ơn bạn đã đặt hàng!</h2>
@stop

@section('content')
<div class="text-center mt-5">
   
    <p>Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Quay lại trang chủ</a>
</div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
