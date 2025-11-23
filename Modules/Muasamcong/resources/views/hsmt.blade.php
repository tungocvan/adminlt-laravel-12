@extends('adminlte::page')

@section('title', 'New Module')


@section('content') 
<div class="container">
    {{-- @livewire('muasamcong.hsmt') --}}
    @livewire('muasamcong.search-hsmt')
</div>

@endsection

@section('css')
@stack('styles')
    {{-- Sử dụng ở component @push(css)<style>...</style>@endpush ở cuối file --}}
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
@stack('scripts')
     {{-- Sử dụng ở component @push(js)<script>...</script>@endpush ở cuối file --}}
     {{-- https://www.daterangepicker.com/#examples  --}}

      <script type="text/javascript">
        $(document).ready(function () {
            console.log("Sử dụng Jquery"); 
            console.log("Các JS script của component sẻ được ưu tiên tải trước"); 

        })
     </> 

     <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log("Lắng nghe sự kiện DOMContentLoaded được gọi trước jquery");
        })
     </script>
@stop
