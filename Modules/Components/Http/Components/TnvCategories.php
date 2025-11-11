<?php

namespace Modules\Components\Http\Components;

use Illuminate\View\Component;
use App\Models\Category;

class TnvCategories extends Component
{
    public $name;
    public $selected;
    public $filterType;
    public $label;
    public $categories = [];

    public function __construct($name = 'category_id', $selected = null, $filterType = null, $label = 'Chọn danh mục')
    {
        $this->name = $name;
        $this->selected = $selected;
        $this->filterType = $filterType;
        $this->label = $label;

        $this->categories = $this->getCategoryTree();
    }

    private function getCategoryTree($parentId = null, $prefix = '')
    {
        $query = Category::query()->orderBy('sort_order');

        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
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

    public function render()
    {
        return view('Components::components.tnv-categories');
    }
}
