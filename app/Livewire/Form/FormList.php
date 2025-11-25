<?php

namespace App\Livewire\Form;

use Livewire\Component;
use Livewire\WithFileUploads;

class FormList extends Component
{
    use WithFileUploads; 
    public $code ;   // lấy từ select-options-table (multiple)
    public $ward; 
    public $products = [];   // lấy từ select-options-table (multiple)
    public $start_date;
    public $end_date;
    public $description = '';
    public $image;
    public $imageUpload;   // file upload chính

    public function submit() 
    {
        if ($this->imageUpload) {
            $image = $this->imageUpload->store('products', 'public');
        }
        // Xử lý dữ liệu
        dd([
            'products'   => $this->products,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'imageUpload'   =>  $image,
            'description'   => $this->description,
        ]);
    }



    public function updatedCode(){
        //dd($this->products);
        $this->ward = (string)$this->code;
    }
    public function updatedProducts(){
        //dd($this->products);
    }

    public function updatedImageUpload()
    {
        // dd($this->imageUpload);
        // $this->image = null;

    }

    public function removeImage()
    {
        // Xóa file cũ trong storage nếu cần
        // if($this->image && Storage::exists($this->image)){
        //     Storage::delete($this->image);
        // }
        // $this->image = null;
    }
    public function render()
    {
        return view('livewire.form.form-list');
    }
}
