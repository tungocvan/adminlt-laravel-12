<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class CreateController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:controller {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // strtolower
        $name = ucfirst($this->argument('name'));
        $module = ucfirst($this->argument('module'));



        $nameController = $this->argument('name').'Controller';
        if($module){
            $pathController = base_path('Modules/' . $module.'/Http/Controllers');
        }else{
            $pathController = base_path('Modules/' . $name.'/Http/Controllers');
        }
        $newController = $pathController . '/' . $name . 'Controller.php';
        $pathViews = base_path('Modules/' . $module.'/resources/views/'.strtolower($name).'.blade.php');
        if(File::exists($newController)) {
            $this->info($name ."Controller đã tồn tại.");
            return 0;
        }

        if(!File::exists($pathController)) {
            $this->info("Module $module không tồn tại.");
            return 0;
        }else{
            $module = $name;
        }

        $template = app_path('Console/Commands/template/controller.txt');
        if (File::exists($template)) {
            $content = file_get_contents($template);
            $newContent = str_replace('{Module}',"$module", $content);
            $newContent = str_replace('{module}',strtolower($name), $newContent);
            file_put_contents($newController, $newContent);


            if(!File::exists($pathViews)) {
                $template = app_path('Console/Commands/template/views.txt');
                if (File::exists($template)) {
                    $content = file_get_contents($template);
                    file_put_contents($pathViews, $content);
                }
            }
            $this->info($name ."Controller đã tạo thành công.");
            return 0;
        }
    }
}
