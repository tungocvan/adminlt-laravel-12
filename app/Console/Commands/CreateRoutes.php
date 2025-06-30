<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class CreateRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:routes {name} ';

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
        $pathroutes = base_path('Modules/' . $name.'/routes/web.php');
        $template = app_path('Console/Commands/template/routes.txt');
        if (File::exists($template)) {
            $content = file_get_contents($template);
            $newContent = str_replace('{Module}',$name, $content);
            $newContent = str_replace('{module}',strtolower($name), $newContent);
            //$webRoutes = $pathroutes . '/web.php';
            //dd($newContent);
            file_put_contents($pathroutes, $newContent);
            $this->info('Create routes web Module success');
        }
    }
}
