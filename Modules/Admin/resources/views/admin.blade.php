@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>{{ __('messages.dashboard') }}</h1>
<h3>{{ __('messages.language') }}</h3>
@stop

@section('content')
    {{-- @livewire('vn-address')    --}}
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}

    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
  
    <script>
        document.addEventListener('DOMContentLoaded', function () {            
            window.Echo.channel('chat')
                .listen('MessageSent', (e) => {
                    console.log('Tin nhắn nhận được:', e.message);
                })
                .error((error) => {
                    console.error('❌ Lỗi WebSocket:', error);
                });
                console.log('✅ Echo đã được khởi tạo');
        });
    </script>
@stop
