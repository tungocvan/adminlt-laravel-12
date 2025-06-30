<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MysqlDatabase extends Command
{
    protected $signature = 'db:mysql {action} {name?}';
    protected $description = 'Manage MySQL databases';

    public function handle()
    {
        $action = $this->argument('action');
        $name = $this->argument('name');

        switch ($action) {
            case 'create':
                $this->createDatabase($name);
                break;

            case 'delete':
                $this->deleteDatabase($name);
                break;

            case 'show':
                $this->showDatabases();
                break;

            case 'query':
                $this->runQuery($name);
                break;
            case 'import':
                $this->importFileMySQL($name);
                break;
            case 'backup':
                $this->backupDatabase($name);
                break;
            case 'restore':
                $this->restoreDatabase($name);
                break;
           
            default:
                $this->helps();
                break;
        }
    }

    protected function helps()
    {
        $this->info("php artisan db:mysql create ten-database");
        $this->info("php artisan db:mysql delete ten-database");
        $this->info("php artisan db:mysql backup ten-database");
        $this->info("php artisan db:mysql restore ten-database");
        $this->info("php artisan db:mysql import ten-file.sql");
        $this->info("php artisan db:mysql query cau-lenh-mysql");
        $this->info("php artisan db:mysql show");
    }
    protected function createDatabase($name)
    {
        if (empty($name)) {
            $this->error('Database name is required.');
            return;
        }

        // Kiểm tra xem database đã tồn tại hay chưa
        $databaseExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$name]);
        
        if (!empty($databaseExists)) {
            $this->info("Database '$name' already exists.");
            return;
        }

        // Tạo database mới
        DB::statement("CREATE DATABASE `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->info("Database '$name' created successfully.");
    }

    protected function deleteDatabase($name)
    {
        if (empty($name)) {
            $this->error('Database name is required.');
            return;
        }

        // Kiểm tra xem database có tồn tại hay không
        $databaseExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$name]);
        
        if (empty($databaseExists)) {
            $this->info("Database '$name' does not exist.");
            return;
        }

        // Xóa database
        DB::statement("DROP DATABASE `$name`");
        $this->info("Database '$name' deleted successfully.");
    }

    protected function showDatabases()
    {
        $databases = DB::select("SHOW DATABASES");

        $this->info("Databases:");
        foreach ($databases as $database) {
            $this->line($database->Database);
        }
    }

    protected function runQuery($query)
    {
        if (empty($query)) {
            $this->error('Query is required.');
            return;
        }
        
        try {
            print_r($query); 
            $results = DB::select($query);
            $this->info("Query executed successfully.");

            if($query === "show tables;" || $query === "SHOW TABLES;"){
                $databaseName = DB::getDatabaseName();            
                $this->info("Query executed successfully.");
                foreach ($results as $result) {
                    $tableNameColumn = "Tables_in_$databaseName";
                    echo $result->$tableNameColumn . "\n";;
                }
            }else{
                print_r($results); 
            }
           // print_r($results);    
        } catch (\Exception $e) {
            $this->error('Error executing query: ' . $e->getMessage());
        }
    }

  
    protected function importFileMySQL($fileName) {
        // Đọc nội dung của file SQL
        $sql = file_get_contents($fileName);
        //dd($sql);
        // Kiểm tra nếu việc đọc file thành công
        if ($sql === false) {
            throw new \Exception("Không thể đọc file: " . $fileName);
        }

        // Chia nhỏ các câu lệnh SQL bằng dấu ';'
        $queries = explode(';', $sql);

        // Thực thi từng câu lệnh SQL
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                DB::statement($query);
            }
        }
        $this->info("Import successfully.");
        return true; // Hoàn thành import
    }

    protected function backupDatabase($filePath) {
        // Lấy thông tin cấu hình database
        $dbConfig = config('database.connections.mysql');
        
        $host = $dbConfig['host'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        // Tạo lệnh mysqldump
        $command = "mysqldump --user={$username} --password={$password} --host={$host} {$database} > {$filePath}";
    
        // Thực thi lệnh
        system($command, $returnVar);
    
        // Kiểm tra kết quả
        if ($returnVar !== 0) {
            throw new \Exception("Backup database failed.");
        }
    
        return true; // Hoàn thành backup
    }

    protected function restoreDatabase($filePath) {
        // Lấy thông tin cấu hình database
        $dbConfig = config('database.connections.mysql');
        
        $host = $dbConfig['host'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        // Tạo lệnh mysql để khôi phục cơ sở dữ liệu
        $command = "mysql --user={$username} --password={$password} --host={$host} {$database} < {$filePath}";
    
        // Thực thi lệnh
        system($command, $returnVar);
    
        // Kiểm tra kết quả
        if ($returnVar !== 0) {
            throw new \Exception("Restore database failed.");
        }
    
        return true; // Hoàn thành khôi phục
    }
}