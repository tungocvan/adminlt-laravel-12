<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Permission::count() == 0) {
            $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'admin-list',
            'admin-create',
            'admin-edit',
            'admin-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'settings-list',
            'settings-create',
            'settings-edit',
            'settings-delete'
            ];

            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
            }
        }else {
            echo "Table already has data, skipping seeding.\n";
        }
    }
}
