<?php

namespace Modules\Components\Livewire;

use Livewire\Component;
use App\Models\Category;

class TnvCategories extends Component
{
    public $model = 'category_id';     // tên field nhận từ wire:model
    public $value = null;              // giá trị selected
    public $type = null;               // lọc theo type (menu/category...)
    public $label = 'Chọn danh mục';   // label
    public $parents = [];              // danh sách category dạng cây

    public function mount($model = 'category_id', $value = null, $type = null, $label = null)
    {
        $this->model = $model;
        $this->value = $value;
        $this->type  = $type;

        if ($label) {
            $this->label = $label;
        }
    }

    public function render()
    {
        
        $this->value = data_get($this, $this->model);
        $this->parents = $this->getCategoryTree();

        return view('Components::livewire.tnv-categories');
    }

    private function getCategoryTree($parentId = null, $prefix = '')
{
    $query = Category::query()->orderBy('sort_order');

    // ✅ Nếu selectedParent có giá trị → chỉ lấy node đó làm root
    if ($parentId === null) {
        if ($this->value) {
            $query->where('id', $this->value);
        } else {
            $query->whereNull('parent_id');
        }
    } else {
        $query->where('parent_id', $parentId);
    }

    if ($this->type) {
        $query->where('type', $this->type);
    }

    $items = $query->get();
    $tree = [];

    foreach ($items as $item) {
        $tree[] = [
            'id'   => $item->id,
            'name' => $prefix . $item->name,
        ];

        $tree = array_merge($tree, $this->getCategoryTree($item->id, $prefix . '— '));
    }

    return $tree;
}

}
