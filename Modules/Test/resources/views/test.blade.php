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

@section('content') 
    @livewire('test.test-list') 
@endsection

@section('css')
@stack('styles')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
@stack('scripts')
     {{-- https://www.daterangepicker.com/#examples  --}}

      <script type="text/javascript">
        $(document).ready(function () {
            console.log("Sử dụng Jquery"); 
            console.log("Các JS script của component sẻ được ưu tiên tải trước"); 
        })
     </script> 

     <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log("Lắng nghe sự kiện DOMContentLoaded được gọi trước jquery");
        })
     </script>
     
@stop
