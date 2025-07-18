<div>
    <div class="container">
        <div class="card mt-5">
            <h3 class="card-header p-3">Laravel 12 Drag and Drop File Upload with Dropzone JS - ItSolutionStuff.com</h3>
            <div class="card-body">
                <form action="{{ route('dropzone.store') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
                    @csrf
                    <div>
                        <h4>Upload Multiple Image By Click On Box</h4>
                    </div>
                </form>
                <button id="uploadFile" class="btn btn-success mt-1">Upload Images</button>
            </div>
        </div>
    </div>
    
</div>