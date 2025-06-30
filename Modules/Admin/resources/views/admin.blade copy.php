@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <x-adminlte-alert>

    <div class="row">
        <div class="col-12">
            <p>Welcome to this beautiful admin panel.</p>
            <x-tnv.button  label="Cập nhật" name="update" />
            <x-tnv.button  label="Xóa" id="delete" theme="danger" />
            <x-tnv.button href="/admin" quanlity="100" icon="fas fa-list" />
        </div>
        <div class="col-6">
            <x-adminlte-card title="Form Card" theme="purple" theme-mode="outline"
    class="elevation-3" body-class="bg-purple" header-class="bg-light"
    footer-class="bg-purple border-top rounded border-light"
    icon="fas fa-lg fa-bell" collapsible removable maximizable>
        <x-slot name="toolsSlot">
            <select class="custom-select w-auto form-control-border bg-light">
                <option>Skin 1</option>
                <option>Skin 2</option>
                <option>Skin 3</option>
            </select>
        </x-slot>
        <x-adminlte-input name="User" placeholder="Username"/>
        <x-adminlte-input name="Pass" type="password" placeholder="Password"/>
        <x-slot name="footerSlot">
            <x-adminlte-button class="d-flex ml-auto" theme="light" label="submit"
                icon="fas fa-sign-in"/>
        </x-slot>
    </x-adminlte-card>
        </div>
    </div>
    </x-adminlte-alert>
    <div class="card collapsed-card">
        <div class="card-header">
          <h3 class="card-title">Collapsible Card Example</h3>
          <div class="card-tools">
            <!-- Collapse Button -->
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
          </div>
          <!-- /.card-tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          The body of the card
        </div>
        <!-- /.card-body -->
     </div>

     <x-adminlte-callout theme="success" title="Success">
        Success theme callout!
    </x-adminlte-callout>



{{-- Custom --}}
<x-adminlte-modal id="modalCustom" title="Account Policy" size="lg" theme="teal"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    <div style="height:800px;">Read the account policies...</div>
    <x-slot name="footerSlot">
        <x-adminlte-button class="mr-auto" theme="success" label="Accept"/>
        <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>
{{-- Example button to open modal --}}
<x-adminlte-button label="Open Modal" data-toggle="modal" data-target="#modalCustom" class="bg-teal"/>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop
