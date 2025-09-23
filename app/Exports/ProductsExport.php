<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $columns = [
        'title',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'image',
        'gallery',
        'tags',
    ];

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->select($this->columns)->get();
    }

    public function headings(): array
    {
        return [
            'Tên sản phẩm',
            'Slug',
            'Mô tả ngắn',
            'Mô tả',
            'Giá gốc',
            'Giá sale',
            'Ảnh',
            'Gallery',
            'Tags',
        ];
    }
}
