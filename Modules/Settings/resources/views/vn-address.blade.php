@extends('adminlte::page')

@section('title', 'TABLES MANAGER')

@section('content_header')
    <h1>Địa chỉ các tỉnh thành</h1>
@stop

@section('content')
    @livewire('vn-address')  
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css"> --}}
    
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
