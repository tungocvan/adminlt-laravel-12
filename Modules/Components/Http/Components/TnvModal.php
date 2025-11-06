<?php

namespace Modules\Components\Http\Components;

use Illuminate\View\Component;

class TnvModal extends Component
{
    public string $id;
    public string $title;
    public string $size;
    public string $theme;
    public string $icon;
    public bool $vCentered;
    public bool $scrollable;

    public function __construct(
        string $id,
        string $title = '',
        string $size = 'lg',
        string $theme = 'teal',
        string $icon = 'fas fa-user',
        bool $vCentered = true,
        bool $scrollable = true,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->size = $size;
        $this->theme = $theme;
        $this->icon = $icon;
        $this->vCentered = $vCentered;
        $this->scrollable = $scrollable;
    }


    public function render()
    {
        return view('Components::components.tnv-modal');
    }
}