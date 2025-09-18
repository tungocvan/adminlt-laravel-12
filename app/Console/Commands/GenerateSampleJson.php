<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSampleJson extends Command
{
    protected $signature = 'sample:json {--count=10}';
    protected $description = 'Generate sample JSON data for categories + products + pivot';

    public function handle()
    {
        $count = (int) $this->option('count');

        // Categories mẫu
        $categories = [
            [
                'id' => 1,
                'name' => 'Điện thoại',
                'slug' => 'dien-thoai',
                'type' => 'category',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Laptop',
                'slug' => 'laptop',
                'type' => 'category',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Phụ kiện',
                'slug' => 'phu-kien',
                'type' => 'category',
                'is_active' => true,
            ],
        ];

        // Sản phẩm random
        $products = [];
        for ($i = 1; $i <= $count; $i++) {
            $title = "Sản phẩm demo {$i}";
            $slug = Str::slug($title) . "-{$i}";

            $products[] = [
                'id' => $i,
                'title' => $title,
                'slug' => $slug,
                'short_description' => "Mô tả ngắn gọn cho {$title}",
                'description' => "<p>Mô tả chi tiết cho {$title}</p>",
                'regular_price' => rand(1000000, 5000000),
                'sale_price' => rand(500000, 4000000),
                'image' => "images/default.jpg", // ảnh cố định
                'gallery' => [
                    "images/gallery1.jpg",
                    "images/gallery2.jpg"
                ], // ảnh cố định
                'tags' => ["tag1", "tag2", "tag3"],
                'categories' => [rand(1, 3)], // random 1 category
            ];
        }

        // Kết quả JSON
        $data = [
            'categories' => $categories,
            'products'   => $products,
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Lưu ra file
        $path = base_path("database/seeders/data/sample_data.json");
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, $json);

        $this->info("✅ Sample JSON generated: {$path}");
    }
}
