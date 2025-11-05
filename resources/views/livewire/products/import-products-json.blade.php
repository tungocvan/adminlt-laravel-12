<div class="d-flex justify-content-between mb-3">
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form wire:submit="importJson">
        <div class="d-flex justify-content-between mb-3">
        <input type="file" wire:model.live="file" class="form-control mb-2" accept=".json" style="height: calc(2.25rem + 6px)" required>
        @error('file') <span class="text-danger">{{ $message }}</span> @enderror

        <button class="btn btn-success mx-2" style="height: 42px; width:150px">Import JSON</button>
        </div>
    </form>

    {{-- Log danh mục trùng --}}
    @if (!empty($skippedCategories))
        <div class="mt-3 alert alert-warning">
            <strong>Danh mục bị bỏ qua (do trùng):</strong>
            <ul>
                @foreach ($skippedCategories as $cat)
                    <li>{{ $cat }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Log sản phẩm trùng --}}
    @if (!empty($skippedProducts))
        <div class="mt-3 alert alert-warning">
            <strong>Sản phẩm bị bỏ qua (do trùng):</strong>
            <ul>
                @foreach ($skippedProducts as $prod)
                    <li>{{ $prod }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
