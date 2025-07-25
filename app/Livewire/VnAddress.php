<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Provinces;
use App\Models\Wards;

class VnAddress extends Component
{
    public $provinces = [];
    public $wards = [];
    public $selectedProvince = '';
    public $selectedWard = '';

    protected $listeners = ['setSelectedWard'];

    public function mount()
    {
        //$this->provinces = Provinces::get(['code', 'full_name']);
        $this->provinces = Provinces::all();
    }

    public function updatedSelectedProvince($value)
    {
        $this->wards = Wards::where('province_code', $value)->get(['province_code', 'full_name', 'id']);
        $this->selectedWard = '';
        
        // Bắt buộc để JS re-init Select2
        $this->dispatch('refreshSelect2');
    }
    public function updatedSelectedWard($value)
    {
        
        $this->selectedWard = $value;
        
        // Bắt buộc để JS re-init Select2
        $this->dispatch('refreshSelect2');
    }
    

    public function setSelectedWard($value)
    {
        $this->selectedWard = $value;
    }

    public function render()
    {
        return view('livewire.vn-address');
    }
}
