<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Modelable;

class TextEditor extends Component
{
    #[Modelable] 
    public $content = ''; // dữ liệu nhập trong summernote

    public $name = 'editor';
    public $label;
    public $height = 200;
    public $config = [];
    public $labelClass = 'form-label';
    public $placeholder = 'Nhập nội dung...';

    public function mount(
        $name = 'editor',
        $label = null,
        $labelClass = 'form-label',
        $placeholder = 'Nhập nội dung...',
        $config = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->labelClass = $labelClass;
        $this->placeholder = $placeholder;
        $this->config = $config ?? [
            'height'   => $this->height,
            'toolbar'  => [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video', 'lfm']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.text-editor');
    }
}
