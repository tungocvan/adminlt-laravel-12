@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Index Upload</h1>
@stop

@section('content')
      {{-- @livewire('upload.upload-image')
      @livewire('upload.upload-images')
      @livewire('upload.photo-upload') --}}

      {{-- <livewire:volt-image-upload multi /> --}}
      <div x-data @image-uploaded.window="alert('Đã upload: ' + $event.detail.join(', '))">
        <h3>Chọn ảnh và tải lên</h3>
        <livewire:volt-image-upload />
    </div>
    
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop

