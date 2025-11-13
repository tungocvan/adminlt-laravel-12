@extends('adminlte::page')
@section('title', 'Users')
@section('plugins.TempusDominusBs4', true)
@section('content_header')
    {{-- <h1>Manager User</h1> --}}
@stop

@section('content')
      @livewire('users.user-list')
      {{-- @livewire('user.user-manager') --}}
@stop

@section('css')
@stack('styles')
    {{-- Sử dụng ở component @push(css) --}}
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
 
@stop


@section('js')
@stack('scripts')

@stop


