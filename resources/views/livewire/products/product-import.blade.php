<div class="d-flex justify-content-between mb-3">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form wire:submit="import" enctype="multipart/form-data">
        <div class="d-flex justify-content-between mb-3">
            <div class="form-group mx-2">
                <input type="file" wire:model.live="file" class="form-control" style="height: calc(2.25rem + 6px)">

                @error('file') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div wire:loading wire:target="file" class="text-info my-2">
                Đang upload file...
            </div>
            <button type="submit" class="btn btn-primary mx-2"
                    style="height: 42px"
                    wire:loading.attr="disabled"
                    wire:target="import">
                Import Excel
            </button>
        </div>
    </form>
</div>
