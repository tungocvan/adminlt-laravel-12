<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VnAdministrativeUnitSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sql/ImportData_vn_units.sql');

        if (!File::exists($path)) {
            $this->command->error("Không tìm thấy file: $path");
            return;
        }

        $sql = File::get($path);

        // Nếu file có nhiều dòng INSERT/LOAD DATA
        DB::unprepared($sql);

        $this->command->info('✅ Import dữ liệu hành chính VN thành công!');
    }
}

 