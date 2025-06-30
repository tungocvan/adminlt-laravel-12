<?php

namespace App\View\Components\Tnv;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use JeroenNoten\LaravelAdminLte\Helpers\UtilsHelper;

class Button extends Component
{
    /**
     * Create a new component instance.
     */
    public $label;
    public $href;
    public $type;
    public $theme;
    public $icon;
    public $quanlity;
    public function __construct(
        $label = null, $href = null, $theme = null, $icon = null, $quanlity = null, $type = 'submit'
    ) {
        $this->label = UtilsHelper::applyHtmlEntityDecoder($label);
        $this->href = $href;
        $this->type = $type;
        $this->theme = $theme;
        $this->icon = $icon;
        $this->quanlity = $quanlity;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tnv.button');
    }
}
