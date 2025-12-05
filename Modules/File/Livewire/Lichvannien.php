<?php

namespace Modules\File\Livewire;

use Livewire\Component;

class Lichvannien extends Component
{
    public $day, $month, $year, $calendar = [];
    public $selectedDay;

    public function mount()
    {
        $date = now();
        $this->day = $date->day;
        $this->month = $date->month;
        $this->year = $date->year;  
        $this->selectedDay = $this->day;


        $this->buildCalendar();
    }

    public function buildCalendar()
    {
        $first = date('w', strtotime("$this->year-$this->month-01"));
        $total = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

        $cells = array_fill(0,$first,null);
        for($d=1;$d<=$total;$d++) $cells[]=$d;

        while(count($cells)%7!=0) $cells[]=null;

        $this->calendar = array_chunk($cells,7);
    }

    public function selectDay($d)
    {
        $this->day = $d;
        $this->selectedDay = $d; // đồng bộ JS
        $this->dispatch('day-changed');
    }


    public function changeMonth($step)
    {
        $this->month += $step;

        if($this->month < 1){ $this->month = 12; $this->year--; }
        if($this->month > 12){ $this->month = 1; $this->year++; }

        $this->day = 1;
        $this->buildCalendar();
    }

    public function render()
    {
        return view('File::livewire.lichvannien');
    }
}
