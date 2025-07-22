<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PermissionTableSeeder;
use Database\Seeders\CreateAdminUserSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\VnAdministrativeUnitSeeder;


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
            ProductSeeder::class,
            VnAdministrativeUnitSeeder::class
        ]);
    }
}
