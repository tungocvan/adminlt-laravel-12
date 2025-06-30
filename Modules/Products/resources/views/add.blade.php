@extends('adminlte::page')
@section('plugins.Summernote', true)
@section('title', 'Settings')

@section('content_header')
    <h1>Add Products</h1>
@stop

@section('content')
      @livewire('products.add-product')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>
        .dropdown-menu {
            max-height: 300px; /* Chiều cao tối đa */
            overflow-y: auto; /* Cuộn dọc */
        }
    </style>
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
