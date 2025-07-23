
@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Upload Image Resize</h1>
@stop

@section('content')
<div class="container">
  
    <div class="card mt-5">
        <h3 class="card-header p-3"><i class="fa fa-star"></i> Laravel 12 Resize Image Before Upload Example - ItSolutionStuff.com</h3>
        <div class="card-body">

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
  
            @session('success')
                <div class="alert alert-success" role="alert"> 
                    {{ $value }}
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <strong>Original Image:</strong>
                        <br/>
                        <img src="/images/{{ Session::get('imageName') }}" width="300px" />
                    </div>
                    <div class="col-md-4">
                        <strong>Thumbnail Image:</strong>
                        <br/>
                        <img src="/images/thumbnail/{{ Session::get('imageName') }}" />
                    </div>
                </div>
            @endsession
            
            <form action="{{ route('upload.image-resize.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
        
                <div class="mb-3">
                    <label class="form-label" for="inputImage">Image:</label>
                    <input 
                        type="file" 
                        name="image" 
                        id="inputImage"
                        class="form-control @error('image') is-invalid @enderror">
        
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
         
                <div class="mb-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Upload</button>
                </div>
             
            </form>
        </div>
    </div>
</div>    

@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
@stop

@section('js')
   
@stop

