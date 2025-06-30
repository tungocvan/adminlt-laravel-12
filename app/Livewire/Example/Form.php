<?php

namespace App\Livewire\Example;

use Livewire\Component;

class Form extends Component
{
    public $country='Alaska';
    public $showDropdown = false;
    public function handleSubmit($message){
        dd($message);
    }
    public function render()
    {
        return view('livewire.example.form');
    }
    public function archive()
    {
        // ...
 
        $this->showDropdown = false;
    }
 
    public function delete()
    {
        // ...
 
        $this->showDropdown = false;
    }
}

