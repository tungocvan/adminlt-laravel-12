@extends('adminlte::page')

@section('title', 'Nguyễn Thị Định')

@section('content_header')
    {{-- <h1>{{ __('messages.dashboard') }}</h1>
    <h3>{{ __('messages.language') }}</h3> --}}
@stop
  
@section('content')
    @livewire('ntd.hocsinh-list')       
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}

    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>   
        

@stop
