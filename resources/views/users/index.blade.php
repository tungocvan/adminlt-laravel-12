@extends('adminlte::page')

@section('title', 'Quản trị Thành viên')

@section('content_header')
    <h2>QUẢN TRỊ THÀNH VIÊN</h2>
@stop

@section('content')

@livewire('users.user-list')

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
