<div>
    <div class="container">
  
        <div class="card mt-5">
            <h3 class="card-header p-3"><i class="fa fa-star"></i> Laravel 12 Image Upload Example - ItSolutionStuff.com</h3>
            <div class="card-body">
      
                @session('success')
                    <div class="alert alert-success" role="alert"> 
                        {{ $value }}
                    </div>
                    <img src="images/{{ Session::get('image') }}" width="40%">
                @endsession
                
                <form action="{{ route('upload.image.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
            
                    <div class="mb-3">
                        <label class="form-label" for="inputImage">Image:</label>
                        <input 
                            type="file" 
                            name="image" 
                            id="inputImage"
                            class="form-control @error('image') is-invalid @enderror">
            
                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
             
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Upload</button>
                    </div>
                 
                </form>
            </div>
        </div>
    </div>
</div>
