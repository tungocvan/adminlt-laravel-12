<?php

namespace Modules\Components\Http\Components;

use Illuminate\View\Component;

class TnvInputImage extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('Components::components.tnv-input-image');
    }
}