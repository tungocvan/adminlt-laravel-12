<?php

namespace Modules\File\Livewire;

use Livewire\Component;

class LichVanNien extends Component
{
    public $selectedDate;
    public $currentMonth;
    public $currentYear;
    
    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }
    
    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }
    
    public function previousMonth()
    {
        // Logic chuyển tháng trước
    }
    
    public function nextMonth()
    {
        // Logic chuyển tháng sau
    }
    
    public function render()
    {
        return view('File::livewire.lichvannien');
    }
}