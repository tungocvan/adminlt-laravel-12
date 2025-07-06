<div>
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
