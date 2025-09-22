<div class="form-group"
     x-data="{
        galleryPreview: [],
        existingGallery: @entangle($attributes->wire('model')).defer,
        previewFiles(event) {
            this.galleryPreview = [];
            Array.from(event.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => this.galleryPreview.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },
        removePreview(index) {
            this.galleryPreview.splice(index, 1);
        }
     }"
>
    @if($label ?? false)
        <label>{{ $label }}</label>
    @endif

    <input type="file" 
           class="form-control"
           wire:model="{{ $uploadModel ?? 'galleryUpload' }}"
           multiple 
           accept="image/*"
           @change="previewFiles($event)">

    <div class="mt-2 d-flex flex-wrap">

        <!-- Preview ảnh mới chọn -->
        <template x-for="(src, index) in galleryPreview" :key="index">
            <div class="position-relative mr-2 mb-2 d-inline-block">
                <img :src="src" class="img-thumbnail" width="100">
                <button type="button"
                        @click="removePreview(index)"
                        class="btn btn-sm btn-danger position-absolute"
                        style="top: -5px; right: -5px; padding:0 5px; line-height:1;"
                        title="Xóa ảnh">×
                </button>
            </div>
        </template>

        <!-- Ảnh cũ từ DB -->
        @if(is_array($current ?? []))
            @foreach($current as $img)
                <div class="position-relative mr-2 mb-2 d-inline-block">
                    <img src="{{ asset('storage/'.$img) }}" width="100" class="img-thumbnail">
                    <button type="button"
                            @click="$wire.{{ $removeMethod ?? 'removeGallery' }}('{{ $img }}')"
                            class="btn btn-sm btn-danger position-absolute"
                            style="top: -5px; right: -5px; padding:0 5px; line-height:1;"
                            title="Xóa ảnh">×
                    </button>
                </div>
            @endforeach
        @endif

    </div>

    @error($uploadModel ?? 'galleryUpload')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>
