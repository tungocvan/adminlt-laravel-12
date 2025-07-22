@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Manager Post</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><i class="fa fa-list"></i> {{ __('Posts List') }}</div>

                <div class="card-body">
                    @session('success')
                        <div class="alert alert-success" role="alert"> 
                            {{ $value }}
                        </div>
                    @endsession

                    
                    <div id="notification">
                        
                    </div>
                    @foreach(auth()->user()->unreadNotifications as $notification)
                        <div class="alert alert-success alert-dismissible fade show">
                            <span><i class="fa fa-circle-check"></i>  [{{ $notification->created_at }}] {{ $notification->data['message'] }}</span>
                            <a href="{{ route('notifications.mark.as.read', $notification->id) }}" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><strong><i class="fa fa-book-open"></i> Mark as Read</strong></span>
                            </a>
                        </div>
                    @endforeach
                    
                    @if(auth()->user()->is_admin)
                    <p><strong>Create New Post</strong></p>
                    <form method="post" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" name="title" class="form-control" />
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Body:</label>
                            <textarea class="form-control" name="body"></textarea>
                            @error('body')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i> Submit</button>
                        </div>
                    </form>
                    @endif

                    <p class="mt-4"><strong>Post List:</strong></p>
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th width="70px">ID</th>
                                <th>Title</th>
                                <th>Body</th>
                                <th>Status</th>
                                @if(auth()->user()->is_admin)
                                <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td>{{ $post->id }}</td>
                                    <td>{{ $post->title }}</td>
                                    <td>{{ $post->body }}</td>
                                    <td>
                                        @if($post->is_approved)
                                            <span class="badge bg-success"><i class="fa fa-check"></i> Approved</span>
                                        @else
                                            <span class="badge bg-primary"><i class="fa fa-circle-dot"></i> Pending</span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->is_admin)
                                    <td>
                                        @if(!$post->is_approved)
                                            <a href="{{ route('posts.approve', $post->id) }}" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Approved</a>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">There are no posts.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {!! $posts->withQueryString()->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    {{-- @if(auth()->user()->is_admin)
    <script type="module">        
        window.Echo.channel('posts')
        
            .listen('.create', (data) => {
                console.log('Order status updated: ', data);
                var d1 = document.getElementById('notification');
                d1.insertAdjacentHTML('beforeend', '<div class="alert alert-success alert-dismissible fade show"><span><i class="fa fa-circle-check"></i>  '+data.message+'</span></div>');
            });
    </script>
    @endif --}}

@stop

