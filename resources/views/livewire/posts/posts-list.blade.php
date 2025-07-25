<div>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (isset($updateMode) && $updateMode)
        @include('livewire.posts.update')
    @else
        @include('livewire.posts.create')
    @endif



    <table class="table table-bordered mt-5">

        <thead>

            <tr>

                <th>No.</th>

                <th>Title</th>

                <th>Body</th> 

                <th width="150px">Action</th>

            </tr>

        </thead>

        <tbody>
            @if (isset($posts))
                @foreach ($posts as $post)
                    <tr>

                        <td>{{ $post->id }}</td>

                        <td>{{ $post->title }}</td>

                        <td>{{ $post->body }}</td>

                        <td>

                            <button wire:click="edit({{ $post->id }})" class="btn btn-primary btn-sm">Edit</button>

                            <button wire:click="delete({{ $post->id }})" class="btn btn-danger btn-sm">Delete</button>

                        </td>

                    </tr>
                @endforeach
            @endif
        </tbody>

    </table>

</div>
