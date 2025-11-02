@extends('adminlte::page')

@section('title', 'Thực hiện quản lý ENV')
@section('plugins.Toastr', true)
@section('content_header')
    <a href="{{ route('file.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@stop

@section('content')
    @livewire('env.env-list')

@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

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
