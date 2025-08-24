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
                    @include('layouts.flash-messages')
                    
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
    <script>    
        document.addEventListener("DOMContentLoaded", function () {
         console.log('✅ DOM loaded');
         window.socket.on("connect", () => {
                 console.log("✅ Connected to NodeJS Socket.IO:", socket.id);
         });

        // Nhận sự kiện post-create
        window.socket.on("post-create", (data) => {
            console.log("🆕 New Post Event:", data);

            let notificationBox = document.getElementById("notification");
            let alert = document.createElement("div");
            alert.classList.add("alert", "alert-info", "alert-dismissible", "fade", "show");
            alert.innerHTML = `<i class="fa fa-bullhorn"></i> [${data.created_at}] New Post: <b>${data.title}</b>`;
            notificationBox.prepend(alert);
                 // === Thêm dòng mới vào bảng ===
            let tbody = document.querySelector("table.data-table tbody");
            if (!tbody) {
                console.warn("⚠️ Không tìm thấy tbody trong bảng!");
                return;
            }

            let newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td>${data.id}</td>
                <td>${data.title}</td>
                <td>${data.body}</td>
                <td><span class="badge bg-primary"><i class="fa fa-circle-dot"></i> Pending</span></td>
                <td>
                    <a href="/posts/${data.id}/approve" class="btn btn-success btn-sm">
                        <i class="fa fa-save"></i> Approved
                    </a>
                </td>
            `;

            // Chèn dòng mới lên đầu tbody
            tbody.prepend(newRow);

        });   
        
        
        
     });
     
        
     </script>
</div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop
{{-- @section('js')

@stop --}}


