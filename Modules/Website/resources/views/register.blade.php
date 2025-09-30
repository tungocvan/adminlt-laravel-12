@extends('layouts.hamada')
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
    <h1>Đăng ký bán hàng</h1>
@stop

@section('footer')
@include('Website::partials.footer')
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}


@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}

    

@stop
 