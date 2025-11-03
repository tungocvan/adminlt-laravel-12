<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateController extends Command
{
    /**
     * Cú pháp lệnh: php artisan create:controller {name} {module}
     */
    protected $signature = 'create:controller {name : Tên controller (không có "Controller")} {module : Tên module}';

    /**
     * Mô tả lệnh.
     */
    protected $description = 'Tạo controller (web + api) và view mặc định cho module';

    /**
     * Thực thi lệnh.
     */
    public function handle(): void
    {
        $name = ucfirst($this->argument('name'));
        $module = ucfirst($this->argument('module'));

        $basePath = base_path("Modules/{$module}");
        $controllerPath = "{$basePath}/Http/Controllers";
        $apiControllerPath = "{$controllerPath}/Api";
        $viewsPath = "{$basePath}/resources/views/" . strtolower($name) . ".blade.php";

        // 🧩 Kiểm tra module tồn tại
        if (!File::exists($basePath)) {
            $this->error("⚠️  Module {$module} không tồn tại!");
            return;
        }

        // 🧩 Đảm bảo thư mục controller tồn tại
        File::ensureDirectoryExists($controllerPath);
        File::ensureDirectoryExists($apiControllerPath);

        // 🧩 Tạo controller Web
        $this->createControllerFromTemplate(
            template: app_path('Console/Commands/template/controller.txt'),
            outputPath: "{$controllerPath}/{$name}Controller.php",
            name: $name,
            module: $module,
            type: 'Web'
        );

        // 🧩 Tạo controller API
        $this->createControllerFromTemplate(
            template: app_path('Console/Commands/template/controller-api.txt'),
            outputPath: "{$apiControllerPath}/{$name}Controller.php",
            name: $name,
            module: $module,
            type: 'API'
        );

        // 🧩 Tạo view mặc định
        $this->createView($viewsPath);

        $this->newLine();
        $this->info("🎉 Hoàn tất tạo controller và view cho module {$module}!");
    }

    /**
     * Hàm tạo controller từ template.
     */
    protected function createControllerFromTemplate(string $template, string $outputPath, string $name, string $module, string $type): void
    {
        if (!File::exists($template)) {
            $this->warn("⚠️  Không tìm thấy template cho {$type} Controller: {$template}");
            return;
        }

        if (File::exists($outputPath)) {
            $this->warn("⏩ {$type} Controller {$name} đã tồn tại, bỏ qua.");
            return;
        }

        $content = str_replace(
            ['{Module}', '{module}'],
            [$module, strtolower($name)],
            File::get($template)
        );

        File::put($outputPath, $content);
        $this->info("✅ Đã tạo {$type} Controller: {$outputPath}");
    }

    /**
     * Hàm tạo view mặc định.
     */
    protected function createView(string $viewPath): void
    {
        if (File::exists($viewPath)) {
            $this->line("📝 View đã tồn tại: {$viewPath}");
            return;
        }

        $templateView = app_path('Console/Commands/template/views.txt');

        if (!File::exists($templateView)) {
            $this->warn("⚠️  Không tìm thấy template view: {$templateView}");
            return;
        }

        File::ensureDirectoryExists(dirname($viewPath));
        File::put($viewPath, File::get($templateView));

        $this->info("📄 Đã tạo view mặc định: {$viewPath}");
    }
}
