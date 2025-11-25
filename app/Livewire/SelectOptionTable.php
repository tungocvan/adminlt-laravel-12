<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Modelable;
use Illuminate\Support\Str;

class SelectOptionTable extends Component
{
    #[Modelable]
    public $selected = null;

    public $placeholder = 'Chọn mục';

    // 2 chế độ
    public $model = null;      // null => dùng options array
    public $options = [];      // nếu truyền options thì không load Model

    // Nếu dùng model
    public $title = 'name';
    public $id = 'id';
    public $filters = [];

    public $class = 'tnv-option';

    public function mount(
        $selected = null,
        $placeholder = 'Chọn mục',
        $model = null,
        $title = 'name',
        $id = 'id',
        $filters = [],
        $options = []   // ✨ mới thêm
    ) {
        $this->selected    = $selected;
        $this->placeholder = $placeholder;

        $this->model       = $model;
        $this->title       = $title;
        $this->id          = $id;
        $this->filters     = $filters;

        $this->options     = $options;

        $this->class       = 'tnv-option-'.Str::random(4);

        // Nếu không truyền options → load từ model
        if (empty($this->options)) {
            $this->loadOptions();
        }
    }

    public function loadOptions()
    {
        // ❗ Nếu đã truyền options → không load từ DB nữa!
        if (!empty($this->options)) return;

        if (!$this->model) return;

        $class = "App\\Models\\" . $this->model;

        if (!class_exists($class)) {
            throw new \Exception("Model $class không tồn tại");
        }

        $query = $class::query();

        foreach ($this->filters as $column => $value) {
            if ($value !== null && $value !== '') {
                $query->where($column, $value);
            } else {
                $this->options = [];
                return;
            }
        }

        $this->options = $query->pluck($this->title, $this->id)->toArray();
    }

    public function updatedFilters()
    {
        if (empty($this->options)) {
            $this->loadOptions();
        }
    }

    public function updatedSelected()
    {
        if (empty($this->options)) {
            $this->loadOptions();
        }
    }

    public function render()
    {
        return view('livewire.select-option-table');
    }
}
