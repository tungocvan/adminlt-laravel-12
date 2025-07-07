<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    public $photo;
    public $photos = [];  // nhiều ảnh
    public $successMessage;
    public $multi=false;
    public $uploaded =false;

    public function updatedPhoto()
    {
        // Khi người dùng chọn ảnh, cập nhật biến photo
        $this->validate([
            'photo' => 'image|max:1024', // 1MB Max
        ]);
    }

    function save()
    {
        $paths = [];

        if ($this->photos) {
            foreach ($this->photos as $image) {
                $paths[] = $image->store('uploads', 'public');
            }
        } elseif ($this->photo) {
            $paths[] = $this->photo->store('uploads', 'public');
        }

        $this->successMessage = 'Tải lên thành công: ' . implode(', ', $paths);
        $this->reset(['photo', 'photos']);
        $this->uploaded = true;
        // Gửi ảnh đã upload về component cha (để form lưu)
        $this->dispatch('image-uploaded', $paths);

    }

    function huyUpload()
    {
        
        $this->successMessage = null;
        $this->reset(['photo', 'photos', 'uploaded']);
        
    }

    public function deleteImage()
    {
        $this->photo = null;
        $this->successMessage = null;
    }
    function removeImage($index)
    {
        
        if($this->multi == false){
            $this->deleteImage();
        }else{
            if (isset($this->photos[$index])) {
                unset($this->photos[$index]);
                $this->photos = array_values($this->photos);
            }
            
        } 
       
        
    }
}; ?>

<div x-data="{ previewUrls: [], uploaded: false }" x-init="$watch('$wire.uploaded', value => uploaded = value)" class="container mt-4">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">📤 Upload {{ $multi ? 'Nhiều ảnh' : '1 ảnh' }}</h3>
        </div>

        <div class="card-body">
            <!-- Upload Form -->
            <form wire:submit.prevent="save" class="form-inline">
                <div class="form-group mr-3">
                    <input
                        type="file"
                        wire:model{{ $multi ? 's' : '' }}="photo"
                        {{ $multi ? 'multiple' : '' }}
                        accept="image/*"
                        class="d-none"
                        id="upload"
                        x-ref="fileInput"
                        @change="
                            previewUrls = [];
                            for (let file of $refs.fileInput.files) {
                                let reader = new FileReader();
                                reader.onload = e => previewUrls.push(e.target.result);
                                reader.readAsDataURL(file);
                            }
                        "
                    >

                    <label for="upload" class="btn btn-outline-secondary">
                        <i class="fas fa-folder-open"></i> {{ $multi ? 'Chọn nhiều ảnh' : 'Chọn ảnh' }}
                    </label>
                </div>

                <div class="form-group mr-2">
                    <button
                        type="submit"
                        class="btn btn-primary ml-2"
                        :disabled="previewUrls.length === 0 || uploaded"
                        :class="(previewUrls.length === 0 || uploaded) ? 'opacity-50 cursor-not-allowed' : ''"
                    >
                        <i class="fas fa-upload"></i> Tải lên
                    </button>

                </div>

                <div class="form-group">
                    <button
                        type="button"
                        class="btn btn-danger ml-2"
                        x-show="previewUrls.length > 0"
                        @click.prevent="
                            $wire.huyUpload();
                            previewUrls = [];
                            $refs.fileInput.value = null;
                            uploaded = false;
                        "
                    >
                        <i class="fas fa-times-circle"></i> Hủy chọn
                    </button>


                </div>
            </form>

            <!-- Preview -->
            <div class="row mt-4" x-show="previewUrls.length" x-cloak>
                <template x-for="(url, index) in previewUrls" :key="index">
                    <div class="col-md-3 mb-2">
                        <img :src="url" class="img-thumbnail mb-1">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger btn-block mt-1"
                            x-show="!uploaded"
                            @click.prevent="
                                previewUrls.splice(index, 1);
                                $wire.removeImage(index);
                            "
                        >
                            <i class="fas fa-trash"></i> Xóa
                        </button>

                    </div>
                </template>
            </div>

            <!-- Success Message -->
            @if ($successMessage)
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i> {{ $successMessage }}
                </div>
            @endif
        </div>
    </div>
</div>

