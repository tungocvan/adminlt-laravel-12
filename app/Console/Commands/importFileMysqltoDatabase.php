<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class importFileMysqltoDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'import:filesql {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'php artisan import:filesql <ten-file>.sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('name');
        // Kiểm tra file SQL có tồn tại hay không
        $sql_file = storage_path().'/mysql//'.$file;
        if(!file_exists($sql_file)){
            $this->info("kiểm tra không có file $file có nằm trong storage");            
        }else{
            // Lấy nội dung của file SQL
                $sql_content = file_get_contents($sql_file);

                // Thực thi các câu lệnh SQL từ file
                try {
                    DB::unprepared($sql_content);
                    $this->info('import sql thành công');                     
                } catch (Exception $e) {
                    return "Error importing file: " . $e->getMessage();
                }
        }
        //$this->info('importFileSql tạo thành công');    
    }
}
