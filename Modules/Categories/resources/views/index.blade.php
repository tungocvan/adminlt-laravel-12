@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Header Categories</h1>
@stop

@section('content')
      @livewire('admin.categories-manager')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
