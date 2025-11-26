<?php

namespace App\Livewire;

use Livewire\Component;

class TsSelect extends Component
{
    public $name;          // wire:model target
    public $value = '';    // selected value
    public $options = [];  // dropdown options
    public $placeholder = '';

    public function mount($name, $options = [], $placeholder = '')
    {
        $this->name = $name;
        $this->options = $options;
        $this->placeholder = $placeholder;
    }

    public function updatedValue($val)
    {
        // Đồng bộ ra ngoài
        $this->dispatch('ts-select-updated', name: $this->name, value: $val);
    }

    public function render()
    {
        return view('livewire.ts-select');
    }
}
