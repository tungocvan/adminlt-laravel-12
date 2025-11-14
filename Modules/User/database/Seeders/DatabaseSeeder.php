<?php

namespace Modules\User\Database\Seeders;
use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Gọi các Seeder khác hoặc thực hiện logic seeding ở đây
        DB::table('wp_users')->insert([
            'user_login' => 'admin',
            'user_pass' => Hash::make('123456'),
            'user_nicename' => 'admin',
            'user_email' => 'tungocvan@gmail.com',
            'user_url' => 'https://hamada.tk',
            'user_registered' => date('Y-m-d H:i:s'),
            'user_activation_key' => '',
            'user_status' => 0,
            'display_name' => 'admin',
        ]);
  
    }
}

