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

        {{-- Preloader Animation (fullscreen mode) --}}
        @if ($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @include('Admin::partials.navbar.navbar')

        {{-- Left Main Sidebar --}}
        @include('Admin::partials.sidebar.left-sidebar')

        {{-- Content Wrapper --}}
        @include('Admin::partials.content.content-default')

        {{-- Footer --}}
        @include('Admin::partials.footer.footer')

 

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')

@stop
