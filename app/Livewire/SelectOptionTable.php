<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Modelable;

class SelectOptionTable extends Component
{
    public $options = [];

    #[Modelable]
    public $selected = null;

    public $placeholder = 'Chọn mục';

    // props truyền từ Blade
    public $model;
    public $title = 'name';
    public $id = 'id';
    public $filters = [];

    public function mount(
        $selected = null,
        $placeholder = 'Chọn mục',
        $model = 'User',
        $title = 'name',
        $id = 'id',
        $filters = []
    ) {
        $this->selected = $selected;
        $this->placeholder = $placeholder;
        $this->model = $model;
        $this->title = $title;
        $this->id = $id;
        $this->filters = $filters;

        $this->loadOptions();
    }

    public function loadOptions()
    {
        $class = "App\\Models\\" . $this->model;

        if (!class_exists($class)) {
            throw new \Exception("Model $class không tồn tại");
        }

        $query = $class::query();

        // Áp dụng filters dạng ['status' => 1, 'type' => 'customer']
        foreach ($this->filters as $column => $value) {
            if ($value !== null && $value !== '') {
                $query->where($column, $value);
            }else{
                $this->options = [];
                return ;
            }
        }

        $this->options = $query->pluck($this->title, $this->id)->toArray();
    }

    public function updatedFilters()
    {
        $this->loadOptions();
       
    }

    public function updatedSelected()
    {
        // sync UI → Livewire
        \Log::info('updatedSelected:'. $this->id);
        $this->loadOptions();
       
      
    }
  


    public function render()
    {
        return view('livewire.select-option-table');
    }
}
