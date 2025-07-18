@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Manager Products</h1>
@stop

@section('content')
      {{-- @livewire('env.env-list') --}}
    
      @yield('content')  
      
      @yield('scripts')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style type="text/css">
        .dropdown{
            float:right;
            padding-right: 30px;
        }
        .dropdown .dropdown-menu{
            padding:20px;
            top:30px !important;
            width:350px !important;
            left:0px !important;
            box-shadow:0px 5px 30px black;
        }
        .dropdown-menu:before{
            content: " ";
            position:absolute;
            top:-20px;
            right:50px;
            border:10px solid transparent;
            border-bottom-color:#fff;
        }
        .fs-8{
            font-size: 13px;
        }
    </style>
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    
   
@stop
