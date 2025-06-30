@extends('adminlte::page')
@section('plugins.Summernote', true)
@section('title', 'Settings')

@section('content_header')
    <h1>Edit Products</h1>
@stop

@section('content')
      @livewire('products.edit-product',['id' => $id])
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>
        .dropdown-menu {
            max-height: 300px; /* Chiều cao tối đa */
            overflow-y: auto; /* Cuộn dọc */
        }
        
        /* Đảm bảo CSS này được tải SAU Bootstrap CSS */

        .image-wrapper {
            width: 80px; /* Phù hợp với width của img-thumbnail */
            height: 80px; /* Phù hợp với height của img-thumbnail */
            overflow: hidden; /* Đảm bảo nút X không bị tràn ra ngoài nếu nó quá lớn */
            border-radius: .25rem; /* Match Bootstrap's img-thumbnail border-radius */
            display: inline-block; /* Để các wrapper xếp cạnh nhau */
        }

        /* Đảm bảo ảnh chiếm hết không gian trong wrapper */
        .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .custom-close-btn {
            position: absolute;
            top: -5px; /* Điều chỉnh vị trí */
            right: -5px; /* Điều chỉnh vị trí */
            z-index: 10;
            opacity: 0; /* Mặc định ẩn */
            transition: opacity 0.2s ease-in-out; /* Hiệu ứng mờ dần */
            background-color: rgba(255, 255, 255, 0.8); /* Màu nền nhẹ cho nút X */
            border-radius: 50%; /* Làm tròn nút */
            padding: 0.1rem; /* Điều chỉnh padding để làm nút nhỏ hơn */
            box-shadow: 0 0 5px rgba(0,0,0,0.2); /* Thêm bóng nhẹ */
        }

        .image-wrapper:hover .custom-close-btn {
            opacity: 1; /* Hiển thị khi hover vào wrapper */
        }

        /* Nếu bạn muốn nút X nhỏ hơn nữa, bạn có thể override kích thước của btn-close */
        .custom-close-btn:focus {
            box-shadow: none; /* Bỏ shadow mặc định của Bootstrap khi focus */
        }
    
    </style>
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
