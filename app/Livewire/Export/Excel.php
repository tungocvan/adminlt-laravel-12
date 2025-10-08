<?php

namespace App\Livewire\Export;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TnvHelper;

class Excel extends Component
{
    public $model;
    public $title = 'BÁO CÁO DỮ LIỆU';
    public $footer = 'Người lập bảng';
    public $fields = [];
    public $selectedIds = []; // tùy chọn nếu bạn muốn export theo ID
    public bool $selectAll = true;
    public $showModal = false;

    public function mount($model)
    {
        if (!class_exists($model)) {
            $this->addError('model', 'Model không tồn tại');
            return;
        }

        $this->model = $model;
        $table = (new $model)->getTable();
        $columns = Schema::getColumnListing($table);

        $this->fields = collect($columns)->map(fn($col) => [
            'name' => $col,
            'selected' => true,
            'label' => ucfirst(str_replace('_', ' ', $col)),
        ])->toArray();
    }

    public function toggleField($index)
    {
        $this->fields[$index]['selected'] = !$this->fields[$index]['selected'];
    }

    public function export()
    {
        
        $selected = collect($this->fields)
            ->where('selected', true)
            ->mapWithKeys(fn($f) => [$f['name'] => $f['label']])
            ->toArray();

        if (empty($selected)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Vui lòng chọn ít nhất 1 cột để xuất!'
            ]);
            return;
        }

        try {
            $result = TnvHelper::exportToExcel(
                modelClass: $this->model,
                ids: $this->selectedIds,
                fields: $selected,
                title: $this->title,
                footer: $this->footer
            );
           // dd($result);
            if (!$result['status']) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => $result['message'] ?? 'Lỗi không xác định!'
                ]);
                return;
            }

            $this->dispatch('exported', [
                'type' => 'success',
                'message' => "Xuất thành công {$result['count']} dòng!"
            ]);
           $this->closeModal();
            // Trả file tải về
            return response()->download($result['path']);
        } catch (\Throwable $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Lỗi khi xuất file: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;

        foreach ($this->fields as &$field) {
            $field['selected'] = $this->selectAll;
        }
    }

    public function render()
    {
        return view('livewire.export.excel');
    }
    public function openModal()
    {
        // $this->reset(['name', 'email', 'password']);
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
    }

   
}
