<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryManager extends Component
{
    use WithPagination;

    /** ----------------------------
     *  Public Properties
     *  ---------------------------- */
    public $name, $slug, $type, $parent_id, $description;
    public $editingId = null;

    public $filterType = '';
    public $filterParentOnly = '';
    public $selectedParent = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $typeOptions = ['category' => 'Category', 'menu' => 'Menu'];

    public $jsonFile;

    protected $paginationTheme = 'bootstrap';

    /** ----------------------------
     *  Validation Rules
     *  ---------------------------- */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'required|in:menu,category',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ];
    }
    public function updatedFilterType($value)
    {
        //$this->getFilteredCategories();
        $this->resetPage();
    }

    /** ----------------------------
     *  Computed: Danh sách Categories
     *  ---------------------------- */
    public function getFilteredCategories()
    {
        $query = Category::query();

        // 1️⃣ Lọc theo parent (bao gồm toàn bộ con)
        if ($this->selectedParent) {
            $ids = array_merge([$this->selectedParent], $this->getDescendantIds($this->selectedParent));
            $query->whereIn('id', $ids);
        }

        // 2️⃣ Lọc theo type (case-insensitive, safe với NULL)
        if ($this->filterType !== '') {
            $filterType = strtolower($this->filterType);
            $query->whereRaw('LOWER(COALESCE(type,"")) = ?', [$filterType]);
        }

        // 3️⃣ Chỉ lấy danh mục cấp 1 (không có parent)
        if ($this->filterParentOnly) {
            $query->whereNull('parent_id');
        }

        // 4️⃣ Sắp xếp theo field và direction
        $query->orderBy($this->sortField, $this->sortDirection);

        // 5️⃣ Lấy tất cả (collection, không paginate)
        return $query->get();
    }

    /** ----------------------------
     *  Event: Create Form
     *  ---------------------------- */
    public function create()
    {
        $this->resetForm();
        $this->dispatch('showModal', id: 'categoryModal');
    }

    /** ----------------------------
     *  Event: Edit Form
     *  ---------------------------- */
    public function edit($id)
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $cat->id;

        $this->name = $cat->name;
        $this->slug = $cat->slug;
        $this->type = $cat->type;
        $this->parent_id = $cat->parent_id;
        $this->description = $cat->description;

        $this->dispatch('showModal', id: 'categoryModal');
    }

    /** ----------------------------
     *  Save / Update
     *  ---------------------------- */
    public function save()
    {
        $this->validate();
        $data = $this->modelData();

        Category::updateOrCreate(['id' => $this->editingId], $data);

        session()->flash('message', $this->editingId ? 'Cập nhật thành công' : 'Thêm mới thành công');

        $this->resetForm();
        $this->dispatch('hideModal', id: 'categoryModal');
    }

    /** ----------------------------
     *  Xác nhận xóa
     *  ---------------------------- */
    public function confirmDelete($id)
    {
        $this->editingId = $id;
        $this->dispatch('confirming-delete');
    }

    /** ----------------------------
     *  Xóa Nhóm
     *  ---------------------------- */
    public function delete()
    {
        Category::findOrFail($this->editingId)->delete();

        $this->editingId = null;
        session()->flash('message', 'Xóa thành công');
    }

    /** ----------------------------
     *  Data Builder
     *  ---------------------------- */
    private function modelData()
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug ?: Str::slug($this->name),
            'type' => $this->type,
            'parent_id' => $this->parent_id ?: null,
            'description' => $this->description,
        ];
    }

    /** ----------------------------
     *  Reset Form
     *  ---------------------------- */
    private function resetForm()
    {
        $this->reset(['name', 'slug', 'type', 'parent_id', 'description', 'editingId']);
    }

    /** ----------------------------
     *  Recursive Descendant Ids
     *  ---------------------------- */
    public function getDescendantIds($id)
    {
        $children = Category::where('parent_id', $id)->pluck('id')->toArray();

        foreach ($children as $childId) {
            $children = array_merge($children, $this->getDescendantIds($childId));
        }

        return $children;
    }

    /** ----------------------------
     *  Category Tree For Parent Select
     *  ---------------------------- */
    public function getCategoryTree()
    {
        $all = Category::orderBy('name')->get();

        $tree = [];

        foreach ($all as $item) {
            $tree[] = [
                'id' => $item->id,
                'label' => $this->buildLabel($item),
            ];
        }

        return $tree;
    }

    private function buildLabel($cat, $level = 0)
    {
        $prefix = str_repeat('— ', $level);

        if ($cat->parent_id == null) {
            return $cat->name;
        }

        $parent = Category::find($cat->parent_id);

        return $this->buildLabel($parent, $level + 1) . ' › ' . $cat->name;
    }

    /** ----------------------------
     *  Import JSON
     *  ---------------------------- */
    public function importJson()
    {
        if (!$this->jsonFile) {
            return session()->flash('message', 'Vui lòng chọn file JSON');
        }

        $raw = file_get_contents($this->jsonFile->getRealPath());
        $data = json_decode($raw, true);

        Category::truncate();
        Category::insert($data);

        session()->flash('message', 'Import JSON thành công');
    }

    /** ----------------------------
     *  Export JSON
     *  ---------------------------- */
    public function exportJson()
    {
        $data = Category::all()->toArray();
        $file = 'categories_' . now()->format('Ymd_His') . '.json';

        Storage::disk('local')->put($file, json_encode($data, JSON_PRETTY_PRINT));

        return response()->download(storage_path("app/$file"));
    }

    /** ----------------------------
     *  Render
     *  ---------------------------- */
     public function render()
{
    $filtered = $this->getFilteredCategories(); // collection đã filter
    $categories = $this->buildTreeFromFiltered($filtered);

    return view('livewire.category.category-manager', [
        'categories' => $categories,
        'parents' => $filtered, // dùng cho select parent filter
    ]);
}

/**
 * Build tree chỉ từ collection đã filter
 */
 public function buildTreeFromFiltered($items, $parentId = null)
{
    return $items
        ->where('parent_id', $parentId)
        ->map(function ($item) use ($items) {
            $children = $this->buildTreeFromFiltered($items, $item->id);
            return [
                'id' => $item->id,
                'name' => $item->name,
                'label' => $item->label,
                'slug' => $item->slug,
                'type' => $item->type,
                'is_active' => $item->is_active,
                'children' => $children->isNotEmpty() ? $children : [],
            ];
        })
        ->values();
}

    /** ----------------------------
     *  Build Tree: dạng mảng phân cấp
     *  ---------------------------- */
    public function getTree()
    {
        $all = Category::orderBy('sort_order')->orderBy('name')->get();

        return $this->buildTreeFromFiltered($all);
    }

    private function buildTree($items, $parentId = null)
    {
        return $items->where('parent_id', $parentId)->map(function ($item) use ($items) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'type' => $item->type,
                'is_active' => $item->is_active,
                'children' => $this->buildTree($items, $item->id),
            ];
        });
    }
}
