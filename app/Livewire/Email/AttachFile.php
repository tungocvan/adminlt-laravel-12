<?php

namespace App\Livewire\Email;

use Livewire\WithFileUploads;
use Livewire\Component;


class AttachFile extends Component
{
    
    use WithFileUploads;
    
    public $photos; // Mảng file upload
    public $uploadedFiles = []; // Lưu tên file sau khi upload
    public $pathDirectory= 'uploads';
    public $messages = []; // Thông báo
    
    public function deleteIndex($index){
        array_splice($this->uploadedFiles,$index,1);    
    }
    public function saveFiles()
    {
        // Kiểm tra xem có file nào không
        if (empty($this->photos)) {
            $this->messages[] = "Vui lòng chọn file!";
            return;
        }

        // Validate tất cả các file
        $this->validate([
            'photos.*' => 'file|max:10240', // Mỗi file tối đa 10MB
        ]);

        foreach ($this->photos as $index => $photo) {
            if (!isset($this->uploadedFiles[$index])) {
                $this->uploadImage($index);
            }
        }    
    }
    public function uploadImage($index)
    {
        if (!isset($this->photos[$index]) || isset($this->uploadedFiles[$index])) return;

        $photo = $this->photos[$index];
        $filename = $photo->getClientOriginalName();

        // Lưu file vào storage/public/uploads
        $path = $photo->storeAs($this->pathDirectory, $filename, 'public');

        // Lưu trạng thái đã upload
        $this->uploadedFiles[$index] = $path;
        $this->messages[] = "Ảnh '{$filename}' đã upload thành công!";
    }
    public function render()
    {
        return view('livewire.email.attach-file');
    }
}
 