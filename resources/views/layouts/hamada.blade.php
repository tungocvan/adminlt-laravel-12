@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
    <style>
      body {
  padding-top: 152px; /* 80px banner + 72px menu */
}

      .nav .nav-link {
            position: relative;
            padding-bottom: 5px; /* tạo khoảng cách cho gạch */
        }

        .nav .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;               /* độ dày gạch */
            background-color: #007bff; /* màu gạch */
        }

    </style>
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
@stop
