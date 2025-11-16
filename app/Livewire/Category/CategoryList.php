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


    public $perPage = 'all';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    // Filter
    public $filterType = '';
    public $filterParentOnly = false;
    public $selectedParent = null;
    public $isChange = false;
    protected $queryString = [
        'page' => ['except' => 1],
        'perPage' => ['except' => 10],
        'filterType' => ['except' => ''],
        'filterParentOnly' => ['except' => false],
        'selectedParent' => ['except' => null],
    ];

    protected string $paginationTheme = 'bootstrap';

    public $customType = null;
    public $isAddingType = false;
    public $typeOptions = ['category' => 'Category', 'menu' => 'Menu']; // danh sách loại có sẵn

    public $selectedCategories = [];
    
    public $selectAll = false;
    public $importFile;
    public $exportFileName = 'categories.json';
    public $isExport = false;

    protected function rules()
    {
        // $allowedTypes = array_keys($this->typeOptions ?: ['category' => 'Category', 'menu' => 'Menu']);

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

    public function mount()
    {
        $this->typeOptions = \App\Models\Category::query()
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->filter() // bỏ null, rỗng
            ->mapWithKeys(fn($t) => [$t => ucfirst($t)])
            ->toArray() ?: ['category' => 'Category'];
    }
 
    public function render()
    {
        $query = Category::query()
            ->when($this->selectedParent, function ($q) {
                $ids = [$this->selectedParent, ...$this->getDescendantIds($this->selectedParent)];
                $q->whereIn('id', $ids);
            })
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterParentOnly, fn($q) => $q->whereNull('parent_id'))
            ->orderBy($this->sortField, $this->sortDirection);

        $perPage = $this->perPage === 'all' ? 1_000_000 : max((int)$this->perPage, 10);
        // logger('Livewire page: '.$this->page ?? 'no page');    

        //dd($query->paginate($perPage));

        return view('livewire.category.category-list', [
            'categories' => $query->paginate($perPage),
            'parents'    => $this->getCategoryTree(),
        ]);
    }

    public function updated($propertyName)
    {
        // reset lại pagination khi đổi bộ lọc
        if (in_array($propertyName, ['filterType', 'filterParentOnly', 'selectedParent'])) {
            $this->resetPage();
        }

        // đánh dấu form thay đổi
        if (in_array($propertyName, [
            'name',
            'slug',
            'url',
            'icon',
            'can',
            'type',
            'parent_id',
            'description',
            'imageFile',
            'is_active',
            'sort_order',
            'meta_title',
            'meta_description'
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

        if ($this->isChange == false) {
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
                ->exists()
            ) {
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

            if ($category->image && Storage::disk('public')->exists($category->image)) {

                Storage::disk('public')->delete($category->image);
            }

            $category->update(['image' => null]);
        }

        // reset trên component
        $this->image = null;
        $this->imageFile = null;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
    public function updatedFilterType()
    {
        $this->resetPage();
    }
    public function updatedSelectedParent()
    {
      
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->reset([
            'categoryId',
            'name',
            'slug',
            'url',
            'icon',
            'can',
            'parent_id',
            'description',
            'image',
            'imageFile',
            'meta_title',
            'meta_description',
        ]);

        $this->type = 'category';
        $this->is_active = true;
        $this->sort_order = 0;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Khi nhấn checkbox Select All
    public function updatedSelectAll($value)
    {
        if ($value) {
            // lấy toàn bộ ID của các danh mục đang hiển thị trong trang hiện tại
            $this->selectedCategories = $this->getCurrentPageCategoryIds();
        } else {
            $this->selectedCategories = [];
        }
    }

    // Lấy danh sách ID các danh mục hiển thị ở trang hiện tại
    private function getCurrentPageCategoryIds()
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

        return $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($perPage)
            ->pluck('id')
            ->toArray();
    }

    // Khi tick từng checkbox riêng lẻ
    public function updatedSelectedCategories()
    {
        $this->selectAll = false;
    }
    public function deleteAll()
    {
        if (empty($this->selectedCategories)) {
            session()->flash('message', 'Không có danh mục nào được chọn để xóa.');
            return;
        }

        // Xóa danh mục theo danh sách ID đã chọn
        Category::whereIn('id', $this->selectedCategories)->delete();

        // Reset lại sau khi xóa
        $this->selectedCategories = [];
        $this->selectAll = false;

        session()->flash('message', 'Đã xóa các danh mục được chọn.');
    }
    protected function getCategoriesWithChildren(array $selectedIds)
    {
        $all = collect();
        $queue = collect($selectedIds);

        while ($queue->isNotEmpty()) {
            $id = $queue->shift();

            $category = \App\Models\Category::find($id);
            if ($category && !$all->contains('id', $category->id)) {
                $all->push($category);

                // Tìm các con
                $children = \App\Models\Category::where('parent_id', $category->id)->get();
                foreach ($children as $child) {
                    $queue->push($child->id);
                }
            }
        }

        // Giữ thứ tự tăng dần ID
        return $all->sortBy('id');
    }



    /**
     * Export toàn bộ danh mục ra file JSON
     */
    public function exportJson()
    {
        try {
            $fileName = trim($this->exportFileName) ?: 'categories.json';

            // Thêm đuôi .json nếu chưa có
            if (!Str::endsWith($fileName, '.json')) {
                $fileName .= '.json';
            }

            // Đảm bảo thư mục tồn tại
            $dir = storage_path('app/public/json');
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $filePath = $dir . '/' . $fileName;

            // 🔹 Kiểm tra xem có danh mục được chọn không
            if (!empty($this->selectedCategories)) {
                // Lấy các danh mục được chọn và con của chúng
                $categories = $this->getCategoriesWithChildren($this->selectedCategories);
            } else {
                // Nếu không chọn gì thì export tất cả
                $categories = \App\Models\Category::orderBy('id')->get();
            }

            if ($categories->isEmpty()) {
                session()->flash('error', 'Không có danh mục nào để xuất!');
                return;
            }

            // Ghi file JSON
            $json = json_encode($categories->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($filePath, $json);

            session()->flash('message', "Đã xuất danh mục ra file: json/{$fileName}");
        } catch (\Throwable $e) {
            session()->flash('error', 'Lỗi khi xuất JSON: ' . $e->getMessage());
        }
    }


    /**
     * Import từ file JSON
     */
    public function importJson()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:json',
        ]);

        $path = $this->importFile->store('json', 'public');
        $filePath = storage_path('app/public/' . $path);
        $data = json_decode(file_get_contents($filePath), true);

        if (!is_array($data)) {
            session()->flash('error', 'File JSON không hợp lệ.');
            return;
        }

        \DB::beginTransaction();

        try {
            // Tắt kiểm tra khóa ngoại tạm thời
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Map ID cũ -> ID mới
            $idMap = [];

            // B1: Tạo các danh mục gốc trước
            foreach ($data as $item) {
                if (empty($item['parent_id'])) {
                    $oldId = $item['id'];
                    unset($item['id']); // để DB tự tăng ID
                    $new = Category::create($item);
                    $idMap[$oldId] = $new->id;
                }
            }

            // B2: Tạo danh mục con (đệ quy hoặc vòng lặp nhiều lần)
            $remaining = true;
            $maxLoop = 10; // tránh vòng lặp vô hạn
            while ($remaining && $maxLoop-- > 0) {
                $remaining = false;

                foreach ($data as $item) {
                    $oldId = $item['id'];

                    // Nếu đã tạo thì bỏ qua
                    if (isset($idMap[$oldId])) {
                        continue;
                    }

                    // Nếu cha đã tồn tại
                    if (isset($idMap[$item['parent_id']]) || $item['parent_id'] === null) {
                        $newData = $item;
                        unset($newData['id']);
                        $newData['parent_id'] = $item['parent_id'] ? $idMap[$item['parent_id']] : null;
                        $new = Category::create($newData);
                        $idMap[$oldId] = $new->id;
                    } else {
                        $remaining = true;
                    }
                }
            }

            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \DB::commit();

            session()->flash('message', 'Import JSON thành công, bao gồm cả danh mục con!');
        } catch (\Throwable $e) {
            \DB::rollBack();
            session()->flash('error', 'Import lỗi: ' . $e->getMessage());
        }
    }

    public function restoreDefault()
    {
        try {
            $filePath = storage_path('app/public/json/categories.json');

            if (!file_exists($filePath)) {
                session()->flash('error', 'Không tìm thấy file mặc định: categories.json');
                return;
            }

            $data = json_decode(file_get_contents($filePath), true);

            if (!is_array($data)) {
                session()->flash('error', 'File JSON mặc định không hợp lệ.');
                return;
            }

            \DB::beginTransaction();
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Category::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Import lại toàn bộ dữ liệu
            $idMap = [];
            foreach ($data as $item) {
                if (empty($item['parent_id'])) {
                    $oldId = $item['id'];
                    unset($item['id']);
                    $new = Category::create($item);
                    $idMap[$oldId] = $new->id;
                }
            }

            $remaining = true;
            $maxLoop = 10;
            while ($remaining && $maxLoop-- > 0) {
                $remaining = false;

                foreach ($data as $item) {
                    $oldId = $item['id'];
                    if (isset($idMap[$oldId])) continue;

                    if (isset($idMap[$item['parent_id']]) || $item['parent_id'] === null) {
                        $newData = $item;
                        unset($newData['id']);
                        $newData['parent_id'] = $item['parent_id'] ? $idMap[$item['parent_id']] : null;
                        $new = Category::create($newData);
                        $idMap[$oldId] = $new->id;
                    } else {
                        $remaining = true;
                    }
                }
            }

            \DB::commit();
            session()->flash('message', 'Phục hồi mặc định thành công từ categories.json!');
        } catch (\Throwable $e) {
            \DB::rollBack();
            session()->flash('error', 'Lỗi khi phục hồi: ' . $e->getMessage());
        }
    }
}