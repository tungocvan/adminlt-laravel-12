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
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <a href="/file/json-excel" type="button" class="btn btn-outline-primary btn-block"> <i class="fas fa-file-code mx-2"></i>JSON TO EXCEL</a>           
        </div>
        <div class="col-md-4">            
            <a href="/file/db-excel" type="button" class="btn btn-outline-info btn-block"><i class="fa fa-book mx-2"></i>DB TO EXCEL</a>            
        </div>
        <div class="col-md-4">            
            <a href="/file/migrations" type="button" class="btn btn-outline-info btn-block"><i class="fa fa-book mx-2"></i>Manager Migrations</a>            
        </div>
       
</div>

@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
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
