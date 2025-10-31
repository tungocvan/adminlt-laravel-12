<?php

namespace App\Livewire\Banggia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BangBaoGia;
use App\Models\Medicine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BangGia extends Component
{
    use WithPagination;

    // 🔹 State chung
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // 🔹 Form
    public $formVisible = false;
    public $editingId = null;
    public $ten_khach_hang = 'QUÝ KHÁCH HÀNG';
    public $ghi_chu;
    public $file_path;
    public $tieu_de_bg = 'BẢNG BÁO GIÁ';
    public $nguoi_duyet_bg = 'Giám đốc Công ty';
    public $ngay_lap_bg;

    // 🔹 Chọn thuốc / dòng
    public $selectedMedicines = [];
    public $selectAll = false;
    public $selectedRows = [];
    public $selectAllRows = false;

    protected $rules = [
        'ten_khach_hang' => 'required|string|max:255',
        'ghi_chu' => 'nullable|string',
    ];

    // =====================================================
    // 🔸 Lifecycle
    // =====================================================

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc')
            ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    // =====================================================
    // 🔸 CRUD chính
    // =====================================================

    public function toggleForm($id = null)
    {
        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $this->editingId = $id;
            $record = BangBaoGia::findOrFail($id);
            $this->fill($record->only(['ten_khach_hang', 'ghi_chu', 'file_path']));
            $this->selectedMedicines = $record->product_ids ?? [];
        }

        $this->formVisible = !$this->formVisible;
    }

    public function save()
    {
        try {
            $today = now()->format('Ymd');
            $countToday = BangBaoGia::whereDate('created_at', now())->count() + 1;
            $maSo = 'BBG_' . $today . '_' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            $date = $this->ngay_lap_bg
                ? 'TP.HCM, ngày ' . $this->ngay_lap_bg
                : 'TP.HCM, ngày ' . now()->day . ' tháng ' . now()->month . ' năm ' . now()->year;

            BangBaoGia::create([
                'ma_so'          => $maSo,
                'user_id'        => Auth::id(),
                'ten_khach_hang' => $this->ten_khach_hang,
                'ghi_chu'        => $this->ghi_chu,
                'product_ids'    => $this->selectedMedicines,
                'company'        => [
                    'title'       => $this->tieu_de_bg,
                    'date'        => $date,
                    'departments' => $this->nguoi_duyet_bg,
                ],
            ]);

            session()->flash('message', "✅ Bảng báo giá {$maSo} đã được tạo!");
            $this->resetForm();
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi lưu bảng báo giá', ['error' => $e]);
            session()->flash('error', '⚠️ Có lỗi xảy ra khi tạo bảng báo giá!');
        }
    }

    public function delete($id)
    {
        $record = BangBaoGia::find($id);

        if (!$record) {
            session()->flash('error', '⚠️ Không tìm thấy bảng báo giá.');
            return;
        }

        try {
            if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                Storage::disk('public')->delete($record->file_path);
            }
            $record->delete();
            session()->flash('message', '✅ Đã xóa bảng báo giá!');
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi xóa bảng báo giá', ['id' => $id, 'error' => $e]);
            session()->flash('error', '⚠️ Không thể xóa bảng báo giá.');
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selectedRows)) return;

        BangBaoGia::whereIn('id', $this->selectedRows)->delete();
        $this->selectedRows = [];
        $this->selectAllRows = false;

        session()->flash('message', '✅ Đã xóa các bản ghi đã chọn!');
    }

    // =====================================================
    // 🔸 Helper
    // =====================================================

    public function resetForm()
    {
        $this->editingId = null;
        $this->ten_khach_hang = 'QUÝ KHÁCH HÀNG';
        $this->ghi_chu = '';
        $this->file_path = '';
        $this->selectedMedicines = [];
        $this->selectAll = false;
        $this->formVisible = false;
    }

    // =====================================================
    // 🔸 Multi-select
    // =====================================================

    public function updatedSelectAllRows($value)
    {
        $this->selectedRows = $value
            ? $this->records()->pluck('id')->toArray()
            : [];
    }

    public function updatedSelectAll($value)
    {
        $this->selectedMedicines = $value
            ? Medicine::pluck('id')->toArray()
            : [];
    }

    // =====================================================
    // 🔸 Computed & Render
    // =====================================================

    public function getMedicinesProperty()
    {
        return Medicine::query()
            ->when($this->search, fn($q) => $q->where('ten_biet_duoc', 'like', "%{$this->search}%"))
            ->orderBy('ten_biet_duoc')
            ->get(['id', 'ten_biet_duoc', 'ten_hoat_chat', 'don_vi_tinh']);
    }

    public function records()
    {
        return BangBaoGia::query()
            ->when($this->search, fn($q) =>
                $q->where('ma_so', 'like', "%{$this->search}%")
                    ->orWhere('ten_khach_hang', 'like', "%{$this->search}%")
            )
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.banggia.bang-gia', [
            'records'   => $this->records()->paginate($this->perPage),
            'medicines' => $this->medicines,
        ]);
    }
}
