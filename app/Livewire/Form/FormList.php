<?php

namespace App\Livewire\Form;

use Livewire\Component;

class FormList extends Component
{
    public $products = [];   // lấy từ select-options-table (multiple)
    public $start_date;
    public $end_date;
    public $description = '';

    public function submit()
    {
        // Xử lý dữ liệu
        dd([
            'products'   => $this->products,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'description'   => $this->description,
        ]);
    }

    public function updatedProducts(){
        //dd($this->products);
    }

   

    public function render()
    {
        return view('livewire.form.form-list');
    }
}
