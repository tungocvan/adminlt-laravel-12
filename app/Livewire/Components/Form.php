<?php

namespace App\Livewire\Components;
use Livewire\Component;

class Form extends Component
{
    public $category='Sports';
    public $showModal = false; 
    public $content = '';
    public $email = '';
    public $password = '';
    public function handleSubmit($data){
       // dd($data);
    }
    // public function save(){
    //     dd($this->category);
    // }
    public function render()
    {
        return view('livewire.components.form');
    }
    public function save()
    {
    
        $this->showModal = false;
       // dd($this->email,$this->password);
    }
}
