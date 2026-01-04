@extends('Website::layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', $product->title)
@section('meta_description', Str::limit($product->short_description, 160))

@push('styles')
<style>
    .img-thumbnail.border-success {
        border-color: #28a745 !important;
        border-width: 3px !important;
    }
</style>
@endpush

@section('header')
    @include('Website::partials.header')
@endsection
   

@section('content')
    
    <div class="container">                                 
        @livewire('website.products.product-detail', ['slug' => $slug])
    </div>
@stop

@section('footer')
@include('Website::partials.footer')
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    

    </style>

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}

    
    
@stop
 