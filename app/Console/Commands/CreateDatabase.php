<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    protected $signature = 'db:create {name}';
    protected $description = 'Create a new database with utf8mb4_unicode_ci';

    public function handle()
    {
        $dbname = $this->argument('name');

        // Kiểm tra xem database đã tồn tại hay chưa
        $databaseExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$dbname]);

        if (!empty($databaseExists)) {
            $this->info("Database '$dbname' already exists.");
            $continue = $this->confirm('Do you want to continue? (y/n)', false);
            if (!$continue) {
                $this->info('Exiting...');
                return;
            }
        }

        // Tạo database mới
        DB::statement("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->info("Database '$dbname' created successfully.");
    }
}