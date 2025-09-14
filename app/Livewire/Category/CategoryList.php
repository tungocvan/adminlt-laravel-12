<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;


class CategoryList extends Component
{
    use WithPagination;

    public $isModalOpen = false;
    public $categoryId;
    public $name, $slug, $url, $icon, $can, $type = 'category';
    public $parent_id, $description, $image, $is_active = true;
    public $sort_order = 0, $meta_title, $meta_description;
    public $filterType = ''; // 'category', 'menu' hoặc ''
    public $filterParentOnly = false;
    public $selectedParent = null; // menu gốc được chọn

    protected function rules()
    {
        return [
            'name'  => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:categories,slug,' . $this->categoryId,
            'type'  => 'required|in:category,menu',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'description' => 'nullable|string',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'can' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ];
    }

    public function render()
    {
        $query = Category::query();

        // lọc theo menu parent nếu có chọn
        if ($this->selectedParent) {
            $query->where('parent_id', $this->selectedParent);
        }
        // Lọc theo loại nếu có chọn
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        // Lọc menu gốc nếu check
        if ($this->filterParentOnly) {
            $query->whereNull('parent_id');
        }

        return view('livewire.category.category-list', [
            'categories' => $query->orderBy('sort_order')->paginate(10),
            'parents' => Category::whereNull('parent_id')->pluck('name', 'id')
        ]);
    }


    public function openCreate()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function openEdit($id)
    {
        $category = Category::findOrFail($id);

        $this->categoryId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->url = $category->url;
        $this->icon = $category->icon;
        $this->can = $category->can;
        $this->type = $category->type;
        $this->parent_id = $category->parent_id;
        $this->description = $category->description;
        $this->image = $category->image;
        $this->is_active = (bool) $category->is_active;
        $this->sort_order = $category->sort_order;
        $this->meta_title = $category->meta_title;
        $this->meta_description = $category->meta_description;

        $this->isModalOpen = true;
    }

    public function saveCategory()
    {
        $data = $this->validate();

        // Nếu slug null hoặc rỗng => tự động sinh từ name
        if (empty($data['slug'])) {
            $slug = \Illuminate\Support\Str::slug($data['name']);

            // đảm bảo slug là unique
            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)
                ->when($this->categoryId, fn($q) => $q->where('id', '!=', $this->categoryId))
                ->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $data['slug'] = $slug;
        }

        if ($this->categoryId) {
            Category::findOrFail($this->categoryId)->update($data);
            session()->flash('message', 'Cập nhật thành công');
        } else {
            Category::create($data);
            session()->flash('message', 'Thêm mới thành công');
        }

        $this->isModalOpen = false;
        $this->resetInputFields();
    }



    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Xóa thành công');
    }

    public function updatedName($value)
    {
        // chỉ tạo slug nếu chưa có hoặc đang tạo mới
        if (!$this->slug || !$this->categoryId) {
            $this->slug = Str::slug($value);
        }
    }

    private function resetInputFields()
    {
        $this->reset([
            'categoryId',
            'name', 'slug', 'url', 'icon', 'can', 'type',
            'parent_id', 'description', 'image', 'is_active',
            'sort_order', 'meta_title', 'meta_description',
        ]);

        $this->type = 'category';
        $this->is_active = true;
        $this->sort_order = 0;
    }
    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedSelectedParent()
    {
        $this->resetPage();
    }

    public function openModal() { $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; }
}
