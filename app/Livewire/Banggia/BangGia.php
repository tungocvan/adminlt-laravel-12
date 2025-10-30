<?php

namespace App\Livewire\Banggia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BangBaoGia;
use App\Models\Medicine;
use Illuminate\Support\Facades\Auth;

class BangGia extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public $formVisible = false;
    public $editingId = null;

    public $ten_khach_hang;
    public $ghi_chu;
    public $file_path;

    public $selectedMedicines = [];
    public $selectAll = false;


    protected $rules = [
        'ten_khach_hang' => 'required|string|max:255',
        'ghi_chu' => 'nullable|string',
    ];

    public function mount()
    {
        $this->records = BangBaoGia::latest()->get();
    }

    public function updatedSearch()
    {
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

    public function toggleForm($id = null)
    {
        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $this->editingId = $id;
            $record = BangBaoGia::findOrFail($id);
            $this->fill($record->only(['ten_khach_hang', 'ghi_chu', 'file_path']));
            $this->selectedMedicines = $record->product_ids ?? [];
            $this->formVisible = true;
        } else {
            $this->formVisible = !$this->formVisible;
        }
    }

    public function save()
    {
        try {
            // 🔹 1. Sinh mã số tự động dạng BBG_YYYYMMDD_XXX
            $today = now()->format('Ymd');
            $countToday = BangBaoGia::whereDate('created_at', now()->toDateString())->count() + 1;
            $maSo = 'BBG_' . $today . '_' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // 🔹 2. Tạo bản ghi (model sẽ tự xuất file Excel)
            $record = BangBaoGia::create([
                'ma_so'          => $maSo,
                'user_id'        => auth()->id(),
                'ten_khach_hang' => $this->ten_khach_hang,
                'ghi_chu'        => $this->ghi_chu,
                'product_ids'    => $this->selectedMedicines,
            ]);

            session()->flash('message', '✅ Bảng báo giá đã được tạo: ' . $maSo);

            $this->resetForm();
        } catch (\Throwable $e) {
            \Log::error('❌ Lỗi khi lưu bảng báo giá', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            session()->flash('error', '⚠️ Có lỗi xảy ra khi tạo bảng báo giá!');
        }
    }

    public function delete($id)
    {
        try {
            $record = BangBaoGia::find($id);
    
            if (!$record) {
                session()->flash('error', '⚠️ Không tìm thấy bảng báo giá.');
                return;
            }
    
            // 🔹 Xóa file nếu có
            if ($record->file_path && \Storage::disk('public')->exists($record->file_path)) {
                \Storage::disk('public')->delete($record->file_path);
                \Log::info('🗑️ Đã xóa file báo giá: ' . $record->file_path);
            }
    
            // 🔹 Xóa bản ghi trong DB
            $record->delete();
    
            session()->flash('message', '✅ Đã xóa bảng báo giá và file liên quan!');
        } catch (\Throwable $e) {
            \Log::error('❌ Lỗi khi xóa bảng báo giá', [
                'id' => $id,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
    
            session()->flash('error', '⚠️ Không thể xóa bảng báo giá, vui lòng thử lại.');
        }
    }
    

    public function resetForm()
    {
        $this->editingId = null;
        $this->ten_khach_hang = '';
        $this->ghi_chu = '';
        $this->file_path = '';
        $this->selectedMedicines = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        $this->selectedMedicines = $value ? Medicine::pluck('id')->toArray() : [];
    }

    public function updatedSelectedMedicines()
    {
        $this->selectAll = count($this->selectedMedicines) === $this->medicines->count();
    }

    public function getMedicinesProperty()
    {
        
        return Medicine::query()
            ->when($this->search, fn($q) => $q->where('ten_biet_duoc', 'like', "%{$this->search}%"))->get();
    }

    public function render()
    {
        $records = BangBaoGia::query()
            ->when($this->search, fn($q) =>
                $q->where('ma_so', 'like', "%{$this->search}%")
                    ->orWhere('ten_khach_hang', 'like', "%{$this->search}%")
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $medicines = Medicine::orderBy('ten_biet_duoc')->get(['id', 'ten_biet_duoc','ten_hoat_chat', 'don_vi_tinh']);

        return view('livewire.banggia.bang-gia', compact('records', 'medicines'));
    }
}
