<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BangGiaThuocImport implements ToCollection, WithStartRow
{
    public $rows = [];

    public function collection(Collection $collection)
    {
        // Chỉ lấy dữ liệu text, không load style/logo
        $this->rows = array_merge($this->rows, $collection->toArray());
    }

    public function startRow(): int
    {
        return 10; // dữ liệu bắt đầu từ dòng 10
    }
}
