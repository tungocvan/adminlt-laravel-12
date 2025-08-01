<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(int $count = 10): void
    {
        // Đảm bảo role 'user' tồn tại
        Role::firstOrCreate(['name' => 'user']);

        // Tạo người dùng thường
        User::factory()->count($count)->regular()->create();
    }
}
