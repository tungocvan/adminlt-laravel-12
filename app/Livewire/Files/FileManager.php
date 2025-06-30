<?php

namespace App\Livewire\Files;
use Livewire\Component;

class FileManager extends Component
{
    public $content="";
    public $name;
    public $label='';
    public $height='200';
    public $labelClass= 'text-dark'; 
    public $config = [
        "height" => "200",
        "toolbar" => [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video', 'lfm']],
            ['view', ['fullscreen', 'codeview', 'help']],
        ],
    ];

    public function mount(){ 
        $this->config['height'] = $this->height;
        if(!$this->name){
            $this->name = 'editor_' . rand(0, 9999);
        }        
    }


    public function hanlderSubmit($data) {
         return $data;       
    }
  
    public function render()
    {
        return view('livewire.files.file-manager');
    }
}
