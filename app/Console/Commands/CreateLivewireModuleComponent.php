<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CreateLivewireModuleComponent extends Command
{
    protected $signature = 'create:livewire
                            {module : Tên module (chữ hoa đầu)}
                            {component : Tên component CamelCase}
                            {--delete : Xóa component và view nếu tồn tại}';

    protected $description = 'Tạo Livewire component trong module với view, và tự động đăng ký view namespace';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $module = ucfirst($this->argument('module')); // Module chữ hoa đầu
        $component = Str::studly($this->argument('component')); // Component CamelCase
        $componentSnake = Str::kebab($component); // snake-case / kebab-case cho view

        // --- Thư mục & file ---
        $componentDir = base_path("Modules/{$module}/Livewire");
        $componentPath = "{$componentDir}/{$component}.php";

        $viewDir = base_path("Modules/{$module}/resources/views/livewire");
        $viewPath = "{$viewDir}/{$componentSnake}.blade.php";

        $serviceProviderPath = base_path("Modules/{$module}/Providers/{$module}ServiceProvider.php");

        // --- Xóa nếu có --delete ---
        if ($this->option('delete')) {
            if ($this->files->exists($componentPath)) $this->files->delete($componentPath);
            if ($this->files->exists($viewPath)) $this->files->delete($viewPath);
            $this->info("Deleted component and view if existed.");
            return 0;
        }

        // --- Tạo thư mục nếu chưa có ---
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
            $this->info("✅ Created component: {$componentPath}");
        }

        // --- Tạo view ---
        if (! $this->files->exists($viewPath)) {
            $viewTemplate = "<div>\n    <!-- Livewire component $component -->\n</div>";
            $this->files->put($viewPath, $viewTemplate);
            $this->info("✅ Created view: {$viewPath}");
        }

        // --- Tự động đăng ký view namespace trong ServiceProvider ---
        if ($this->files->exists($serviceProviderPath)) {
            $content = $this->files->get($serviceProviderPath);
            $loadViewCode = "\$this->loadViewsFrom(__DIR__.'/../resources/views', '$module');";

            if (! str_contains($content, $loadViewCode)) {
                // Thêm vào method boot()
                $content = preg_replace(
                    '/public function boot\(\)\s*\{/',
                    "public function boot()\n    {\n        $loadViewCode",
                    $content,
                    1
                );
                $this->files->put($serviceProviderPath, $content);
                $this->info("✅ Registered view namespace in {$module}ServiceProvider");
            }
        } else {
            // Nếu ServiceProvider chưa có, tạo file mẫu
            $providerTemplate = <<<PHP
<?php

namespace Modules\\$module\\Providers;

use Illuminate\Support\ServiceProvider;

class {$module}ServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \$this->loadViewsFrom(__DIR__.'/../resources/views', '$module');
    }

    public function register()
    {
        //
    }
}
PHP;
            $providerDir = dirname($serviceProviderPath);
            if (! $this->files->isDirectory($providerDir)) {
                $this->files->makeDirectory($providerDir, 0755, true);
            }
            $this->files->put($serviceProviderPath, $providerTemplate);
            $this->info("✅ Created ServiceProvider and registered view namespace: {$serviceProviderPath}");
        }

        $this->info("🎉 Livewire component ready! Use: @livewire('" . Str::lower($module) . ".$componentSnake')");
    }
}
