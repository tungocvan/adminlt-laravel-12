@extends('adminlte::page')

@section('title', 'Laravel Firebase Push Notification to Android and IOS App Example ')

@section('content_header')
    <h1>Laravel Firebase Push Notification to Android and IOS App Examp</h1>
@stop

@section('content')
<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card">

                <div class="card-header">{{ __('Dashboard') }}</div>

     

                <div class="card-body">

                    @if (session('success'))

                        <div class="alert alert-success" role="alert">

                            {{ session('success') }}

                        </div>

                    @endif

    

                    <form action="{{ route('send.notification') }}" method="POST">

                        @csrf

                        <div class="form-group">

                            <label>Title</label>

                            <input type="text" class="form-control" name="title">

                        </div>

                        <div class="form-group">

                            <label>Body</label>

                            <textarea class="form-control" name="body"></textarea>

                          </div>

                        <button type="submit" class="btn btn-primary">Send Notification</button>

                    </form>

    

                </div>

            </div>

        </div>

    </div>

</div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    {{-- Component cảnh báo quyền thông báo --}}
    @include('components.notification-permission')
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>

    <!-- Config & Init -->
    <script src="/firebase-config.js"></script>
    <script src="/firebase-init.js"></script>
    <script>
        
    </script>
@stop

