<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class ImageUploader extends Component
{
    use WithFileUploads;
    public $pathDirectory= 'uploads';
    public $photosArray = []; // Danh sách file
    #[Validate(['photos.*' => 'image|max:1024'])]
    public $photos = []; // Danh sách file
    public $uploadedPhotos = []; // Danh sách ảnh đã upload
    public $messages = []; // Thông báo
    public $countFiles = 0; // Thông báo

    public function updatedPhotos(){
        $this->photosArray = [...$this->photos,...$this->photosArray];
        $this->photos = [...$this->photosArray];
        $this->countFiles = count($this->photos);    
      
    }

    public function uploadImage($index)
    {
        if (!isset($this->photos[$index]) || isset($this->uploadedPhotos[$index])) return;

        $photo = $this->photos[$index];
        $filename = $photo->getClientOriginalName();

        // Lưu file vào storage/public/uploads
        $path = $photo->storeAs($this->pathDirectory, $filename, 'public');

        // Lưu trạng thái đã upload
        $this->uploadedPhotos[$index] = $path;
        $this->messages[] = "Ảnh '{$filename}' đã upload thành công!";
    }

    public function uploadAll()
    {
        //dd($this->photos);
        foreach ($this->photos as $index => $photo) {
            if (!isset($this->uploadedPhotos[$index])) {
                $this->uploadImage($index);
            }
        }
    }

    public function cancelImage($index)
    {
        unset($this->photos[$index]);
        unset($this->uploadedPhotos[$index]);
    }

    public function cancelAll()
    {
        $this->photos = [];
        $this->photosArray = [];
        $this->uploadedPhotos = [];
        $this->messages = [];
        $this->countFiles = 0;
    }

    public function render()
    {
        return view('livewire.image-uploader');
    }
}
