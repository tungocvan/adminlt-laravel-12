<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateModuleComponent extends Command
{
    protected $signature = 'create:component
        {module? : Tên module (chữ cái đầu viết hoa, ví dụ: Components)}
        {component? : Tên component (ví dụ: TnvModal)}
        {--force : Ghi đè nếu file đã tồn tại}
        {--inline : Tạo component inline (không có file view)}';

    protected $description = 'Tạo Blade Component trong Module (Http/Components + resources/views/components)';

    public function handle()
    {
        $module = $this->argument('module');
        $component = $this->argument('component');
        $force = $this->option('force');
        $inline = $this->option('inline');

        // ⚠️ Nếu thiếu tham số → hiển thị hướng dẫn
        if (empty($module) || empty($component)) {
            $this->warn("⚠️  Thiếu tham số bắt buộc!");
            $this->info("\nCú pháp đúng:");
            $this->line("  php artisan create:component <TênModule> <TênComponent> [--force] [--inline]");
            $this->info("\nVí dụ:");
            $this->line("  php artisan create:component Components TnvModal");
            $this->line("  php artisan create:component User AvatarCard --force");
            $this->line("  php artisan create:component Core Alert --inline");
            $this->newLine();
            return Command::FAILURE;
        }

        // 🔠 Module chữ đầu viết hoa
        $module = ucfirst(Str::camel($module));
        $component = Str::studly($component);
        $modulePath = base_path("Modules/{$module}");

        // 🧱 Kiểm tra module tồn tại
        if (!File::exists($modulePath)) {
            $this->error("❌ Module '{$module}' chưa tồn tại!");
            $this->info("👉 Hãy tạo module trước bằng lệnh:");
            $this->line("   php artisan create:module {$module}");
            return Command::FAILURE;
        }

        // --- Tạo thư mục nếu chưa có ---
        $classDir = "{$modulePath}/Http/Components";
        $viewDir  = "{$modulePath}/resources/views/components";

        if (!File::exists($classDir)) {
            File::makeDirectory($classDir, 0755, true);
            $this->info("📁 Đã tạo thư mục: {$classDir}");
        }

        if (!$inline && !File::exists($viewDir)) {
            File::makeDirectory($viewDir, 0755, true);
            $this->info("📁 Đã tạo thư mục: {$viewDir}");
        }

        // --- File class ---
        $classFile = "{$classDir}/{$component}.php";

        if (File::exists($classFile) && !$force) {
            $this->error("⚠️ File {$classFile} đã tồn tại! Dùng --force để ghi đè.");
            return Command::FAILURE;
        }

        // --- Nội dung class ---
        $classContent = $inline
            ? $this->inlineComponentContent($module, $component)
            : $this->viewComponentContent($module, $component);

        File::put($classFile, $classContent);
        $this->info("✅ Đã tạo class: {$classFile}");

        // --- Nếu không inline thì tạo file view ---
        if (!$inline) {
            $bladeFile = "{$viewDir}/" . $this->kebab($component) . ".blade.php";

            if (File::exists($bladeFile) && !$force) {
                $this->error("⚠️ File view {$bladeFile} đã tồn tại! Dùng --force để ghi đè.");
                return Command::FAILURE;
            }

            $bladeContent = <<<BLADE
<div>
    <!-- {$component} component -->
</div>
BLADE;
            File::put($bladeFile, $bladeContent);
            $this->info("✅ Đã tạo view: {$bladeFile}");
        }

        $this->newLine();
        $this->info("🎉 Blade Component '{$component}' trong module '{$module}' đã sẵn sàng!");
        $this->line("👉 Dùng trong Blade:");
        $this->line("   <x-" . strtolower($module) . "::" . $this->kebab($component) . " />");

        return Command::SUCCESS;
    }

    private function kebab($value)
    {
        return Str::kebab($value); // trả về "tnv-modal"
    }

    private function inlineComponentContent($module, $component)
    {
        
        return <<<PHP
<?php

namespace Modules\\{$module}\\Http\\Components;

use Illuminate\\View\\Component;

class {$component} extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return <<<'blade'
<div>
    <!-- {$component} inline component -->
</div>
blade;
    }
} 
PHP;
    }

    private function viewComponentContent($module, $component)
    {
        $module = ucfirst($module);
        return <<<PHP
<?php

namespace Modules\\{$module}\\Http\\Components;

use Illuminate\\View\\Component;

class {$component} extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('{$module}::components.{$this->kebab($component)}');
    }
}
PHP;
    }
}
