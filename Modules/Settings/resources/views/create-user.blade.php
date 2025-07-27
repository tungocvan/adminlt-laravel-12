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
<div class="container">
      
    <div class="card mt-5">
        <h3 class="card-header p-3"><i class="fa fa-star"></i> Laravel 12 Form Validation Example - ItSolutionStuff.com</h3>
        <div class="card-body">
            @session('success')
                <div class="alert alert-success" role="alert"> 
                    {{ $value }}
                </div>
            @endsession
          
            <!-- Way 1: Display All Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
             
            <form method="POST" action="{{ route('settings.store-user.store') }}">
            
                {{ csrf_field() }}
            
                <div class="mb-3">
                    <label class="form-label" for="inputName">Name:</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="inputName"
                        class="form-control @error('name') is-invalid @enderror" 
                        placeholder="Name">
      
                    <!-- Way 2: Display Error Message -->
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="inputName">User Name:</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="inputUserName"
                        class="form-control @error('username') is-invalid @enderror" 
                        placeholder="User Name">
      
                    <!-- Way 2: Display Error Message -->
                    @error('username')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
           
                <div class="mb-3">
                    <label class="form-label" for="inputPassword">Password:</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="inputPassword"
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Password">
      
                    <!-- Way 3: Display Error Message -->
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
             
                <div class="mb-3">
                    <label class="form-label" for="inputEmail">Email:</label>
                    <input 
                        type="text" 
                        name="email" 
                        id="inputEmail"
                        class="form-control @error('email') is-invalid @enderror" 
                        placeholder="Email">
      
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @endif
                </div>
           
                <div class="mb-3">
                    <button class="btn btn-success btn-submit"><i class="fa fa-save"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>       
          
</div>
    {{-- @livewire('form.form-list') --}}
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
