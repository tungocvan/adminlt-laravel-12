<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PermissionTableSeeder;
use Database\Seeders\CreateAdminUserSeeder;
use Database\Seeders\VnAdministrativeUnitSeeder;
use Illuminate\Support\Facades\Artisan; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,        
            VnAdministrativeUnitSeeder::class
        ]);
        Artisan::call('import:danhmuc', [
            'path' => 'database/seeders/data/nhom_thuoc.json',
        ]);
        Artisan::call('import:danhmucthuoc');
      
    }
}
