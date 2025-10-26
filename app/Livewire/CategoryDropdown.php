<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class CategoryDropdown extends Component
{
    public $categories = [];
    public $selectedCategories = [];

    public $placeholder = 'Áp dụng danh mục';
    public $width = '40%';
    public $maxHeight = '300px';

    public function mount($categories = [], $selected = [], $placeholder = null, $width = null, $maxHeight = null)
    {
        // Nếu truyền danh sách chưa lọc, component sẽ tự lọc lại để tránh trùng
        $this->categories = collect($categories)->isNotEmpty() ? collect($categories)->whereNull('parent_id') : Category::whereNull('parent_id')->with('childrenRecursive')->get();

        $this->selectedCategories = $selected;
        $this->placeholder = $placeholder ?? 'Áp dụng danh mục';
        $this->width = $width ?? '40%';
        $this->maxHeight = $maxHeight ?? '300px';
    }

    public function updatedSelectedCategories()
    {
        // Gửi event khi thay đổi lựa chọn
        $this->dispatch('categoriesUpdated', $this->selectedCategories);
    }

    public function apply()
    {
        // Gửi event khi nhấn nút Áp dụng
        $this->dispatch('categoriesApplied', $this->selectedCategories);
    }

    public function render()
    {
        return view('livewire.category-dropdown');
    }
}
