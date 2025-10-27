<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MedicinesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return Medicine::whereIn('id', $this->ids)
            ->select('id', 'ten_biet_duoc', 'quy_cach_dong_goi', 'gia_ke_khai', 'nuoc_san_xuat', 'nha_phan_phoi')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên biệt dược',
            'Quy cách đóng gói',
            'Giá kê khai',
            'Nước sản xuất',
            'Nhà phân phối',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Định dạng hàng tiêu đề
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '808080']
                ]
            ]
        ]);

        // Toàn bảng viền mảnh + font Arial
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:F{$highestRow}")->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 11,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        return [];
    }
}
