@extends('layouts.app')

@section('title', 'In đơn hàng #' . $order->id)

@section('content')
    <p>{{ $type }}</p>
    @if(View::exists("Order::$type"))    
        @include("Order::$type")
    @endif
@endsection
