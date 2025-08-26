<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() > 0) {
            echo "User table already has data. Skipping...\n";
            return;
        }

        // Tạo role Admin nếu chưa có
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);        
        // Gán toàn bộ permission cho Admin
        $permissions = Permission::pluck('id', 'id')->all();
        $adminRole->syncPermissions($permissions);

          // Tạo admin user
          $admin = User::withoutEvents(function () use ($adminRole) {
            $user = User::create([
                'name' => 'Từ Ngọc Vân',
                'email' => 'tungocvan@gmail.com',
                'username' => 'tungocvan',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'is_admin' => 1,
            ]);
            $user->assignRole($adminRole);
            return $user;
        });

        // Tạo role User nếu chưa có

        $userRole  = Role::firstOrCreate(['name' => 'User']);
        // Gán toàn bộ permission cho Admin
        $permissions = Permission::where('name', 'admin-list')->pluck('id');

        $userRole->syncPermissions($permissions);

      

        // Tạo user thường
        User::withoutEvents(function () use ($userRole) {
            $user01 = User::create([
                'name' => 'User 01',
                'email' => 'user01@gmail.com',
                'username' => 'user01',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'is_admin' => 0,
            ]);
            $user01->assignRole($userRole);
        });

        echo "Admin and sample user created successfully.\n";
    }
}
