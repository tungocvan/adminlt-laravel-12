<?php

namespace App\Livewire;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentLookup extends Component
{
    public string $keyword = '';
    public ?array $student = null;
    public bool $searched = false;

    protected $rules = [
        'keyword' => 'required|string|min:4',
    ];

    public function search()
    {
        $this->validate();
        $this->searched = true;
        $this->student = null;

        $import = new StudentsImport();
        // Đọc file Excel từ storage. Đổi tên file nếu bạn dùng tên khác
        $filePath = storage_path('app/public/dsk1.xlsx');

        if (!file_exists($filePath)) {
            $this->addError('keyword', 'Không tìm thấy file dữ liệu: dsk1.xlsx trong storage/app/public');
            return;
        }

        Excel::import($import, $filePath);

        // So khớp theo mã định danh (cột F / alias: ma_dinh_danh_hoc_sinh)
        $needle = $this->normalize($this->keyword);
        $row = $import->students->first(function ($r) use ($needle) {
            $code = $this->normalize($r['ma_dinh_danh'] ?? '');
            return $code === $needle;
        });

        if ($row) {
            $this->student = $row;
        }
    }

    private function normalize($s): string
    {
        return preg_replace('~\s+~', '', mb_strtolower((string)$s));
    }

    public function render()
    {
        return view('livewire.student-lookup');
    }
}