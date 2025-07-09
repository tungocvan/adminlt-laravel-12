@extends('adminlte::page')
@section('plugins.Select2', true)
{{-- @section('plugins.jsGrid', true) --}}
{{-- @section('plugins.Summernote', true) --}}
{{-- @section('plugins.TempusDominusBs4', true) --}} 
{{-- @section('plugins.Toastr', true) --}}
{{-- @section('plugins.BsCustomFileInput', true) --}}
{{-- @section('plugins.FileManager', true) --}}
{{-- @section('plugins.Datatables', true) --}}
{{-- @section('plugins.DatatablesPlugins', true) --}}
@section('plugins.DateRangePicker', true)
@section('title', 'FORM')

@section('content_header')
    <h1>FORM TABS PANEL</h1>
@stop

@section('content')
<div class="container">
    <div class="card mt-5">
        <div class="card-header"><h4>Laravel Example</h4></div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ @when(!request()->tab, 'active') }}" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ @when(request()->tab == 'profile', 'active') }}" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ @when(request()->tab == 'contact', 'active') }}" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade  {{ @when(!request()->tab, 'show active') }}" id="home" role="tabpanel" aria-labelledby="home-tab"><br/>
              This is Home Tab
              </div>

              <div class="tab-pane fade {{ @when(request()->tab == 'profile', 'show active') }}" id="profile" role="tabpanel" aria-labelledby="profile-tab"><br/>
              This is Profile Tab
              </div>

              <div class="tab-pane fade {{ @when(request()->tab == 'contact', 'show active') }}" id="contact" role="tabpanel" aria-labelledby="contact-tab"><br/>
              This is Contact Tab
              </div>
            </div>
        </div>
    </div>
</div>

  
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script> --}}
    {{-- <script>
         function save() {
          
            let data = $(`#question-content`).val();
            console.log('data:',data);
         }
    </script> --}}

@stop
