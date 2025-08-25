<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HocsinhImport;

class ImportHocSinh extends Command
{
    protected $signature = 'import:hocsinh';
    protected $description = 'Import danh sách học sinh từ Excel';

    public function handle()
    {
        Excel::import(new HocsinhImport, storage_path('app/public/dsk1.xlsx'));
        $this->info('Đã import dữ liệu học sinh thành công!');
    }
}