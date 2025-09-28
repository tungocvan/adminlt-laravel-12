<?php

namespace App\Livewire\Hamada;

use Livewire\Component;

class HamadaContent extends Component
{
    public $description;
    public function submit()
    {
       
        // Xử lý dữ liệu
        dump($this->description);
    }
    public function render()
    {
        return view('livewire.hamada.hamada-content');
    }
}
  