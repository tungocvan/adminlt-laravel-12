<?php

namespace App\Imports;

use App\Models\WpProduct;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WpProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Nếu không có title thì bỏ qua
        if (empty($row['title'])) {
            return null;
        }

        $slug = $row['slug'] ?? Str::slug($row['title']);

        // Kiểm tra trùng title hoặc slug
        $exists = WpProduct::where('title', $row['title'])
            ->orWhere('slug', $slug)
            ->exists();

        if ($exists) {
            return null; // bỏ qua, không import
        }

        return new WpProduct([
            'title'             => $row['title'],
            'slug'              => $slug,
            'short_description' => $row['short_description'] ?? null,
            'description'       => $row['description'] ?? null,
            'regular_price'     => $row['regular_price'] ?? 0,
            'sale_price'        => $row['sale_price'] ?? 0,
            'image'             => $row['image'] ?? null,
            'gallery'           => isset($row['gallery']) ? explode(',', $row['gallery']) : [],
            'tags'              => isset($row['tags']) ? explode(';', $row['tags']) : [],
        ]);
    }
}
