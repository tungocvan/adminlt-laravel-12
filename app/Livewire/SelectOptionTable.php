<?php

namespace App\Livewire;

use Livewire\Component;

class SelectOptionTable extends Component
{
    public $options = [];
    public $selected = null;
    public $placeholder = 'Chọn mục';

    // tham số truyền từ blade
    public $model;
    public $title = 'name';
    public $id = 'id';

    public function mount($selected = null, $placeholder = 'Chọn mục', $model = 'User', $title = 'name', $id = 'id')
    {
        if (!$model) {
            throw new \Exception("Model không được để trống");
        }

        $this->model = $model;
        $this->title = $title;
        $this->id = $id;
        $this->selected = $selected;

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
        // bạn có thể emit event nếu cần
        // $this->dispatch('product-selected', $this->selected);
        // hoặc xử lý trực tiếp
        // logger()->info('Selected:', ['id' => $this->selected]);
    }
    public function render()
    {
        return view('livewire.select-option-table');
    }
}
