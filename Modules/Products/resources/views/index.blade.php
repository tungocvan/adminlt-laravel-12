@extends('adminlte::page')
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'Danh sách các sản phẩm')

@section('content_header')
    <h1 id="page-header">Danh sách các sản phẩm</h1>
@stop

@section('content')
      @livewire('products.product-manager')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}


@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>


@stop
