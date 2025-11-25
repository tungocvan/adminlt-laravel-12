<?php

namespace App\Livewire;

use Livewire\Component;

class SpinWheel extends Component
{
    public array $rings = [
        ['image'=>'/assets/wheel_1.svg','spins'=>5,'duration'=>4.8,'easing'=>'cubic-bezier(.3,.92,.22,.95)'],
        ['image'=>'/assets/wheel_2.svg','spins'=>4,'duration'=>4.2,'easing'=>'cubic-bezier(.25,.9,.25,.98)'],
        ['image'=>'/assets/wheel_3.svg','spins'=>5,'duration'=>3.6,'easing'=>'cubic-bezier(.2,.9,.28,1)'],
        ['image'=>'/assets/wheel_4.svg','spins'=>6,'duration'=>3.0,'easing'=>'cubic-bezier(.16,.9,.3,1.03)'],
        ['image'=>'/assets/wheel_5.svg','spins'=>6,'duration'=>2.6,'easing'=>'cubic-bezier(.14,.9,.34,1.05)'],
    ];

    public array $labels = [];

    public function mount()
    {
        for ($i = 1; $i <= 60; $i++) {
            $this->labels[] = [
                'name' => "Phần $i",
                'icon' => '/assets/gift_default.svg',
            ];
        }
    }

    public function calculateResult($angle)
    {
        $arrowOffset = 180; // arrow ở dưới
        $labelsCount = count($this->labels);
        $angleAtArrow = ($angle + $arrowOffset) % 360;
        $index = intval(floor($angleAtArrow / (360 / $labelsCount))) % $labelsCount;
        return $index; // lưu vào DB nếu muốn
    }

    public function render()
    {
        return view('livewire.spin-wheel');
    }
}
