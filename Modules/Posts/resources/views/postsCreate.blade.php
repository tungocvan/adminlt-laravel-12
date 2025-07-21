@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Manager Post</h1>
@stop

@section('content')
<div class="container">
    <div class="card mt-5">
        <h3 class="card-header p-3">Laravel 12 Summernote Editor Image Upload Example - ItSolutionStuff.com</h3>
        <div class="card-body"> 
            <form method="post" action="{{ route('posts.store-post') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="summernote" name="body"></textarea>
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-block">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" />

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js"></script>
        
    <script type="text/javascript">
        $(document).ready(function () {
            $('#summernote').summernote({
                height: 300,
            });
        });
</script>

@stop

