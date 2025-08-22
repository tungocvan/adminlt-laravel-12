@extends('layouts.chat')
@section('title', 'CHAT NỘI BỘ')
@section('content')
@if(Auth::check())    
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
        'user' => Auth::user(),
    ]) !!};
</script>
@include('Chat::chat-private')

@endif
@endsection

