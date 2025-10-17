@extends('adminlte::page')

@section('content')

    <div class="row justify-content-center mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h3>Nội dung....</h3>
                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>

@endsection
