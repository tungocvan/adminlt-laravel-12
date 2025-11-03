<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateRoutes extends Command
{
    /**
     * Tên và cú pháp của lệnh.
     */
    protected $signature = 'create:routes {name : Tên module}';

    /**
     * Mô tả lệnh.
     */
    protected $description = 'Tạo file routes web.php và api.php cho module';

    /**
     * Thực thi lệnh.
     */
    public function handle(): void
    {
        $name = ucfirst($this->argument('name'));
        $modulePath = base_path("Modules/{$name}/routes");

        // Kiểm tra thư mục routes
        if (!File::exists($modulePath)) {
            File::makeDirectory($modulePath, 0755, true);
            $this->info("📁 Đã tạo thư mục: {$modulePath}");
        }

        // Danh sách routes cần tạo
        $routes = [
            'web' => 'routes-web.txt',
            'api' => 'routes-api.txt',
        ];

        foreach ($routes as $type => $templateFile) {
            $templatePath = app_path("Console/Commands/template/{$templateFile}");
            $targetPath = "{$modulePath}/{$type}.php";

            if (!File::exists($templatePath)) {
                $this->error("⚠️  Không tìm thấy template: {$templatePath}");
                continue;
            }

            // Đọc và thay thế nội dung template
            $content = str_replace(
                ['{Module}', '{module}'],
                [$name, strtolower($name)],
                File::get($templatePath)
            );

            // Ghi nội dung vào file
            File::put($targetPath, $content);

            $this->info("✅ Đã tạo file routes {$type}.php cho module {$name}");
        }

        $this->newLine();
        $this->info("🎉 Hoàn tất tạo routes cho module: {$name}");
    }
}
