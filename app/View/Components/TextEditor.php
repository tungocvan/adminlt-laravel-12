<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TextEditor extends Component
{
    public $name;
    public $label;
    public $labelClass; 
    public $config;

    /**
     * Khởi tạo component với các tham số có thể truyền vào.
     */
    public function __construct($name, $label = '', $labelClass = 'text-dark', $config = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->labelClass = $labelClass;

        // Cấu hình mặc định của Summernote nếu không truyền từ ngoài vào
        $this->config = $config ?: [
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
    }

    public function render()
    {
        return view('components.text-editor');
    }
}
