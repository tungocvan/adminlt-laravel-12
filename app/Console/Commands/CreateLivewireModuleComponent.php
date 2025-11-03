<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CreateLivewireModuleComponent extends Command
{
    protected $signature = 'create:livewire {module? : Tên module (ví dụ: Blog)} {component? : Tên component (ví dụ: PostList)} {--delete : Xóa component và view nếu tồn tại}';

    protected $description = 'Tạo Livewire component trong module với view (ModuleServiceProvider sẽ tự động đăng ký).';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $module = $this->argument('module');
        $component = $this->argument('component');

        // --- Kiểm tra hợp lệ ---
        if (empty($module) || empty($component)) {
            $this->warn("⚠️ Thiếu tham số!");
            $this->line("👉 Cú pháp đúng: php artisan create:livewire {module} {component}");
            $this->info("   Ví dụ: php artisan create:livewire Blog PostList");
            return Command::INVALID;
        }

        // --- Tự động chuẩn hoá ---
        $module = Str::studly($module);       // qlhs -> Qlhs
        $component = Str::studly($component); // qlhs-list -> QlhsList
        $componentSnake = Str::kebab($component); // QlhsList -> qlhs-list

        // --- Đường dẫn ---
        $componentDir = base_path("Modules/{$module}/Livewire");
        $componentPath = "{$componentDir}/{$component}.php";
        $viewDir = base_path("Modules/{$module}/resources/views/livewire");
        $viewPath = "{$viewDir}/{$componentSnake}.blade.php";

        // --- Nếu có --delete ---
        if ($this->option('delete')) {
            $deleted = false;
            if ($this->files->exists($componentPath)) {
                $this->files->delete($componentPath);
                $deleted = true;
            }
            if ($this->files->exists($viewPath)) {
                $this->files->delete($viewPath);
                $deleted = true;
            }

            if ($deleted) {
                $this->info("🗑️ Đã xóa component và view của {$module}/{$component}.");
            } else {
                $this->warn("⚠️ Không tìm thấy component hoặc view để xóa.");
            }
            return Command::SUCCESS;
        }

        // --- Tạo thư mục ---
        foreach ([$componentDir, $viewDir] as $dir) {
            if (! $this->files->isDirectory($dir)) {
                $this->files->makeDirectory($dir, 0755, true);
            }
        }

        // --- Tạo component class ---
        if (! $this->files->exists($componentPath)) {
            $classTemplate = <<<PHP
<?php

namespace Modules\\$module\\Livewire;

use Livewire\Component;

class $component extends Component
{
    public function render()
    {
        return view('$module::livewire.$componentSnake');
    }
}
PHP;
            $this->files->put($componentPath, $classTemplate);
            $this->info("✅ Đã tạo component: {$componentPath}");
        } else {
            $this->warn("⚠️ Component {$component} đã tồn tại!");
        }

        // --- Tạo view ---
        if (! $this->files->exists($viewPath)) {
            $viewTemplate = <<<BLADE
<div>
    <!-- Livewire component: $component -->
</div>
BLADE;
            $this->files->put($viewPath, $viewTemplate);
            $this->info("✅ Đã tạo view: {$viewPath}");
        } else {
            $this->warn("⚠️ View {$componentSnake}.blade.php đã tồn tại!");
        }

        // --- Thông báo cuối ---
        $this->info("🎉 Livewire component sẵn sàng!");
        $this->line("👉 Dùng trong blade: @livewire('" . Str::lower($module) . ".$componentSnake')");
        return Command::SUCCESS;
    }
}
