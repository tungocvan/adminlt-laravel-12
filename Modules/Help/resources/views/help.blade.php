@extends('adminlte::page')

@section('title', 'New Module')

@section('content_header')

@stop

@section('content') 
<div class="container-fluid py-2">
    {{-- @livewire('help.help-manager')     --}}
    @livewire('help.help-list', ['currentFile' => request()->segment(2)])    

</div>

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
        })
     </script> 

     <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log("Lắng nghe sự kiện DOMContentLoaded được gọi trước jquery");
        })
     </script>
@stop
