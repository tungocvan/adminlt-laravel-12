<?php

namespace Modules\Medicine\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicineStock;
use App\Models\Medicine;
use Carbon\Carbon;

class MedicinesStock extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $perPageOptions = [10, 50, 100];
    public $selectAll = false;
    public $selectedStocks = [];

    public $editingStockId = null;
    public $medicine_id;
    public $so_lo;
    public $han_dung;
    public $so_luong;
    public $gia_von;

    public $status = 'available';
    public $location;
    public $notes;

    protected $paginationTheme = 'bootstrap';

    public $statusOptions = [
        'available' => 'Còn hàng',
        'low-stock' => 'Sắp hết hàng',
        'out-of-stock' => 'Hết hàng',
        'expired' => 'Hết hạn',
    ];

    protected $rules = [
        'medicine_id' => 'required|integer|exists:medicines,id',
        'so_lo' => 'required|string|max:50',
        'han_dung' => 'required|date',
        'so_luong' => 'required|numeric|min:0',
        'gia_von' => 'required|numeric|min:0',
        'status' => 'required|in:available,low-stock,out-of-stock,expired',
        'location' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:500',
    ];

    protected $listeners = ['reset-form' => 'resetForm'];

    // ---------------- Computed Properties ----------------
    public function getStocksProperty()
    {
        return MedicineStock::with('medicine')
            ->where(function ($q) {
                $q->whereHas('medicine', fn($mq) => $mq->where('ten_biet_duoc', 'like', "%{$this->search}%")->orWhere('ten_hoat_chat', 'like', "%{$this->search}%"))
                    ->orWhere('so_lo', 'like', "%{$this->search}%")
                    ->orWhere('location', 'like', "%{$this->search}%");
            })
            ->orderBy('han_dung', 'asc')->orderBy('id', 'asc')
            ->paginate($this->perPage)
            ->withPath(route('medicine.stock')); // 🔹 Fix link phân trang
    }

    // ---------------- Pagination / Search ----------------
    public function updatingPage()
    {
        $this->selectAll = false;
        $this->selectedStocks = [];
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selectedStocks = [];
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        $this->selectAll = false; // Reset select all
        $this->selectedStocks = [];
    }

    // ---------------- Multi-Select ----------------
    public function updatedSelectAll($value)
    {
        $this->selectedStocks = $value ? $this->stocks->pluck('id')->toArray() : [];
    }

    // ---------------- Modal / Form ----------------
    public function openCreateForm()
    {
        $this->resetForm();
        $this->editingStockId = null;
        $this->dispatch('show-modal-medicine');
    }

    public function openEditForm($id)
    {
        $stock = MedicineStock::findOrFail($id);
        $this->editingStockId = $id;
        $this->medicine_id = $stock->medicine_id;
        $this->so_lo = $stock->so_lo;
        $this->han_dung = Carbon::parse($stock->han_dung)->format('Y-m-d');
        $this->so_luong = $stock->so_luong;
        $this->gia_von = $stock->gia_von;
        $this->status = $stock->status;
        $this->location = $stock->location;
        $this->notes = $stock->notes;

        $this->dispatch('show-modal-medicine');
    }

    public function saveStock()
    {
       // $this->validate();

        $service = app(\App\Services\OrderStockService::class);

        $data = [
            'medicine_id' => $this->medicine_id,
            'so_lo' => $this->so_lo,
            'han_dung' => $this->han_dung,
            'so_luong' => $this->so_luong,
            'gia_von' => $this->gia_von,
        ];
     
        try {
            // service xử lý thêm mới + update + giá vốn
            $stock = $service->addOrUpdateStock($data);

            // cập nhật thêm dữ liệu trong component
            $stock->update([
                'status' => $this->status,
                'location' => $this->location,
                'notes' => $this->notes,
            ]);

            session()->flash('message', $this->editingStockId ? 'Cập nhật tồn kho thành công!' : 'Thêm tồn kho thành công!');

            $this->resetForm();
            $this->dispatch('close-modal-medicine');
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function deleteStock($id)
    {
        MedicineStock::findOrFail($id)->delete();
        session()->flash('message', 'Xóa lô thuốc thành công!');
        $this->selectedStocks = array_diff($this->selectedStocks, [$id]);
    }

    public function deleteSelected()
    {
        if (!empty($this->selectedStocks)) {
            MedicineStock::whereIn('id', $this->selectedStocks)->delete();
        }
        session()->flash('message', 'Xóa các lô thuốc được chọn thành công!');
        $this->selectAll = false;
        $this->selectedStocks = [];
    }

    public function resetForm()
    {
        $this->reset(['editingStockId', 'medicine_id', 'so_lo', 'han_dung', 'so_luong', 'gia_von', 'status', 'location', 'notes']);
    }

    public function closeModal()
    {
        $this->dispatch('close-modal-medicine');
        $this->resetForm();
    }

    // ---------------- Render ----------------
    public function render()
    {
        $medicines = Medicine::orderBy('ten_biet_duoc')->get();
        return view('Medicine::livewire.medicines-stock', [
            'stocks' => $this->stocks,
            'medicines' => $medicines,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
