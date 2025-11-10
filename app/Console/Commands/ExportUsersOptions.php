<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\UsersOptionsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportUsersOptions extends Command
{
    protected $signature = 'export:users-options {filename=users_options.xlsx}';
    protected $description = 'Export toàn bộ users + options ra Excel chuẩn import';

    public function handle()
    {
        $filename = $this->argument('filename');

        // Tự động tạo thư mục public/excel/database/
        $path = "excel/database/{$filename}";

        Excel::store(new UsersOptionsExport(), $path, 'public');

        $this->info("✅ Đã xuất file: storage/app/public/{$path}");

        return 0;
    }
}
