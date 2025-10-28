<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Traits\FillsComponentFromModel;

use App\Exports\MedicinesExport;
use Maatwebsite\Excel\Facades\Excel;
// use Illuminate\Support\Facades\Response;
// use App\Exports\MedicinesTemplateExport;

// use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Traits\HasExcelExportTemplate;

class Medicines extends Component
{
    use WithPagination, WithFileUploads;
    use FillsComponentFromModel;
    use HasExcelExportTemplate;

    public $search = '',
        $perPage = 10,
        $sortField = 'id',
        $sortDirection = 'desc';
    public $showForm = false,
        $medicineId,
        $activeTab = 'general',
        $image,
        $link_hinh_anh;
    public $selectedProducts = [],
        $selectAll = false,
        $selectedCategory = null;
    public $ten_biet_duoc, $ten_hoat_chat, $dang_bao_che, $duong_dung;
    public $nong_do_ham_luong, $don_vi_tinh, $quy_cach_dong_goi, $giay_phep_luu_hanh;
    public $han_dung, $co_so_san_xuat, $nuoc_san_xuat, $gia_ke_khai, $don_gia, $gia_von;
    public $trang_thai_trung_thau, $nha_phan_phoi, $nhom_thuoc, $stt_tt20_2022, $phan_nhom_tt15;
    public $link_hssp, $han_dung_visa, $han_dung_gmp;

    public $categories = [],
        $selectedCategories = [],
        $bulkCategory = null;

    protected $listeners = [
        'categoriesApplied' => 'onCategoriesApplied',
        'categoriesUpdated' => 'onCategoriesUpdated',
    ];

    public function onCategoriesApplied($selected)
    {
        $this->selectedCategories = $selected;

        if (empty($this->selectedProducts)) {
            session()->flash('message', 'Vui lòng chọn thuốc cần áp dụng danh mục.');
            return;
        }

        if (empty($selected)) {
            session()->flash('message', 'Vui lòng chọn ít nhất một danh mục.');
            return;
        }

        // ✅ Cập nhật danh mục cho tất cả thuốc đã chọn
        foreach ($this->selectedProducts as $id) {
            $medicine = Medicine::find($id);
            if ($medicine) {
                // Thêm danh mục mà không xoá danh mục cũ
                $medicine->categories()->syncWithoutDetaching($selected);
            }
        }

        // Tuỳ chọn: reset selections
        $this->selectedProducts = [];
        $this->selectedCategories = [];

        session()->flash('success', 'Đã áp dụng danh mục cho các thuốc đã chọn thành công!');
    }

    public function removeCategory($productId, $categoryId, $catName = '')
    {
        //dd($productId, $categoryId);
        $medicine = Medicine::find($productId);
        if (!$medicine) {
            return;
        }
        $name = $medicine['ten_biet_duoc'];

        $medicine->categories()->detach($categoryId);

        session()->flash('message', "Đã xóa danh mục $catName ra khỏi sản phẩm: $name");
    }

    public function onCategoriesUpdated($selected)
    {
        $this->selectedCategories = $selected;
    }

    protected $rules = [
        'ten_biet_duoc' => 'required|string|max:255',
        'ten_hoat_chat' => 'nullable|string|max:255',
        'gia_ke_khai' => 'nullable|numeric',
        'don_gia' => 'nullable|numeric',
    ];

    protected $messages = [
        'ten_biet_duoc.required' => 'Vui lòng nhập tên biệt dược',
    ];

    public function mount()
    {
        $root = Category::where('slug', 'nhom-thuoc')->first();
        if ($root) {
            $this->categories = Category::with('children')->where('id', $root->id)->orWhere('parent_id', $root->id)->get();
        } else {
            $this->categories = collect();
        }
        //dd($this->categories);
        $this->perPage = session('medicines_per_page', $this->perPage);
    }

