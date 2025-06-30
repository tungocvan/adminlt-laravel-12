<?php

namespace App\Livewire;

use Livewire\Component;

class Category extends Component
{

    public $selectedCategories;
    public $categoriesTree = [];

    public function mount($taxonomy = 'product_cat')
    {
        $allCategories = getCategories($taxonomy);
        $this->categoriesTree = $this->buildTree($allCategories->toArray());
    }

    protected function buildTree(array $elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent == $parentId) {
                $children = $this->buildTree($elements, $element->term_id);
                if ($children) {
                    $element->children = $children; // Thêm danh mục con
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function updatedSelectedCategories($value)
    {
        // Kiểm tra giá trị khi nó thay đổi
        //$this->selectedCategories = $value;
        //dd($value);
    }

    public function render()
    {
        return view('livewire.category');
    }
}