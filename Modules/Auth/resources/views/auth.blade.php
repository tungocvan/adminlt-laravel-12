@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (Route::is('login'))             
                @livewire('auth.login')
            @elseif (Route::is('register'))
                @livewire('auth.register')            
            @elseif (Route::is('forgot'))
                @livewire('auth.forgot')
            @endif

        </div>
    </div>
</div>
@endsection
