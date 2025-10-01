@extends('layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'HOME PAGE')

@section('content_header')
    {{-- <h1 id="page-header">HOME PAGE1</h1> --}}
@stop

@section('header')
    @include('Website::partials.header')
@endsection
   

@section('content')
    <div class="container">
        <section class="content mt-2">
            <!-- Default box -->
            <div class="card">
                <div class="card-body row">
                    <div class="col-5 text-center d-flex align-items-center justify-content-center">
                        <div class="">
                            <h2>Admin<strong>LTE</strong></h2>
                            <p class="lead mb-5">
                                123 Testing Ave, Testtown, 9876 NA<br />
                                Phone: +1 234 56789012
                            </p>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="form-group">
                            <label for="inputName" class="">Name</label>
                            <input type="text" id="inputName" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="inputEmail" class="">E-Mail</label>
                            <input type="email" id="inputEmail" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="inputSubject" class="">Subject</label>
                            <input type="text" id="inputSubject" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="inputMessage">Message</label>
                            <textarea id="inputMessage" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Send message" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
    </div>
@stop

@section('footer')
@include('Website::partials.footer')
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}


@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}

    

@stop
 