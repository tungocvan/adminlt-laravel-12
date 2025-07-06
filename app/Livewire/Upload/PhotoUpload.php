<?php

namespace App\Livewire\Upload;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Image;

class PhotoUpload extends Component
{
    use WithFileUploads;
    public $photo;
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
            "title" => "Test",
            "filepath" => $filepath
        ]);

        info($image);
    }
}
