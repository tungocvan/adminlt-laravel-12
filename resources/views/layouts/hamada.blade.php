@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">     
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
<header>
    @yield('header')
</header>
<div class="wrapper">    
   
    <main class="content">
        @yield('content_header')
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>
</div>   
<footer>
    @yield('footer')
</footer>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')    
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>  
@stop
