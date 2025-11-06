<?php

namespace Modules\Components\Http\Components;

use Illuminate\View\Component;

class TnvInputDate extends Component
{
    public string $name;
    public string $label;
    public ?string $placeholder;
    public array $config;
    public string $placement;

    public function __construct(
        string $name,
        string $label = '',
        string $placeholder = '',
        array $config = [],
        string $placement = 'bottom'
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder ?: 'Chọn ngày...';
        $this->placement = $placement;

        // Merge default config
        $this->config = array_merge([
            'format' => 'DD/MM/YYYY',
            'display' => [
                'components' => [
                    'useTwentyfourHour' => true,
                ],
                // Dự phòng nếu JS không override
                'placement' => $placement,
            ],
        ], $config);
    }

    public function render()
    {
        return view('Components::components.tnv-input-date');
    }
}
