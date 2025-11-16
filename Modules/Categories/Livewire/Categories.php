<?php

namespace Modules\Categories\Livewire;

use Livewire\Component;
use App\Models\Category;

class Categories extends Component
{
    public $slug;
    public $selectedCategories = [];
    public $categories;
    public $render = 'tree'; // <-- public property, nhận giá trị từ attribute render="..."

    public function mount($slug = 'nhom-thuoc', $selectedCategories = [])
    {
        $this->slug = $slug;
        $this->selectedCategories = $selectedCategories;

        $root = Category::where('slug', $this->slug)->first();

        if ($root) {
            $this->categories = Category::with('children')
                ->where('id', $root->id)
                ->orWhere('parent_id', $root->id)
                ->get();
        } else {
            $this->categories = collect();
        }
    }

    public function render()
    {
        return view('Categories::livewire.categories');
    }

    /* TREE RENDER (checkbox lines) */
    public function renderCategoryTree($categories, $selectedCategories = [], $level = 0, &$rendered = null)
    {
        if ($rendered === null) $rendered = [];

        $html = '';

        foreach ($categories as $category) {
            if (in_array($category->id, $rendered, true)) continue;
            $rendered[] = $category->id;

            $margin = $level * 20;
            $checked = in_array($category->id, $selectedCategories, true) ? 'checked' : '';

            $html .= '<div class="form-check" style="margin-left:'.$margin.'px">';
            $html .= '  <input type="checkbox"
                                class="form-check-input"
                                wire:model="selectedCategories"
                                value="'.e($category->id).'"
                                id="cat_'.e($category->id).'" '.$checked.'>';
            $html .= '  <label class="form-check-label" for="cat_'.e($category->id).'">'
                        .e($category->name).'</label>';
            $html .= '</div>';

            if ($category->children && $category->children->count()) {
                $html .= $this->renderCategoryTree(
                    $category->children,
                    $selectedCategories,
                    $level + 1,
                    $rendered
                );
            }
        }

        return $html;
    }

    /* DROPDOWN TREE (checkbox inside dropdown) */
    public function renderCategoryDropdown($categories, $selectedCategories = [], $level = 0, &$rendered = null)
    {
        if ($rendered === null) $rendered = [];
        $html = '';

        foreach ($categories as $category) {
            if (in_array($category->id, $rendered, true)) continue;
            $rendered[] = $category->id;

            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
            $checked = in_array($category->id, $selectedCategories, true) ? 'checked' : '';
            $hasChildren = $category->children && $category->children->count();

            $html .= '<div class="dropdown-item">';
            if ($hasChildren) {
                $html .= '<span class="toggle-node" data-target="node-'.$category->id.'" style="cursor:pointer;font-weight:bold;">+</span>&nbsp;';
            } else {
                $html .= '<span style="padding-left:12px;"></span>';
            }

            $html .= $indent.'<input type="checkbox" wire:model="selectedCategories" value="'.e($category->id).'" '.$checked.' style="margin-right:6px;">';
            $html .= '<span>'.e($category->name).'</span>';
            $html .= '</div>';

            if ($hasChildren) {
                $html .= '<div id="node-'.$category->id.'" class="ml-3" style="display:none;">';
                $html .= $this->renderCategoryDropdown($category->children, $selectedCategories, $level+1, $rendered);
                $html .= '</div>';
            }
        }

        return $html;
    }


}
