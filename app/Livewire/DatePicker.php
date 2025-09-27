<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Modelable;

class DatePicker extends Component
{
    #[Modelable]
    public $selected; 
    public $placeholder = 'Chọn ngày';
    public $name = 'date';
    public $format = 'DD/MM/YYYY';

    public function mount($selected = null, $placeholder = null, $name = 'date', $format = 'DD/MM/YYYY')
    {
        $this->selected = $selected;
        $this->name = $name;
        $this->format = $format;
        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
    }

    public function updatedSelected()
    {
        // Bạn có thể xử lý hoặc emit event
        // $this->dispatch('date-changed', $this->selected);
    }

    public function render()
    {
        return view('livewire.date-picker');
    }
}
