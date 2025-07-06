{{-- @extends('layouts.app') --}}
@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Create New User</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('users.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

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

<form wire:submit.prevent="save">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                <input type="text" class="form-control" wire:model="name">
            </div>
            <div class="form-group">
                <strong>Email:</strong>
                <input type="email" class="form-control" wire:model="email">
            </div>
            <div class="form-group">
                <strong>User Name:</strong>
                <input type="text" class="form-control" wire:model="username">
            </div>
            <div class="form-group">
                <strong>Password:</strong>
                <input type="password" class="form-control" wire:model="password">
            </div>
            <div class="form-group">
                <strong>Role:</strong>
                <select class="form-control" multiple wire:model="role">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">
                    <i class="fa-solid fa-floppy-disk"></i> Submit
                </button>
            </div>
        </div>
    </div>
</form>


{{-- <p class="text-center text-primary"><small>Tutorial by Từ Ngọc Vân</small></p> --}}
@endsection
