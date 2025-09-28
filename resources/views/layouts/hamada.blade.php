@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
<div class="wrapper">    
    <header>
        @yield('header')
    </header>
    <main class="content">
        @yield('content_header')
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>
    <header>
        @yield('footer')
    </header>

</div>   
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')      
@stop
