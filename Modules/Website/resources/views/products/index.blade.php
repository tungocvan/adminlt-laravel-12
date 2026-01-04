@extends('Website::layouts.website')
@section('plugins.Toastr', true)
@section('title', 'PRODUCT LIST PAGE')

@section('content_header')

@stop

@section('header')
    @include('Website::partials.header')
@endsection
   

@section('content')    
    <div class="container">                                        
        @livewire('website.products.product-list')
    </div>
@stop

@section('footer')
@include('Website::partials.footer')
@endsection

@section('css')

@stop

@section('js')
        
@stop
 