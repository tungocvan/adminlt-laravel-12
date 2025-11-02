@extends('adminlte::page')
 
@section('title', 'New Module')

@section('content_header')
    {{-- 
            khai báo ngôn ngữ: 
            tiếng anh: resources/lang/en
            tiếng việt: resources/lang/vi 
        --}}
    <h3>{{ __('messages.language') }}</h3>
@stop
@php
    $menuItems = [
        ['url' => '/file/json-excel', 'icon' => 'fas fa-file-code', 'text' => 'JSON TO EXCEL', 'color' => 'primary'],
        ['url' => '/file/db-excel', 'icon' => 'fas fa-database', 'text' => 'DB TO EXCEL', 'color' => 'info'],
        [
            'url' => '/file/migrations',
            'icon' => 'fas fa-code-branch',
            'text' => 'MANAGER MIGRATIONS',
            'color' => 'secondary',
        ],
        ['url' => '/file/artisan', 'icon' => 'fas fa-terminal', 'text' => 'THỰC HIỆN ARTISAN', 'color' => 'warning'],
        ['url' => '/file/env', 'icon' => 'fas fa-cog', 'text' => 'MANAGER ENV', 'color' => 'success'],
        
    ];
@endphp
@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">
                    <i class="fas fa-tools mr-2"></i>File & System Manager
                </h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @foreach ($menuItems as $item)
                        <div class="col-md-4 mb-3">
                            <a href="{{ $item['url'] }}" class="btn btn-outline-{{ $item['color'] }} btn-block py-3">
                                <i class="{{ $item['icon'] }} fa-lg mb-2"></i><br>
                                {{ $item['text'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>
        /* Hover hiệu ứng nhẹ */
        .btn-block {
            transition: all 0.2s ease-in-out;
        }

        .btn-block:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
@stop

@section('js')
    {{-- https://www.daterangepicker.com/#examples  --}}

    <script type="text/javascript">
        $(document).ready(function() {
            console.log("Sử dụng Jquery");
        })
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log("Lắng nghe sự kiện DOMContentLoaded được gọi trước jquery");
        })
    </script>
@stop
