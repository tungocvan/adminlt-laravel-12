<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GdtInvoicesExport implements FromGenerator, WithHeadings
{
    protected $rows;

    public function __construct(\Generator $rows)
    {
        $this->rows = $rows;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã tra cứu',
            'Ký hiệu',
            'Số HĐ',
            'Loại',
            'Ngày lập',
            'MST Người mua',
            'Người mua',
            'Email Người mua',
            'Người bán',
            'Thuế suất',
            'VAT',
            'Trước VAT',
            'Thành tiền',
        ];
    }

    public function generator(): \Generator
    {
        yield from $this->rows;
    }
}
