{{-- @extends('layouts.app') --}}
@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Create permission module</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('roles.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>


<form method="POST" action="{{ route('roles.store-permission') }}">
    @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                @if(count($modules) > 0)
                    <label for="module">Select a module:</label>
                    <select name="module" id="module">
                        @foreach($modules as $directory)
                            <option value="{{ $directory }}">{{ $directory }}</option>
                        @endforeach
                    </select>
                @else
                    <p>Không có Modules để phân quyền.</p>
                @endif


            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mb-3"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
        </div>
    </div>
</form>
{{--
<p class="text-center text-primary"><small>Tutorial by Từ Ngọc Vân</small></p> --}}
@endsection
