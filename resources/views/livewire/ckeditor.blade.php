<div>
    @section('content')
    <div class="container">
        <div class="card mt-5">
            <h3 class="card-header p-3">Laravel 12 Ckeditor Image Upload Example - ItSolutionStuff.com</h3>
            <div class="card-body">
                <form action="{{ route('ckeditor.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <strong>Title:</strong>
                        <input type="text" name="title" class="form-control" placeholder="Title" value="{{ old('title') }}">
                    </div>
            
                    <div class="form-group">
                        <strong>Slug:</strong>
                        <input type="text" name="slug" class="form-control" placeholder="Slug" value="{{ old('slug') }}">
                    </div>
            
                    <div class="form-group">
                        <strong>Body:</strong>
                        <textarea name="editor" id="editor"></textarea>
                    </div>
            
                    <div class="form-group mt-2">
                        <button class="btn btn-success" type="submit">Submit</button>
                    </div>
            
                </form>
            </div>
        </div>
    </div>
    @stop

    @section('css')
        {{-- Add here extra stylesheets --}}
        {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
        .ck-editor__editable_inline {
            min-height: 300px;
        }
        </style>

    @stop

    @section('js')
        <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
        <script>
            const ckeditorUploadUrl = "{{ route('ckeditor.upload') }}?_token={{ csrf_token() }}";
            ClassicEditor
            .create(document.querySelector('#editor'), {
                ckfinder: {
                    uploadUrl: ckeditorUploadUrl
                }
            })
            .catch(error => {});
            
        </script>

    @stop
</div>
