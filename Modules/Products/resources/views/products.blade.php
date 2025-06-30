@extends('adminlte::page')
@section('plugins.xlsx', true)
@section('plugins.jsGrid', true)

@section('title', 'Settings')

@section('content_header')
    {{-- <h1>Products List</h1> --}}
@stop

@section('content')
      @livewire('products.list-product')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
