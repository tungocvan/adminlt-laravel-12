@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Image Gallery CRUD Example</h1>
@stop

@section('content')
<div class="container">



    <h3>Laravel - Image Gallery CRUD Example</h3>

    <form action="{{ url('upload/image-gallery') }}" class="form-image-upload" method="POST" enctype="multipart/form-data">



        {!! csrf_field() !!}



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



        @if ($message = Session::get('success'))

        <div class="alert alert-success alert-block">

            <button type="button" class="close" data-dismiss="alert">×</button>

                <strong>{{ $message }}</strong>

        </div>

        @endif



        <div class="row">

            <div class="col-md-5">

                <strong>Title:</strong>

                <input type="text" name="title" class="form-control" placeholder="Title">

            </div>

            <div class="col-md-5">

                <strong>Image:</strong>

                <input type="file" name="image" class="form-control">

            </div>

            <div class="col-md-2">

                <br/>

                <button type="submit" class="btn btn-success">Upload</button>

            </div>

        </div>



    </form> 



    <div class="row">

    <div class='list-group gallery'>

            @if($images->count())

                @foreach($images as $image)
                <div class='col-sm-4 col-xs-6 col-md-3 col-lg-3'>
                    <a class="thumbnail fancybox" rel="ligthbox" href="/images/{{ $image->image }}">
                        <img class="img-responsive" alt="" src="/images/{{ $image->image }}" width="200"/>
                        <div class='text-center'>
                            <small class='text-muted'>{{ $image->title }}</small>
                        </div> <!-- text-center / end -->
                    </a>
                    <form action="{{ url('upload/image-gallery',$image->id) }}" method="POST">
                    <input type="hidden" name="_method" value="delete">
                    {!! csrf_field() !!}
                    <button type="submit" class="close-icon btn btn-danger"><i class="fas fa-times"></i>
                    </button>
                    </form>

                </div> <!-- col-6 / end -->

                @endforeach

            @endif



        </div> <!-- list-group / end -->

    </div> <!-- row / end -->

</div> <!-- container / end -->
    
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <style type="text/css">
        .gallery    
        {
            display: inline-block;    
            margin-top: 20px;    
        }    
        .close-icon{    
            border-radius: 50%;    
            position: absolute;    
            right: -160px;    
            top: -10px;    
            padding: 5px 8px;    
        }    
        .form-image-upload{    
            background: #e8e8e8 none repeat scroll 0 0;    
            padding: 15px;    
        }    
    </style>
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function(){
    
            $(".fancybox").fancybox({
    
                openEffect: "none",
    
                closeEffect: "none"
    
            });
    
        });
    
    </script>

@stop