    public function render()
    {
        $query = Medicine::with('categories')->when($this->search, fn($q) => $q->where(fn($sub) => $sub->where('ten_biet_duoc', 'like', "%$this->search%")->orWhere('ten_hoat_chat', 'like', "%$this->search%")))->when($this->selectedCategory, fn($q) => $q->whereHas('categories', fn($sub) => $sub->where('categories.id', $this->selectedCategory)))->orderBy($this->sortField, $this->sortDirection);

        $medicines = $this->perPage === 'all' ? $query->get() : $query->paginate($this->perPage);

        return view('livewire.medicines', ['medicines' => $medicines]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->dispatch('setHeader', 'Thêm thuốc');
    }

    public function edit($id)
    {
        $m = Medicine::with('categories')->findOrFail($id);
        $this->fillFromModel($m, ['categories']);
        $this->selectedCategories = $m->categories->pluck('id')->toArray();
        //dd($this->selectedCategories);
        $this->medicineId = $id;
        $this->showForm = true;
        $this->dispatch('setHeader', 'Chỉnh sửa thuốc');
    }

    public function save()
    {
        $this->validate();

        // Lưu ảnh nếu có
        if ($this->image) {
            $this->link_hinh_anh = $this->image->store('medicines', 'public');
        }

        // Lưu hoặc cập nhật thuốc
        $medicine = Medicine::updateOrCreate(['id' => $this->medicineId], $this->only(['stt_tt20_2022', 'phan_nhom_tt15', 'ten_hoat_chat', 'nong_do_ham_luong', 'ten_biet_duoc', 'dang_bao_che', 'duong_dung', 'don_vi_tinh', 'quy_cach_dong_goi', 'giay_phep_luu_hanh', 'han_dung', 'co_so_san_xuat', 'nuoc_san_xuat', 'gia_ke_khai', 'don_gia', 'gia_von', 'trang_thai_trung_thau', 'nha_phan_phoi', 'nhom_thuoc', 'link_hinh_anh', 'link_hssp', 'han_dung_visa', 'han_dung_gmp']));

        // 👉 Cập nhật liên kết danh mục
        if (!empty($this->selectedCategories)) {
            $medicine->categories()->sync($this->selectedCategories);
        } else {
            // Nếu không chọn danh mục nào thì bỏ hết liên kết cũ
            $medicine->categories()->detach();
        }

        // Thông báo thành công
        session()->flash('success', 'Đã lưu thông tin thuốc thành công!');

        // Reset form
        $this->resetForm();
        $this->showForm = false;

        // Đổi header
        $this->dispatch('setHeader', 'Danh sách thuốc');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('setHeader', 'Danh sách thuốc');
    }

    public function delete($id)
    {
        $m = Medicine::findOrFail($id);
        if ($m->link_hinh_anh) {
            Storage::disk('public')->delete($m->link_hinh_anh);
        }
        $m->delete();
        session()->flash('success', "Đã xoá {$m->ten_biet_duoc} thành công.");
    }

    private function resetForm()
    {
        $this->reset(['medicineId', 'ten_biet_duoc', 'ten_hoat_chat', 'dang_bao_che', 'duong_dung', 'nong_do_ham_luong', 'don_vi_tinh', 'quy_cach_dong_goi', 'giay_phep_luu_hanh', 'han_dung', 'co_so_san_xuat', 'nuoc_san_xuat', 'gia_ke_khai', 'don_gia', 'gia_von', 'trang_thai_trung_thau', 'nha_phan_phoi', 'nhom_thuoc', 'link_hinh_anh', 'selectedCategories', 'image', 'link_hssp', 'han_dung_visa', 'han_dung_gmp']);
    }

    public function removeImage()
    {
        if ($this->link_hinh_anh) {
            Storage::disk('public')->delete($this->link_hinh_anh);
        }
        $this->link_hinh_anh = null;
        $this->image = null; // reset file upload Livewire
        $this->dispatch('image-removed', ['tab' => $this->activeTab]);
    }

    public function clearSearch()
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function updatedSelectAll($val)
    {
        $query = Medicine::when($this->search, fn($q) => $q->where('ten_biet_duoc', 'like', "%$this->search%")->orWhere('ten_hoat_chat', 'like', "%$this->search%"))->orderBy($this->sortField, $this->sortDirection);

        if ($val) {
            // Lấy ID của trang hiện tại
            $medicines = $this->perPage === 'all' ? $query->get() : $query->paginate($this->perPage);
            $this->selectedProducts = $medicines->pluck('id')->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function updatedSelectedProducts()
    {
        $query = Medicine::when($this->search, fn($q) => $q->where('ten_biet_duoc', 'like', "%$this->search%")->orWhere('ten_hoat_chat', 'like', "%$this->search%"));

        $medicinesOnPage = $this->perPage === 'all' ? $query->get() : $query->paginate($this->perPage);

        $this->selectAll = count($this->selectedProducts) === $medicinesOnPage->count();
    }

    public function deleteSelected()
    {
        if ($this->selectedProducts) {
            $items = Medicine::whereIn('id', $this->selectedProducts)->get();
            foreach ($items as $m) {
                if ($m->link_hinh_anh) {
                    Storage::disk('public')->delete($m->link_hinh_anh);
                }
            }
            Medicine::whereIn('id', $this->selectedProducts)->delete();
            $this->selectedProducts = [];
            $this->selectAll = false;
            session()->flash('message', 'Đã xóa các thuốc đã chọn.');
        }
    }

    public function duplicate($id)
    {
        $m = Medicine::with('categories')->findOrFail($id);
        $new = $m->replicate();
        $new->ten_biet_duoc .= ' (Bản sao)';
        $new->save();
        $new->categories()->sync($m->categories->pluck('id')->toArray());
        session()->flash('success', "Đã nhân bản thuốc '{$m->ten_biet_duoc}' thành công.");
    }

    public function applySelectedCategory()
    {
        if (empty($this->selectedProducts)) {
            session()->flash('message', 'Vui lòng chọn thuốc.');
            return;
        }

        if (empty($this->selectedCategories)) {
            session()->flash('message', 'Vui lòng chọn danh mục.');
            return;
        }

        foreach ($this->selectedProducts as $id) {
            $medicine = Medicine::find($id);
            if ($medicine) {
                // Thêm danh mục mà không bỏ các danh mục cũ
                $medicine->categories()->syncWithoutDetaching($this->selectedCategories);
            }
        }

        // Reset selected products / categories nếu muốn
        $this->selectedProducts = [];
        $this->selectedCategories = [];

        session()->flash('success', 'Cập nhật danh mục thành công!');
    }

    public function exportJson()
    {
        $categories = Category::select('id', 'name', 'slug', 'type', 'is_active')->get();
        $query = Medicine::with('categories');
        if ($this->selectedProducts) {
            $query->whereIn('id', $this->selectedProducts);
        }
        $medicines = $query->get();
        $data = [
            'categories' => $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug, 'type' => $c->type, 'is_active' => (bool) ($c->is_active ?? false)])->toArray(),
            'medicines' => $medicines->map(fn($m) => ['id' => $m->id, 'ten_biet_duoc' => $m->ten_biet_duoc, 'ten_hoat_chat' => $m->ten_hoat_chat, 'don_gia' => $m->don_gia, 'gia_ke_khai' => $m->gia_ke_khai, 'link_hinh_anh' => $m->link_hinh_anh ?? 'images/default.jpg', 'categories' => $m->categories->pluck('id')->toArray()])->toArray(),
        ];
        $filename = 'medicines_export_' . now()->format('Ymd_His') . '.json';
        return response()->streamDownload(fn() => print json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $filename, ['Content-Type' => 'application/json']);
    }
    public function updatedActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage($val)
    {
        session(['medicines_per_page' => $val]);
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
    public function exportSelectedToExcel()
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('notify', 'Vui lòng chọn ít nhất một sản phẩm để xuất Excel.');
            return;
        }

        $fileName = 'Danh_sach_thuoc_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new MedicinesExport($this->selectedProducts), $fileName);
    }
  
