<?php

namespace Modules;
use Illuminate\Support\ServiceProvider;
use File;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->register(\Modules\ModuleServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $modules = $this->getModules();
        if(count($modules) > 0) {
            foreach ($modules as $module) {
                $this->registerModule($module);
            }
        }

    }
    private function registerModule($module){
        $modulePath = __DIR__ . "/{$module}";
        // Khai báo route
        if (File::exists($modulePath . '/routes/web.php')) {
            $this->loadRoutesFrom($modulePath . '/routes/web.php');
        }
        if (File::exists($modulePath . '/routes/api.php')) {
            $this->loadRoutesFrom($modulePath . '/routes/api.php');
        }
        // Khai báo views
        // Gọi view thì ta sử dụng: view( ' Demo: : index' ) , @extends( ' Demo: : index' ) , @include( ' Demo: : index' )
        if (File::exists($modulePath . '/resources/views')) {
            $this->loadViewsFrom($modulePath . '/resources/views', $module);
        }
        // Khai báo languages
        if (File::exists($modulePath . '/resources/lang')) {
            // Đa ngôn ngữ theo file php
            // Dùng đa ngôn ngữ tại file php resources/lang/en/general. php : @lang( ' Demo: : general. hello' ) Laravel Modules 4
            $this->loadTranslationsFrom($modulePath . '/resources/lang', $module);
            // Đa ngôn ngữ theo file j son
            $this->loadJSONTranslationsFrom($modulePath . '/resources/lang');
        }
        // Khai báo helpers
        if (File::exists($modulePath . '/Helpers')) {
            // Tất cả files có tại thư mục helpers
            $helper_dir = File::allFiles($modulePath . '/Helpers');
            // khai báo helpers
            foreach ($helper_dir as $key => $value) {
                $file = $value->getPathName();
                require $file;
            }
        }
        // Khai báo migration
        // Toàn bộ file migration của modules sẽ tự động được load
        if (File::exists($modulePath . '/database/migrations')) {
            $this->loadMigrationsFrom($modulePath . '/database/migrations');
        }

    }
    private function getModules(){
        $directories = array_map('basename', File::directories(__DIR__));
        return $directories;
    }
}
