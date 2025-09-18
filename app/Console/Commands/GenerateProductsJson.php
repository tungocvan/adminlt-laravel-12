<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateProductsJson extends Command
{
    protected $signature = 'products:generate-json 
        {count=10 : Number of products to generate} 
        {--path=database/data/products.json : Output file path}';

    protected $description = 'Generate sample products JSON file';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $path = $this->option('path');

        $products = [];

        for ($i = 1; $i <= $count; $i++) {
            $title = "Sản phẩm demo {$i}";
            $slug = Str::slug($title);

            $products[] = [
                'title'             => $title,
                'slug'              => $slug,
                'short_description' => "Mô tả ngắn cho sản phẩm {$i}",
                'description'       => "<p>Đây là phần mô tả chi tiết cho sản phẩm {$i}.</p>",
                'regular_price'     => rand(100000, 500000),
                'sale_price'        => rand(50000, 400000),
                'category_id'       => rand(1, 5), // giả sử có 5 category
                'image'             => "products/default.jpg", 
                'gallery'           => [
                    "products/default-1.jpg",
                    "products/default-2.jpg"
                ],
                'tags'              => ["tag{$i}", "demo", "sample"]
            ];
        }

        // Tạo thư mục nếu chưa có
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($path, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ Generated {$count} products into {$path}");
        return Command::SUCCESS;
    }
}
