<!DOCTYPE html>
<html>
<head>
    <title>Laravel 12 Compress Image Example - ItSolutionStuff.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
        
<body>
<div class="container">
  
    <div class="card mt-5">
        <h3 class="card-header p-3"><i class="fa fa-star"></i> Laravel 12 Compress Image Example - ItSolutionStuff.com</h3>
        <div class="card-body">

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
  
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <strong>{{ $message }}</strong>
                </div>
                <img src="/images/{{ Session::get('image') }}" width="512">
            @endif
            
            <form action="{{ route('upload.image-resize.store') }}" method="POST" enctype="multipart/form-data">
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
</body>
      
</html>