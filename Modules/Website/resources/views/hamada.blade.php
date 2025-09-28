@extends('layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'HOME PAGE')

@section('content_header')
    <h1 id="page-header">HOME PAGE</h1>
@stop

@section('header')
      <h1>HEADER</h1>     
@endsection

@section('content')
      @livewire('hamada.hamada-content')
     
@stop

@section('footer')
      <h1>FOOTER</h1>     
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}


@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}

    

@stop
 