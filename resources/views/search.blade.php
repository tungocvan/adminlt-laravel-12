@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Demo Search User</h1>
@stop

@section('content')
    @livewire('user-search')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop

