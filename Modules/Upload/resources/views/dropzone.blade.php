@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Dropzone Upload</h1>
@stop

@section('content')
    @livewire('upload.upload-dropzone')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
      .dz-preview .dz-image img{
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
      }
    </style>

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>    
    <script type="text/javascript">
          
                Dropzone.autoDiscover = false;
        
                var images = {{ Js::from($images) }};
          
                var myDropzone = new Dropzone(".dropzone", { 
                    init: function() { 
                        myDropzone = this;
        
                        $.each(images, function(key,value) {
                            var mockFile = { name: value.name, size: value.filesize};
             
                            myDropzone.emit("addedfile", mockFile);
                            myDropzone.emit("thumbnail", mockFile, value.path);
                            myDropzone.emit("complete", mockFile);
                  
                        });
                    },
                   autoProcessQueue: false,
                   paramName: "files",
                   uploadMultiple: true,
                   maxFilesize: 5,
                   acceptedFiles: ".jpeg,.jpg,.png,.gif"
                });
              
                $('#uploadFile').click(function(){
                   myDropzone.processQueue();
                });
          
        </script>

@stop

