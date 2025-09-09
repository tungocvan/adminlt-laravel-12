@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (Route::is('auth.login'))
                @livewire('auth.login')
            @elseif (Route::is('auth.register'))
                @livewire('auth.register')            
            @elseif (Route::is('auth.forgot'))
                @livewire('auth.forgot')
            @endif

        </div>
    </div>
</div>
@endsection
