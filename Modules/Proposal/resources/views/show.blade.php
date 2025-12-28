@extends('adminlte::page')

@section('title', 'Chi tiết đề xuất')

@section('content')

    @livewire('proposal.proposal-show',['proposal' => $proposal])
@endsection
