@extends('adminlte::page')

@section('title', 'New Module')

@section('content_header')
        {{-- 
            khai báo ngôn ngữ: 
            tiếng anh: resources/lang/en
            tiếng việt: resources/lang/vi 
        --}}
        {{-- <h3>{{ __('messages.language') }}</h3> --}}
@stop

@section('content') 
<div class="container">
    @livewire('invoices.search-hoadon')
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
  
@stop
