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
   {{-- gọi component livewire/User/UserList.php --}}
   {{-- @livewire('user.user-list') --}}
   {{-- Custom --}}
<x-adminlte-modal id="modalCustom" title="Account Policy" size="lg" theme="teal"
icon="fas fa-bell" v-centered static-backdrop scrollable>
<div style="height:800px;">Read the account policies...</div>
<x-slot name="footerSlot">
    <x-adminlte-button class="mr-auto" theme="success" label="Accept"/>
    <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
</x-slot>
</x-adminlte-modal>
{{-- Example button to open modal --}}
<x-adminlte-button label="Cập nhật Role" data-toggle="modal" data-target="#modalCustom" class="bg-teal"/>
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
            console.log("Các JS script của component sẻ được ưu tiên tải trước"); 
        })
     </script> 

     <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log("Lắng nghe sự kiện DOMContentLoaded được gọi trước jquery");
        })
     </script>
@stop
