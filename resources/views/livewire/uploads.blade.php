<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class UploadImage extends Component
{
    use WithFileUploads;

    public $photo;
    public $successMessage;

    public function updatedPhoto()
    {
        // Khi người dùng chọn ảnh, cập nhật biến photo
        $this->validate([
            'photo' => 'image|max:1024', // 1MB Max
        ]);
    }

    public function save()
    {
        if ($this->photo) {
            $path = $this->photo->store('uploads', 'public'); // Lưu vào storage/app/public/uploads
            $this->successMessage = "Tải lên thành công! Đường dẫn: $path";
        }
    }

    public function cancelUpload()
    {
        $this->reset('photo');
    }

    public function deleteImage()
    {
        $this->photo = null;
        
    }

    public function render()
    {
        return view('livewire.upload-image');
    }
}


?>
<div class="row" x-data="{ previewUrl: null }">
  <div class="col-md-12">
      <div class="card card-default">
          <div class="card-header">
              <h3 class="card-title">Upload File</h3>
          </div>
          <div class="card-body">
              <!-- Form Upload -->
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
                      </label>

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
          </div>

          @if (isset($successMessage))
              <div class="card-footer success">
                  {{ $successMessage }}
              </div>
          @endif
      </div>
  </div>
</div>


