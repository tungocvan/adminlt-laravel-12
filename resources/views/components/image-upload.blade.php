<div class="form-group" 
     x-data="{
        imagePreview: null,
        init() {
            // nếu có sẵn file từ DB thì không làm gì
        },
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => this.imagePreview = e.target.result;
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = null;
            }
        }
     }">
    @if($label ?? false)
        <label>{{ $label }}</label>
    @endif

    <input type="file" 
           class="form-control mb-1"
           style="height: calc(2.25rem + 6px)"
           wire:model.live="{{ $model ?? 'imageUpload' }}"
           accept="image/*"
           @change="previewImage($event)">

    {{-- Ảnh cũ trong DB --}}
    @php
        $hasFile = !empty($current) && \Illuminate\Support\Facades\Storage::disk('public')->exists($current);
    @endphp

    @if($hasFile)
        <div class="position-relative mr-2 mb-2 d-inline-block mt-1">
            <img src="{{ asset('storage/'.$current) }}" width="120" class="img-thumbnail">
            <button type="button"
                    @click="$wire.{{ $removeMethod ?? 'removeImage' }}()"
                    class="btn btn-sm btn-danger position-absolute"
                    style="top:-5px; right:-5px;">×</button>
        </div>
    @endif


    {{-- Ảnh preview mới chọn --}}
    <template x-if="imagePreview">
        <div class="position-relative d-inline-block">
            <img :src="imagePreview" class="img-thumbnail" width="120">
            <button type="button"
                    @click="imagePreview=null; $wire.{{ $removeMethod ?? 'removeImage' }}()"
                    class="btn btn-sm btn-danger position-absolute"
                    style="top:-5px; right:-5px;">×</button>
        </div>
    </template>

    @error($model ?? 'imageUpload') 
        <small class="text-danger">{{ $message }}</small> 
    @enderror
</div>
