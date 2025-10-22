<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class Medicines extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $imageUpload;

    public $showForm = false;
    public $medicineId;
    public $categoryFilter = '';

    public $stt_tt20_2022;
    public $phan_nhom_tt15;
    public $ten_hoat_chat;
    public $nong_do_ham_luong;
    public $ten_biet_duoc;
    public $dang_bao_che;
    public $duong_dung;
    public $don_vi_tinh;
    public $quy_cach_dong_goi;
    public $giay_phep_luu_hanh;
    public $han_dung;
    public $co_so_san_xuat;
    public $nuoc_san_xuat;
    public $gia_ke_khai;
    public $don_gia;
    public $gia_von;
    public $nha_phan_phoi;
    public $nhom_thuoc;
    public $link_hinh_anh;

    public $categories = [];
    public $selectedCategories = [];
    public $selectedCategory = null;
    public $selectedProducts = [];
    public $selectAll = false;
    public $bulkCategory = null;
    public $slug = 'nhom-thuoc'; // slug cố định

    protected function rules(): array
    {
        return [
            'ten_biet_duoc' => 'required|string|max:255',
            'ten_hoat_chat' => 'nullable|string|max:255',
            'gia_ke_khai' => 'nullable|numeric',
            'don_gia' => 'nullable|numeric',
            'gia_von' => 'nullable|numeric',
            'link_hinh_anh' => 'nullable|string',
        ];
    }

    protected $messages = [
        'ten_biet_duoc.required' => 'Vui lòng nhập tên biệt dược',
    ];

    

    public function mount()
    {
        // Lấy ID danh mục gốc theo slug
        $root = Category::where('slug', $this->slug)->first();

        if ($root) {
            // Lấy danh mục gốc + danh mục con
            $this->categories = Category::with('children')
                ->where('id', $root->id)
                ->orWhere('parent_id', $root->id)
                ->get();
        } else {
            $this->categories = collect(); // tránh lỗi null
        }
        $this->perPage = session('medicines_per_page', $this->perPage);
    }

    public function render()
    {
        $query = Medicine::query()->with('categories');

        if (!empty($this->selectedCategories)) {
            $query->whereHas('categories', function ($q) {
                $q->whereIn('categories.id', $this->selectedCategories);
            });
        }

        $medicines = $query->get();

        return view('livewire.medicines', [
            'medicines' => $medicines,
            'categories' => $this->categories,
        ]);
    }

    public function updatedSelectedCategories()
    {
        // Cập nhật lọc danh mục khi chọn checkbox
        $this->render();
    }


    
    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->dispatch('setHeader', 'Thêm thuốc');
    }

    public function edit($id)
    {
        $medicine = Medicine::with('categories')->findOrFail($id);

        $this->medicineId = $medicine->id;
        $this->stt_tt20_2022 = $medicine->stt_tt20_2022;
        $this->phan_nhom_tt15 = $medicine->phan_nhom_tt15;
        $this->ten_hoat_chat = $medicine->ten_hoat_chat;
        $this->nong_do_ham_luong = $medicine->nong_do_ham_luong;
        $this->ten_biet_duoc = $medicine->ten_biet_duoc;
        $this->dang_bao_che = $medicine->dang_bao_che;
        $this->duong_dung = $medicine->duong_dung;
        $this->don_vi_tinh = $medicine->don_vi_tinh;
        $this->quy_cach_dong_goi = $medicine->quy_cach_dong_goi;
        $this->giay_phep_luu_hanh = $medicine->giay_phep_luu_hanh;
        $this->han_dung = $medicine->han_dung;
        $this->co_so_san_xuat = $medicine->co_so_san_xuat;
        $this->nuoc_san_xuat = $medicine->nuoc_san_xuat;
        $this->gia_ke_khai = $medicine->gia_ke_khai;
        $this->don_gia = $medicine->don_gia;
        $this->gia_von = $medicine->gia_von;
        $this->nha_phan_phoi = $medicine->nha_phan_phoi;
        $this->nhom_thuoc = $medicine->nhom_thuoc;
        $this->link_hinh_anh = $medicine->link_hinh_anh;
        $this->selectedCategories = $medicine->categories->pluck('id')->toArray();

        $this->showForm = true;
        $this->dispatch('setHeader', 'Chỉnh sửa thuốc');
    }

    public function save()
    {
     
        $this->validate();
        
        if ($this->imageUpload) {
            if ($this->link_hinh_anh && Storage::disk('public')->exists($this->link_hinh_anh)) {
                Storage::disk('public')->delete($this->link_hinh_anh);
            }
            $this->link_hinh_anh = $this->imageUpload->store('medicines', 'public');
        }

        $data = [
            'stt_tt20_2022' => $this->stt_tt20_2022,
            'phan_nhom_tt15' => $this->phan_nhom_tt15,
            'ten_hoat_chat' => $this->ten_hoat_chat,
            'nong_do_ham_luong' => $this->nong_do_ham_luong,
            'ten_biet_duoc' => $this->ten_biet_duoc,
            'dang_bao_che' => $this->dang_bao_che,
            'duong_dung' => $this->duong_dung,
            'don_vi_tinh' => $this->don_vi_tinh,
            'quy_cach_dong_goi' => $this->quy_cach_dong_goi,
            'giay_phep_luu_hanh' => $this->giay_phep_luu_hanh,
            'han_dung' => $this->han_dung,
            'co_so_san_xuat' => $this->co_so_san_xuat,
            'nuoc_san_xuat' => $this->nuoc_san_xuat,
            'gia_ke_khai' => $this->gia_ke_khai,
            'don_gia' => $this->don_gia,
            'gia_von' => $this->gia_von,
            'nha_phan_phoi' => $this->nha_phan_phoi,
            'nhom_thuoc' => $this->nhom_thuoc,
            'link_hinh_anh' => $this->link_hinh_anh,
        ];

        $medicine = Medicine::updateOrCreate(
            ['id' => $this->medicineId],
            $data
        );

        $medicine->categories()->sync($this->selectedCategories ?? []);

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('setHeader', 'Danh sách thuốc');
        session()->flash('success', "Lưu '{$medicine->ten_biet_duoc}' thành công.");
        $this->redirect('/medicine');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('setHeader', 'Danh sách thuốc');
    }

    public function delete($id)
    {
        $item = Medicine::findOrFail($id);
        $title = $item->ten_biet_duoc;
        if ($item->link_hinh_anh && Storage::disk('public')->exists($item->link_hinh_anh)) {
            Storage::disk('public')->delete($item->link_hinh_anh);
        }
        $item->delete();
        session()->flash('success', "Đã xoá $title thành công.");
    }

    private function resetForm()
    {
        $this->reset([
            'medicineId',
            'stt_tt20_2022','phan_nhom_tt15','ten_hoat_chat','nong_do_ham_luong','ten_biet_duoc',
            'dang_bao_che','duong_dung','don_vi_tinh','quy_cach_dong_goi',
            'giay_phep_luu_hanh','han_dung','co_so_san_xuat','nuoc_san_xuat',
            'gia_ke_khai','don_gia','gia_von','nha_phan_phoi','nhom_thuoc',
            'link_hinh_anh','selectedCategories','imageUpload'
        ]);
    }

    public function removeImage()
    {
        if ($this->link_hinh_anh && Storage::disk('public')->exists($this->link_hinh_anh)) {
            Storage::disk('public')->delete($this->link_hinh_anh);
        }
        $this->link_hinh_anh = null;
    }

    public function updatedImageUpload()
    {
        $this->link_hinh_anh = null;
    }
    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $q = Medicine::when($this->search, fn($q) =>
                $q->where('ten_biet_duoc', 'like', "%{$this->search}%")
                  ->orWhere('ten_hoat_chat', 'like', "%{$this->search}%")
            )->orderBy($this->sortField, $this->sortDirection);

            if ($this->perPage === 'all') {
                $this->selectedProducts = $q->pluck('id')->toArray();
            } else {
                $this->selectedProducts = $q->paginate($this->perPage)->pluck('id')->toArray();
            }
        } else {
            $this->selectedProducts = [];
        }
    }

    public function deleteSelected()
    {
        if (!empty($this->selectedProducts)) {
            $items = Medicine::whereIn('id', $this->selectedProducts)->get();
            foreach ($items as $item) {
                if ($item->link_hinh_anh && Storage::disk('public')->exists($item->link_hinh_anh)) {
                    Storage::disk('public')->delete($item->link_hinh_anh);
                }
            }
            Medicine::whereIn('id', $this->selectedProducts)->delete();
            $this->selectedProducts = [];
            $this->selectAll = false;
            $this->dispatch('setHeader', 'Danh sách thuốc');
            session()->flash('message', 'Đã xóa các thuốc đã chọn.');
        }
    }

    public function duplicate($id)
    {
        $medicine = Medicine::with('categories')->findOrFail($id);

        $new = $medicine->replicate();
        $new->ten_biet_duoc = $medicine->ten_biet_duoc . ' (Bản sao)';
        $new->save();
        $new->categories()->sync($medicine->categories->pluck('id')->toArray());

        session()->flash('success', "Đã nhân bản thuốc '{$medicine->ten_biet_duoc}' thành công.");
    }

    public function filterByCategory()
    {
        // Khi người dùng bấm nút lọc
        $this->resetPage();
    }

    public function applySelectedCategory()
    {
        if (!$this->selectedCategory || count($this->selectedProducts) === 0) {
            session()->flash('message', 'Vui lòng chọn thuốc và danh mục để áp dụng.');
            return;
        }

        foreach ($this->selectedProducts as $id) {
            $medicine = Medicine::find($id);
            if ($medicine) {
                $medicine->categories()->syncWithoutDetaching([$this->selectedCategory]);
            }
        }

        $this->selectedProducts = [];
        session()->flash('success', 'Cập nhật danh mục thành công!');
    }

    public function exportJson()
    {
        $categories = Category::select('id','name','slug','type','is_active')->get();

        $query = Medicine::with('categories');
        if (!empty($this->selectedProducts)) {
            $query->whereIn('id', $this->selectedProducts);
        }
        $medicines = $query->get();

        $data = [
            'categories' => $categories->map(function($cat){
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'type' => $cat->type,
                    'is_active' => (bool) ($cat->is_active ?? false),
                ];
            })->toArray(),
            'medicines' => $medicines->map(function($m){
                return [
                    'id' => $m->id,
                    'ten_biet_duoc' => $m->ten_biet_duoc,
                    'ten_hoat_chat' => $m->ten_hoat_chat,
                    'don_gia' => $m->don_gia,
                    'gia_ke_khai' => $m->gia_ke_khai,
                    'link_hinh_anh' => $m->link_hinh_anh ?? 'images/default.jpg',
                    'categories' => $m->categories->pluck('id')->toArray(),
                ];
            })->toArray(),
        ];

        $filename = "medicines_export_" . now()->format('Ymd_His') . ".json";
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function updatingPerPage($value)
    {
        session(['medicines_per_page' => $value]);
        $this->resetPage();
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
}
