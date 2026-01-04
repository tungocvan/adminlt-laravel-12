<?php

namespace Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $modules = $this->getModules();
        foreach ($modules as $module) {
            $this->registerModule($module);
        }
    }

    private function getModules(): array
    {
        return array_map('basename', File::directories(__DIR__));
    }

    private function registerModule($module)
    {
        $modulePath = __DIR__ . "/{$module}";

        // --- Routes ---
        if (File::exists($modulePath . '/routes/web.php')) {
            $this->loadRoutesFrom($modulePath . '/routes/web.php');
        }
        // if (File::exists($modulePath . '/routes/api.php')) {
        //     $this->loadRoutesFrom($modulePath . '/routes/api.php');
        // }
        if (File::exists($modulePath . '/routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(function () use ($modulePath) {
                    require $modulePath . '/routes/api.php';
                });
        }

        // --- Views ---
        if (File::exists($modulePath . '/resources/views')) {
            $this->loadViewsFrom($modulePath . '/resources/views', $module);
        }

        // --- Translations ---
        if (File::exists($modulePath . '/resources/lang')) {
            $this->loadTranslationsFrom($modulePath . '/resources/lang', $module);
            $this->loadJSONTranslationsFrom($modulePath . '/resources/lang');
        }

        // --- Helpers ---
        if (File::exists($modulePath . '/Helpers')) {
            $helperFiles = File::allFiles($modulePath . '/Helpers');
            foreach ($helperFiles as $file) {
                require $file->getPathname();
            }
        }

        // --- Migrations ---
        if (File::exists($modulePath . '/database/migrations')) {
            $this->loadMigrationsFrom($modulePath . '/database/migrations');
        }

        // --- Livewire Components ---
        $livewirePath = $modulePath . '/Livewire';

        if (File::exists($livewirePath)) {
            foreach (File::allFiles($livewirePath) as $file) {
                $relativePath = str_replace([$livewirePath . DIRECTORY_SEPARATOR, '.php'], '', $file->getPathname());

                $classPath = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

                $fullClass = "\\Modules\\{$module}\\Livewire\\{$classPath}";

                if (!class_exists($fullClass)) {
                    continue;
                }

                $alias = collect(explode(DIRECTORY_SEPARATOR, $relativePath))
                    ->map(fn($part) => Str::kebab($part))
                    ->implode('.');

                app()->booted(function () use ($module, $alias, $fullClass) {
                    Livewire::component(strtolower($module) . '.' . $alias, $fullClass);
                });
            }
        }

        /** ---------------- BLADE COMPONENTS ---------------- */
        $componentPath = $modulePath . '/Http/Components';
        $bladeViewPath = $modulePath . '/resources/views/components';

        if (File::exists($componentPath)) {
            Blade::componentNamespace("Modules\\{$module}\\Http\\Components", strtolower($module));
        }

        if (File::exists($bladeViewPath)) {
            // 🔹 Cho phép load view của Blade component trong module
            $this->loadViewsFrom($bladeViewPath, strtolower($module));
        }
    }
}