    public function exportWithTemplate(array $options = [])
    {
        if (empty($this->selectedProducts)) {
            $this->dispatch('notify', 'Vui lòng chọn ít nhất một sản phẩm để xuất Excel.');
            return;
        }
    
        // ----- 1️⃣ Mặc định các tham số -----
        $defaultColumns = [
            ['field' => 'stt_tt20_2022', 'title' => 'STT TT20/2022'],
            ['field' => 'phan_nhom_tt15', 'title' => 'Phân nhóm TT15'],
            ['field' => 'ten_hoat_chat', 'title' => 'Tên hoạt chất', 'align' => 'left'],
            ['field' => 'nong_do_ham_luong', 'title' => 'Nồng độ / Hàm lượng'],
            ['field' => 'ten_biet_duoc', 'title' => 'Tên biệt dược', 'align' => 'left'],
            ['field' => 'dang_bao_che', 'title' => 'Dạng bào chế'],
            ['field' => 'don_vi_tinh', 'title' => 'Đơn vị tính'],
            ['field' => 'quy_cach_dong_goi', 'title' => 'Quy cách đóng gói'],
            ['field' => 'giay_phep_luu_hanh', 'title' => 'Số GPLH'],
            ['field' => 'han_dung', 'title' => 'Hạn dùng'],
            ['field' => 'co_so_san_xuat', 'title' => 'Cơ sở sản xuất', 'align' => 'left'],
            ['field' => 'don_gia', 'title' => 'Đơn giá', 'type' => 'numeric'],
            ['field' => 'gia_ke_khai', 'title' => 'Giá kê khai', 'type' => 'numeric'],
        ];
    
        $defaults = [
            'templatePath' => database_path('exports/MAU-BANG-BAO-GIA.xlsx'),
            'sheetName'    => 'Sheet1',
            'startRow'     => 10,
            'columns'      => $defaultColumns,
            'auto_width'   => false,
            'auto_height'  => true,
            'fit_to_page'  => true,
            'row_font'     => ['name' => 'Times New Roman', 'size' => 12],
            'titles'       => [
                ['cell' => 'A1', 'text' => 'BẢNG BÁO GIÁ', 'style' => ['bold' => true, 'size' => 16, 'align' => 'center'], 'merge' => 'A1:F1'],
                ['cell' => 'L12', 'text' => 'TP.HCM, ngày ' . now()->day . ' tháng ' . now()->month . ' năm ' . now()->year, 'style' => ['align' => 'right']]
            ],
        ];
    
        // Merge options người dùng truyền vào
        $options = array_merge($defaults, $options);
    
        // ----- 2️⃣ Lấy dữ liệu từ DB -----
        $fields = array_column($options['columns'], 'field');
        $data = \App\Models\Medicine::whereIn('id', $this->selectedProducts)
            ->get($fields);
    
        $options['data'] = $data;
    
        // ----- 3️⃣ Gọi exportTemplate -----
        return $this->exportTemplate($options);
    }
    

}
