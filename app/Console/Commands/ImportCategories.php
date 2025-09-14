<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Category;

class ImportCategories extends Command
{
    protected $signature = 'categories:import 
                            {path : Đường dẫn file JSON chứa dữ liệu categories} 
                            {--truncate : Xóa toàn bộ dữ liệu categories trước khi import} 
                            {--update : Update dữ liệu nếu trùng slug thay vì tạo mới} 
                            {--dry-run : Chỉ hiển thị kết quả dự kiến mà không ghi dữ liệu vào DB}';

    protected $description = 'Import categories/menu từ file JSON vào database';

    protected int $createdCount = 0;
    protected int $updatedCount = 0;
    protected array $createdSlugs = [];
    protected array $updatedSlugs = [];
    protected bool $dryRun = false;

    public function handle(): int
    {
        $path = $this->argument('path');

        if (!File::exists($path)) {
            $this->error("File {$path} không tồn tại.");
            return Command::FAILURE;
        }

        $this->dryRun = $this->option('dry-run');

        if ($this->dryRun) {
            $this->warn("⚠️ Đang chạy ở chế độ Dry-run (không ghi dữ liệu vào DB).");
        } elseif ($this->option('truncate')) {
            $this->warn("Đang xóa toàn bộ dữ liệu trong bảng categories...");
            Category::truncate();
            $this->info("Đã xóa xong.");
        }

        $json = File::get($path);
        $items = json_decode($json, true);

        if ($items === null) {
            $this->error("File {$path} không đúng định dạng JSON.");
            return Command::FAILURE;
        }

        $this->info("Đang phân tích dữ liệu từ file: {$path}");

        $this->importCategories($items);

        $this->newLine();
        $this->info("✅ Hoàn tất xử lý categories/menu.");
        $this->line("📊 Thống kê:");
        $this->line("   - Tạo mới: {$this->createdCount}");
        if ($this->createdCount > 0) {
            $this->line("     • " . implode(', ', $this->createdSlugs));
        }

        $this->line("   - Cập nhật: {$this->updatedCount}");
        if ($this->updatedCount > 0) {
            $this->line("     • " . implode(', ', $this->updatedSlugs));
        }

        $this->line("   - Tổng cộng xử lý: " . ($this->createdCount + $this->updatedCount));

        return Command::SUCCESS;
    }

    protected function importCategories(array $items, $parentId = null): void
    {
        foreach ($items as $item) {
            $children = $item['children'] ?? [];
            unset($item['children']);

            $item['parent_id'] = $parentId;

            if ($this->option('update') && !empty($item['slug'])) {
                if ($this->dryRun) {
                    $exists = Category::where('slug', $item['slug'])->exists();
                    if ($exists) {
                        $this->updatedCount++;
                        $this->updatedSlugs[] = $item['slug'];
                    } else {
                        $this->createdCount++;
                        $this->createdSlugs[] = $item['slug'];
                    }
                } else {
                    $category = Category::updateOrCreate(
                        ['slug' => $item['slug']],
                        $item
                    );

                    if ($category->wasRecentlyCreated) {
                        $this->createdCount++;
                        $this->createdSlugs[] = $category->slug;
                    } else {
                        $this->updatedCount++;
                        $this->updatedSlugs[] = $category->slug;
                    }
                }
            } else {
                if ($this->dryRun) {
                    $this->createdCount++;
                    $this->createdSlugs[] = $item['slug'] ?? '(no-slug)';
                } else {
                    $category = Category::create($item);
                    $this->createdCount++;
                    $this->createdSlugs[] = $category->slug;
                }
            }

            if (!empty($children)) {
                $this->importCategories($children, $this->dryRun ? null : ($category->id ?? null));
            }
        }
    }
}
