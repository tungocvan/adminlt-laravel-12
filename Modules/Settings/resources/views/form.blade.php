@extends('adminlte::page')
@section('plugins.Select2', true)
{{-- @section('plugins.jsGrid', true) --}}
@section('plugins.Summernote', true)
{{-- @section('plugins.TempusDominusBs4', true) --}} 
{{-- @section('plugins.Toastr', true) --}}
{{-- @section('plugins.BsCustomFileInput', true) --}}
{{-- @section('plugins.FileManager', true) --}}
{{-- @section('plugins.Datatables', true) --}}
{{-- @section('plugins.DatatablesPlugins', true) --}}
@section('plugins.DateRangePicker', true)
@section('title', 'FORM')

@section('content_header')
    <h1>FORM</h1>
@stop

@section('content')
   
    @livewire('form.form-list')

    {{-- @livewire('image-uploader') --}}
    {{-- @livewire('files.file-manager',['name' => 'question','label' => 'Nhập nội dung'])
    <button type="button" class="btn btn-primary" onclick="save()">Submit</button> --}}
    {{-- @livewire('files.file-manager') --}}
    {{-- <div id="question-content" class="border p-2 mt-2 bg-light"></div> --}}
    {{-- <input id="question-content" type="text"> --}}
    {{-- @livewire('component', ['user' => $user], key($user->id)) --}}
  
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script> --}}
    {{-- <script>
         function save() {
          
            let data = $(`#question-content`).val();
            console.log('data:',data);
         }
    </script> --}}

@stop
