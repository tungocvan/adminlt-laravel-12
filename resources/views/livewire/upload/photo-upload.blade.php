<div>
    @session('success')
    <div class="card card-warning">
        <div class="card-header">
          <h3 class="card-title">{{ $value }}</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
            </button>
          </div>

        </div>

      </div>
    @endsession

    <form wire:submit.prevent="submit" enctype="multipart/form-data">
        @if ($photo)
           
            <div>
                <label>Photo Preview:</label><br>
                {{-- <img src="{{ $photo->temporaryUrl() }}" width="400px"><br/> --}}
                <img src="{{ asset('storage/livewire-tmp/' . $photo->getFilename()) }}" width="400">

            </div>
        @endif

        <label>Image:</label>
        <input type="file" name="photo" wire:model="photo" class="form-control">
        @error('photo') <p class="text-danger">{{ $message }}</p> @enderror

        <button type="submit" class="btn btn-success mt-2">Submit</button>
    </form>

    <hr />
    <form wire:submit.prevent="save">
      <div class="btn-group w-100">
        <input type="file" wire:model="photo" accept="image/*"
            class="hidden"
            id="upload"
            x-ref="fileInput"
            @change="
                let file = $refs.fileInput.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = (e) => previewUrl = e.target.result;
                    reader.readAsDataURL(file);
                }
            "
        >
        <label for="upload" class="btn btn-light">
            <i class="fas fa-upload"></i> Chọn ảnh
        </label>`
        

        <div class="form-group mx-2">
            <x-adminlte-button class="btn-flat" type="submit" label="Start upload" theme="primary" icon="fas fa-upload"/>
        </div>
        
        <div class="form-group">
            <x-adminlte-button class="btn-flat" type="button" label="Cancel upload" theme="danger" icon="fas fa-times-circle" wire:click="cancelUpload"/>
        </div>
    </div>
    </form>
    <!-- Hiển thị ảnh trước khi upload -->
    <hr>
    <div class="row mt-2 w-100 px-2" x-show="previewUrl">
        <div class="col-2">
            <img :src="previewUrl" alt="Preview" class="img-thumbnail">
        </div>
        <div class="col-2 d-flex align-items-center">
            <strong class="text-danger" wire:click="deleteImage" style="cursor: pointer">
                <i class="fas fa-trash"></i> Xóa ảnh
            </strong>
        </div>
    </div>
    @if (isset($successMessage))
    <div class="card-footer success">
        {{ $successMessage }}
    </div>
    @endif
</div>
