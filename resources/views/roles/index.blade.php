{{-- @extends('layouts.app') --}}
@extends('adminlte::page')
@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
@stop
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Role Management</h2>
        </div>
        <div class="pull-right">
        @can('role-create')
            <a class="btn btn-success btn-sm mb-2" href="{{ route('roles.create') }}"><i class="fa fa-plus"></i> Create New Role</a>
            <a class="btn btn-success btn-sm mb-2" href="{{ route('roles.permission') }}"><i class="fa fa-plus"></i> Create Permission Module</a>
         @endcan
        </div>
    </div>
</div>

@session('success')
    <div class="alert alert-success" role="alert">
        {{ $value }}
    </div>
@endsession

<table class="table table-bordered">
  <tr>
     <th width="100px">No</th>
     <th>Name</th>
     <th width="280px">Action</th>
  </tr>
    @foreach ($roles as $key => $role)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $role->name }}</td>
        <td>
            <a class="btn btn-info btn-sm" href="{{ route('roles.show',$role->id) }}"><i class="fa-solid fa-list"></i> Show</a>
            @can('role-edit')
                <a class="btn btn-primary btn-sm" href="{{ route('roles.edit',$role->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
            @endcan

            @can('role-delete')
            <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display:inline">
                @csrf
                @method('DELETE')
                @if ( $role->name !=='Admin')
                   <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                @endif                
            </form>
            @endcan
        </td>
    </tr>
    @endforeach
</table>

{!! $roles->links('pagination::bootstrap-5') !!}
{{--
<p class="text-center text-primary"><small>Tutorial by Từ Ngọc Vân</small></p> --}}
@endsection
