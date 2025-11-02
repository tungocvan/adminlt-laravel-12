<?php

namespace Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use File;
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
        if (File::exists($modulePath . '/routes/api.php')) {
            $this->loadRoutesFrom($modulePath . '/routes/api.php');
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
            $componentFiles = File::allFiles($livewirePath);
            foreach ($componentFiles as $file) {
                $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $fullClass = "\\Modules\\$module\\Livewire\\$className";
                $alias = Str::kebab($className); // CamelCase -> kebab-case
                Livewire::component(strtolower($module) . '.' . $alias, $fullClass);
            }
        }
    }
}
