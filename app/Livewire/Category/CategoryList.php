<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


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
    public $isChange = false;
    protected $queryString = [
        'perPage' => ['except' => 10],
    ];

    public $customType = null;
    public $isAddingType = false;
    public $typeOptions = ['category' => 'Category', 'menu' => 'Menu']; // danh sách loại có sẵn

    public function mount()
    {
        $this->loadTypeOptions();
    }

    private function loadTypeOptions()
    {
        $types = \App\Models\Category::select('type')
            ->distinct()
            ->pluck('type')
            ->filter() // loại bỏ null, rỗng
            ->toArray();

        // map thành [key => Label]
        $this->typeOptions = [];
        foreach ($types as $t) {
            $this->typeOptions[$t] = ucfirst($t);
        }

        // nếu rỗng thì thêm mặc định
        if (empty($this->typeOptions)) {
            $this->typeOptions = [
                'category' => 'Category',
            ];
        }
    }


    protected function rules()
    {
        $allowedTypes = array_keys($this->typeOptions ?: ['category' => 'Category','menu' => 'Menu']);

        return [
            'name'  => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:categories,slug,' . $this->categoryId,
            'type' => ['required', 'string', 'max:255'],
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

    
    public function updated($propertyName)
    {
        // Bất kỳ khi nào có thay đổi trong input nào thì đánh dấu đã thay đổi
        if (in_array($propertyName, [
            'name', 'slug', 'url', 'icon', 'can', 'type',
            'parent_id', 'description', 'imageFile', 'is_active',
            'sort_order', 'meta_title', 'meta_description'
        ])) {
            $this->isChange = true;
        }
    }

    public function updatedType($value)
    {
        if ($value === 'new') {
            $this->isAddingType = true;
            $this->customType = '';
        } else {
            $this->isAddingType = false;
        }
    }

    public function saveNewType()
    {
        if (!$this->customType || trim($this->customType) === '') {
            $this->addError('customType', 'Vui lòng nhập tên loại mới.');
            return;
        }

        $key = Str::slug($this->customType);

        if (!isset($this->typeOptions[$key])) {
            $this->typeOptions[$key] = $this->customType;
        }

        $this->type = $key;       // 👈 Quan trọng: phải gán lại type
        $this->isAddingType = false;
        $this->customType = '';
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
        // Nếu user vẫn ở chế độ "thêm mới" (chọn option new)
        if ($this->type === 'new') {
            if ($this->customType && trim($this->customType) !== '') {
                $this->saveNewType(); // sẽ thêm vào $typeOptions và gán $this->type = slug
            } else {
                $this->addError('type', 'Bạn chưa nhập tên loại mới.');
                return;
            }
        }


        $data = $this->validate();     
        
        if($this->isChange == false) {
            $this->resetInputFields();
            $this->isModalOpen = false;
            return;
        }
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


    // xóa ảnh (gọi từ nút X trên UI)
    public function removeImage()
    {
        
        // nếu đã có trong DB, xóa file và cập nhật DB ngay
        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            
            if($category->image && Storage::disk('public')->exists($category->image)) {
                
                Storage::disk('public')->delete($category->image);
            }
           
            $category->update(['image' => null]);
           
        }

        // reset trên component
        $this->image = null;
        $this->imageFile = null;        
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
