<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>{{ $title ?? 'Page Title' }}</title>
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
   
        <style>
            .pagination {
        display: flex;
        justify-content: center;
        padding: 10px 0;
        }
    
        .pagination .page-link {
            border-radius: 5px !important;
            margin: 0 5px;
            color: #007bff;
        }
    
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
    
        .pagination .page-link:hover {
            background-color: #0056b3;
            color: white;
        }
     
        </style>
        @livewireStyles 
    </head>
    <body>
        @yield('content')
        @livewireScripts
    </body>
</html>