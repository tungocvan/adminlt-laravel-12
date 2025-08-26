<?php

namespace App\Livewire\Ntd;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Hocsinh;

class HocsinhList extends Component
{
    use WithPagination;

    public $search = '';
    public $ho_va_ten, $lop, $ngay_sinh, $gioi_tinh, $ma_dinh_danh_hoc_sinh, $gvcn, $bao_mau;
    public $isEdit = false, $editId = null;
    public $deleteId;

    protected $rules = [
        'ho_va_ten' => 'required|string',
        'ma_dinh_danh_hoc_sinh' => 'required|string|unique:hocsinhs,ma_dinh_danh_hoc_sinh',
    ];

    public function render()
    {
        $hocsinhs = Hocsinh::where('ho_va_ten', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.ntd.hocsinh-list', compact('hocsinhs'));
    }

    public function create()
    {
        $this->resetInput();
        $this->isEdit = false;
        $this->dispatch('showModal');
    }

    public function edit($id)
    {
        $hs = Hocsinh::findOrFail($id);
        $this->editId = $hs->id;
        $this->ho_va_ten = $hs->ho_va_ten;
        $this->lop = $hs->lop;
        $this->ngay_sinh = $hs->ngay_sinh;
        $this->gioi_tinh = $hs->gioi_tinh;
        $this->ma_dinh_danh_hoc_sinh = $hs->ma_dinh_danh_hoc_sinh;
        $this->gvcn = $hs->gvcn;
        $this->bao_mau = $hs->bao_mau;

        $this->isEdit = true;
        $this->dispatch('showModal');
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->rules['ma_dinh_danh_hoc_sinh'] = 'required|string|unique:hocsinhs,ma_dinh_danh_hoc_sinh,' . $this->editId;
        }

        $this->validate();

        if ($this->isEdit) {
            $hs = Hocsinh::findOrFail($this->editId);
            $hs->update($this->only(['ho_va_ten','lop','ngay_sinh','gioi_tinh','ma_dinh_danh_hoc_sinh','gvcn','bao_mau']));
        } else {
            Hocsinh::create($this->only(['ho_va_ten','lop','ngay_sinh','gioi_tinh','ma_dinh_danh_hoc_sinh','gvcn','bao_mau']));
        }

        $this->dispatch('hideModal');
        $this->resetInput();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('showConfirm');
    }

    public function delete()
    {
        Hocsinh::destroy($this->deleteId);
        $this->dispatch('hideConfirm');
    }

    public function closeModal()
    {
        $this->dispatch('hideModal');
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->editId = null;
        $this->ho_va_ten = '';
        $this->lop = '';
        $this->ngay_sinh = '';
        $this->gioi_tinh = '';
        $this->ma_dinh_danh_hoc_sinh = '';
        $this->gvcn = '';
        $this->bao_mau = '';
    }
}
