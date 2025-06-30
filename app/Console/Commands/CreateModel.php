<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class CreateModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:model {name} {module}';

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
        $name = ucfirst($this->argument('name'));        
        $module = ucfirst($this->argument('module'));          
        
        $pathModels = base_path('Modules/' . $module.'/Models'); 
        if(!File::exists($pathModels)) {
            return $this->error("Module $module không tồn tại ");
        }  

        $nameModels = $pathModels.'/'.$name . '.php';

        if(File::exists($nameModels)) {
            return $this->error("Model $name đã tồn tại ");
        }

        $template = app_path('Console/Commands/template/models.txt');
        if (File::exists($template)) {
            $content = file_get_contents($template);
            $newContent = str_replace('{Module}',$module, $content);
            $newContent = str_replace('{Name}',$name, $newContent);
            file_put_contents($nameModels, $newContent);
            $this->info("Model $name đã tạo thành công.");
        }else{
            $this->error("Template models.txt không tồn tại ");
        }

    }
}
