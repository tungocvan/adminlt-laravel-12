@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    {{-- <h1>Manager User</h1> --}}
@stop

@section('content')
      @livewire('users.user-list')
@stop

@section('css')
@stack('styles')
    {{-- Sử dụng ở component @push(css) --}}
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
 
@stop

@section('js')
@stack('scripts')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package! "); </script>

@stop
