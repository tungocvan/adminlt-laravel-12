<?php

namespace App\Livewire;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class SearchStudent extends Component
{
    public $search;
    public $results = [];

    public function updatedSearch()
    {
        dd('1');
        $this->searchStudent();
    }
    public function searchStudent()
    {
        
        if (strlen($this->search) < 2) {
            $this->results = [];
            return;
        }
    
        $filePath = storage_path('app/public/danh_sach_hoc_sinh_tieu_hoc.xlsx');
        
        if (!file_exists($filePath)) {
            $this->results = [];
            session()->flash('error', 'Không tìm thấy file Excel');
            return;
        }
    
        $sheets = Excel::toArray([], $filePath);
        $data = $sheets[0] ?? [];
    
        // Nếu có tiêu đề thì bỏ
        if (isset($data[0]) && $data[0][0] === 'Mã định danh') {
            array_shift($data);
        }
    
        $this->results = array_filter($data, function ($row) {
            $maDinhDanh = $row[0] ?? '';
            $hoTen = $row[1] ?? '';
    
            return stripos($maDinhDanh, $this->search) !== false ||
                   stripos($hoTen, $this->search) !== false;
        });
    }
    

    public function render()
        {
            return view('livewire.search-student')
                ->layout('layouts.ntd', ['title' => 'Tìm kiếm học sinh']);
}

}
