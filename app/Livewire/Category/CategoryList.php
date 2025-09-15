<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class CategoryList extends Component
{
    use WithPagination, WithFileUploads;

    public $isModalOpen = false;
    public $categoryId;

    public $name, $slug, $url, $icon, $can, $type = 'category';
    public $parent_id, $description, $image, $imageFile, $is_active = true;
    public $sort_order = 0, $meta_title, $meta_description;

    public $perPage = 10;

    // Filter
    public $filterType = '';
    public $filterParentOnly = false;
    public $selectedParent = null;

    protected $queryString = [
        'perPage' => ['except' => 10],
    ];

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
            'imageFile' => 'nullable|image|max:1024',
        ];
    }

    public function render()
    {
        $query = Category::query();

        if ($this->selectedParent) {
            $ids = [$this->selectedParent];
            $ids = array_merge($ids, $this->getDescendantIds($this->selectedParent));
            $query->whereIn('id', $ids);
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterParentOnly) {
            $query->whereNull('parent_id');
        }

        $perPage = $this->perPage === 'all' ? 1000000 : max((int)$this->perPage, 10);

        $categories = $query->orderBy('sort_order')->paginate($perPage);

        return view('livewire.category.category-list', [
            'categories' => $categories,
            'parents' => $this->getCategoryTree(),
        ]);
    }

    private function getCategoryTree($parentId = null, $prefix = '')
    {
        $query = Category::query()->orderBy('sort_order');

        if ($parentId === null) {
            if ($this->selectedParent) {
                $query->where('id', $this->selectedParent);
            } else {
                $query->whereNull('parent_id');
            }
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
                'id' => $item->id,
                'name' => $prefix . $item->name,
                'slug' => $item->slug,
                'type' => $item->type,
                'is_active' => $item->is_active,
                'parent_id' => $item->parent_id,
            ];

            $tree = array_merge($tree, $this->getCategoryTree($item->id, $prefix . '— '));
        }

        return $tree;
    }

    private function getDescendantIds($parentId)
    {
        $ids = [];
        $children = Category::where('parent_id', $parentId)->pluck('id');
        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getDescendantIds($childId));
        }
        return $ids;
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

        if (empty($data['slug'])) {
            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)
                ->when($this->categoryId, fn($q) => $q->where('id', '!=', $this->categoryId))
                ->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($this->imageFile) {
            $data['image'] = $this->imageFile->store('categories', 'public');
        }

        if ($this->categoryId) {
            Category::findOrFail($this->categoryId)->update($data);
            session()->flash('message', 'Cập nhật thành công');
        } else {
            Category::create($data);
            session()->flash('message', 'Thêm mới thành công');
        }

        $this->resetInputFields();
        $this->isModalOpen = false;
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Xóa thành công');
    }

    public function updatedName($value)
    {
        if (!$this->categoryId) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedPerPage() { $this->resetPage(); }
    public function updatedFilterType() { $this->resetPage(); }
    public function updatedSelectedParent() { $this->resetPage(); }

    public function closeModal() { $this->isModalOpen = false; }

    private function resetInputFields()
    {
        $this->reset([
            'categoryId',
            'name', 'slug', 'url', 'icon', 'can',
            'parent_id', 'description', 'image', 'imageFile',
            'meta_title', 'meta_description',
        ]);

        $this->type = 'category';
        $this->is_active = true;
        $this->sort_order = 0;
    }
}
