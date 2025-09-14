@extends('adminlte::page')

@section('title', 'Categories')

@section('content_header')
    <h1>Index Categories</h1>
@stop

@section('content')
    @livewire('category.category-list')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
