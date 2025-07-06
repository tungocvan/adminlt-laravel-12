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
</div>
