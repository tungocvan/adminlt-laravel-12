<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\WpProduct;

class ImportSampleJson extends Command
{
    protected $signature = 'sample:import 
                            {path : Đường dẫn tới file JSON} 
                            {--truncate : Xoá dữ liệu cũ trước khi import} 
                            {--update : Update nếu slug đã tồn tại thay vì tạo mới}';

    protected $description = 'Import dữ liệu mẫu (categories + wp_products + category_product) từ JSON';

    public function handle()
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("❌ File không tồn tại: {$path}");
            return Command::FAILURE;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data) {
            $this->error("❌ Không đọc được dữ liệu JSON!");
            return Command::FAILURE;
        }

        // Nếu truncate thì xử lý riêng ngoài transaction
        if ($this->option('truncate')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('category_product')->truncate();
            Category::truncate();
            WpProduct::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info("🧹 Đã xoá dữ liệu cũ trong database.");
        }

        // Chạy import trong transaction
        DB::transaction(function () use ($data) {
            $categoryMap = [];
            $createdCats = $updatedCats = [];
            $createdProds = $updatedProds = [];

            // Insert / Update categories
            foreach ($data['categories'] as $cat) {
                if ($this->option('update')) {
                    $category = Category::updateOrCreate(
                        ['slug' => $cat['slug']],
                        [
                            'name'        => $cat['name'],
                            'type'        => $cat['type'] ?? 'category',
                            'is_active'   => $cat['is_active'] ?? true,
                            'parent_id'   => $cat['parent_id'] ?? null,
                            'description' => $cat['description'] ?? null,
                        ]
                    );
                    if ($category->wasRecentlyCreated) {
                        $createdCats[] = $category->slug;
                    } else {
                        $updatedCats[] = $category->slug;
                    }
                } else {
                    $category = Category::create([
                        'name'        => $cat['name'],
                        'slug'        => $cat['slug'] ?? null,
                        'type'        => $cat['type'] ?? 'category',
                        'is_active'   => $cat['is_active'] ?? true,
                        'parent_id'   => $cat['parent_id'] ?? null,
                        'description' => $cat['description'] ?? null,
                    ]);
                    $createdCats[] = $category->slug;
                }

                $categoryMap[$cat['id']] = $category->id;
            }

            // Insert / Update products
            foreach ($data['products'] as $prod) {
                if ($this->option('update')) {
                    $product = WpProduct::updateOrCreate(
                        ['slug' => $prod['slug']],
                        [
                            'title'             => $prod['title'],
                            'short_description' => $prod['short_description'] ?? null,
                            'description'       => $prod['description'] ?? null,
                            'regular_price'     => $prod['regular_price'] ?? null,
                            'sale_price'        => $prod['sale_price'] ?? null,
                            'image'             => $prod['image'] ?? null,
                            'gallery'           => $prod['gallery'] ?? [],
                            'tags'              => $prod['tags'] ?? [],
                        ]
                    );
                    if ($product->wasRecentlyCreated) {
                        $createdProds[] = $product->slug;
                    } else {
                        $updatedProds[] = $product->slug;
                    }
                } else {
                    $product = WpProduct::create([
                        'title'             => $prod['title'],
                        'slug'              => $prod['slug'],
                        'short_description' => $prod['short_description'] ?? null,
                        'description'       => $prod['description'] ?? null,
                        'regular_price'     => $prod['regular_price'] ?? null,
                        'sale_price'        => $prod['sale_price'] ?? null,
                        'image'             => $prod['image'] ?? null,
                        'gallery'           => $prod['gallery'] ?? [],
                        'tags'              => $prod['tags'] ?? [],
                    ]);
                    $createdProds[] = $product->slug;
                }

                // Gắn category (pivot)
                if (!empty($prod['categories'])) {
                    $pivotIds = [];
                    foreach ($prod['categories'] as $oldCatId) {
                        if (isset($categoryMap[$oldCatId])) {
                            $pivotIds[] = $categoryMap[$oldCatId];
                        }
                    }
                    $product->categories()->sync($pivotIds);
                }
            }

            // Log kết quả
            if ($createdCats) {
                $this->info("➕ Categories created: " . implode(', ', $createdCats));
            }
            if ($updatedCats) {
                $this->info("✏️  Categories updated: " . implode(', ', $updatedCats));
            }
            if ($createdProds) {
                $this->info("➕ Products created: " . implode(', ', $createdProds));
            }
            if ($updatedProds) {
                $this->info("✏️  Products updated: " . implode(', ', $updatedProds));
            }
        });

        $this->info("✅ Import dữ liệu từ file JSON thành công: {$path}");
        return Command::SUCCESS;
    }
}
