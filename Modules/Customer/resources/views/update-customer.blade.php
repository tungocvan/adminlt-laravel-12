@extends('adminlte::page')
 
@section('title', 'CẬP NHẬT KHÁCH HÀNG')

@section('content_header')
    {{-- 
            khai báo ngôn ngữ: 
            tiếng anh: resources/lang/en
            tiếng việt: resources/lang/vi 
        --}}
    {{-- <h3>{{ __('messages.language') }}</h3> --}}
@stop

@section('content')
    @livewire('customer.update-customer')
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
