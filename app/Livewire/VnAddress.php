<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Provinces;
use App\Models\Wards;

class VnAddress extends Component
{
    public $provinces = [];
    public $wards = [];
    public $selectedProvince;
    public $selectedWard;

    public function mount()
    {
        $this->provinces = Provinces::get(['code', 'full_name','id']);
    }

    public function updatedSelectedProvince($value)
    {
        
        
        $this->wards = Wards::where('province_code', $value)->get(['province_code', 'full_name']);
        $this->selectedWard = null; // Reset ward nếu tỉnh đổi
     
    }

    public function render()
    {
        return view('livewire.vn-address');
    }
}
