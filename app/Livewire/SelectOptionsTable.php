<?php

namespace App\Livewire;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class SelectOptionsTable extends Component
{
    public $options = [];
    #[Modelable] // Cho phép wire:model từ cha
    public $selected = []; // array cho multiple
    public $placeholder = 'Chọn mục';

    // tham số truyền từ blade
    public $model;
    public $title = 'title';
    public $id = 'id';

    public function mount(
        $selected = [], 
        $placeholder = null, 
        $model = null, 
        $title = 'title', 
        $id = 'id'
    ) {
        if (!$model) {
            throw new \Exception("Model không được để trống");
        }

        $this->model = $model;
        $this->title = $title;
        $this->id = $id;
        $this->selected = is_array($selected) ? $selected : [$selected];

        // Tạo full namespace model
        $class = "App\\Models\\" . $this->model;

        if (!class_exists($class)) {
            throw new \Exception("Model $class không tồn tại");
        }

        // Lấy dữ liệu
        $this->options = $class::pluck($this->title, $this->id)->toArray();

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
    }

    public function updatedSelected()
    {
        // logger()->info('Selected multiple:', $this->selected);
        // $this->dispatch('options-selected', $this->selected);
    }

    public function render()
    {
        return view('livewire.select-options-table');
    }
}
