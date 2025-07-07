<?php

namespace App\Livewire\Upload;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Image;

class PhotoUpload extends Component
{
    use WithFileUploads;
    public $photo;
    public $successMessage;

    public function render()
    {
        return view('livewire.upload.photo-upload');
    }
    public function submit(){
        //dd($this->photo->temporaryUrl());
        $this->validate([
            "photo" => "required|image"
        ]);

        $filepath = $this->photo->store("photos");

        $image = Image::create([        
            "name" => $filepath
        ]);
        return back()->with('success', 'Uploaded successfully!');
    }
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
} 
