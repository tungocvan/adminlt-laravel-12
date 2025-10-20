<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CleanTable extends Command
{
    /**
     * Cú pháp lệnh
     * php artisan clean:table users
     */
    protected $signature = 'clean:table {table : Tên bảng cần xóa trong database và bảng migrations}';

    /**
     * Mô tả lệnh
     */
    protected $description = 'Xóa bảng trong database và dòng tương ứng trong bảng migrations để có thể migrate lại.';

    public function handle()
    {
        $table = $this->argument('table');

        $this->info("🔍 Kiểm tra bảng '{$table}'...");

        // Bước 1: Kiểm tra bảng có tồn tại không
        if (!$this->tableExists($table)) {
            $this->warn("⚪ Bảng '{$table}' không tồn tại trong database.");
        } else {
            // Bước 2: Xác nhận trước khi xóa
            if (!$this->confirm("⚠️ Bạn có chắc chắn muốn xóa bảng '{$table}' trong database không?", false)) {
                $this->info('❌ Hủy thao tác.');
                return Command::SUCCESS;
            }

            DB::statement("DROP TABLE IF EXISTS `$table`");
            $this->info("🗑️ Đã xóa bảng '{$table}' thành công!");
        }

        // Bước 3: Tìm file migration có liên quan đến bảng này
        $migrationPath = database_path('migrations');
        $files = File::files($migrationPath);
        $migrationNames = [];

        foreach ($files as $file) {
            $content = File::get($file->getRealPath());

            if (preg_match("/Schema::create\(['\"]{$table}['\"]/", $content)) {
                $migrationName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $migrationNames[] = $migrationName;
            }
        }

        // Bước 4: Xóa các dòng tương ứng trong bảng migrations
        if (!empty($migrationNames)) {
            foreach ($migrationNames as $migrationName) {
                DB::table('migrations')->where('migration', $migrationName)->delete();
                $this->info("🧹 Đã xóa dòng migration: {$migrationName}");
            }
            $this->info("✅ Bảng '{$table}' đã được dọn sạch — bạn có thể chạy lại php artisan migrate.");
        } else {
            $this->warn("⚠️ Không tìm thấy migration nào chứa Schema::create('{$table}').");
        }

        return Command::SUCCESS;
    }

    /**
     * Kiểm tra bảng có tồn tại không
     */
    protected function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }
}
