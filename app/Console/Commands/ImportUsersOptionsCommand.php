<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Excel\UsersOptionsImporter;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportUsersOptionsCommand extends Command
{
    protected $signature = 'excel:import-users {filename}';
    protected $description = 'Import users & all user options from Excel file';

    public function handle()
    {
        $file = $this->argument('filename');
        $path = storage_path("app/public/excel/database/{$file}");

        $this->info("📥 Đang tải file Excel...");

        try {
            $importer = (new UsersOptionsImporter())->loadFile($path);
        } catch (\Exception $e) {
            $this->error("❌ " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("🔄 Đang xử lý dữ liệu...");
        $bar = $this->output->createProgressBar(1);
        $bar->start();

        $result = $importer->import();

        $bar->finish();
        $this->newLine(2);

        if (!$result) {
            $this->error("❌ Import thất bại! Kiểm tra file: storage/logs/import.log");
            return Command::FAILURE;
        }

        $this->info("✅ Import hoàn tất.");
        return Command::SUCCESS;
    }

}

