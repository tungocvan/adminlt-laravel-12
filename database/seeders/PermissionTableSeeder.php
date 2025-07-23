<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulesPath = base_path('Modules');

        if (!File::exists($modulesPath)) {
            $this->command->error("Modules folder not found.");
            return;
        }

        $defaultPermissions = ['list', 'create', 'edit', 'delete'];

        $modules = collect(File::directories($modulesPath))
            ->map(fn($path) => strtolower(basename($path)))
            ->unique()
            ->values();
        // Nếu chưa có 'role', thêm vào danh sách
        if (!$modules->contains('role')) {
            $modules->push('role');
        }
        foreach ($modules as $module) {
            foreach ($defaultPermissions as $perm) {
                $permissionName = "{$module}-{$perm}";
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        $this->command->info("Permissions generated for modules: " . $modules->implode(', '));
    }
}
