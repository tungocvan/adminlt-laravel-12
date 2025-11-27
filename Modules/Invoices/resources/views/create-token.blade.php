@extends('adminlte::page')
{{-- @section('plugins.Select2', true) --}}
@section('title', 'CREATETOKEN')

@section('content_header')
        {{-- 
            khai báo ngôn ngữ: 
            tiếng anh: resources/lang/en
            tiếng việt: resources/lang/vi 
        --}}
        {{-- <h3>{{ __('messages.language') }}</h3> --}}
@stop

@section('content') 
<div class="container-fluid p-2">
    @livewire('invoices.gdt-login') 
    <hr>
    @livewire('invoices.gdt-invoice') 
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
