<?php

namespace Modules\Invoices\Exports;

use Modules\Invoices\Models\Invoices;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesSelectedExport implements FromCollection, WithHeadings
{
    public array $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return Invoices::whereIn('id', $this->ids)
            ->select([
                'id',
                'lookup_code',
                'symbol',
                'invoice_number',
                'issued_date',
                'name',
                'tax_code',
                'tax_rate',
                'total_amount',
                'vat_amount'
            ])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Mã tra cứu',
            'Ký hiệu',
            'Số HĐ',
            'Ngày lập',
            'Tên',
            'MST',
            'Thuế suất',
            'Thành tiền',
            'VAT'
        ];
    }
}
