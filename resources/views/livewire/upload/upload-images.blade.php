<div class="container">
  
    <div class="card mt-5">
        <h3 class="card-header p-3"><i class="fa fa-star"></i> Laravel 12 Multiple Image Upload Example - ItSolutionStuff.com</h3>
        <div class="card-body">
  
            @session('success')
                <div class="alert alert-success" role="alert"> 
                    {{ $value }}
                </div>
                @foreach(Session::get('images') as $image)
                    <img src="images/{{ $image['name'] }}" width="300px">
                @endforeach
            @endsession
            
            <form action="{{ route('upload.images.store') }}" method="POST" enctype="multipart/form-data" class="mt-2">
                @csrf
       
                <div class="mb-3">
                    <label class="form-label" for="inputImage">Select Images:</label>
                    <input 
                        type="file" 
                        name="images[]" 
                        id="inputImage"
                        multiple 
                        class="form-control @error('images') is-invalid @enderror">
      
                    @error('images')
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
