<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CleanTable extends Command
{
    /**
     * Cú pháp: php artisan clean:table medicines
     */
    protected $signature = 'clean:table {table : Tên bảng cần xóa trong database và bảng migrations}';

    protected $description = 'Xóa bảng trong database và dòng tương ứng trong bảng migrations (bỏ qua foreign key).';

    public function handle()
    {
        $table = $this->argument('table');

        $this->info("🔍 Kiểm tra bảng '{$table}'...");

        // Kiểm tra bảng có tồn tại không
        if (!$this->tableExists($table)) {
            $this->warn("⚪ Bảng '{$table}' không tồn tại trong database.");
        } else {
            // Hỏi xác nhận
            // if (!$this->confirm("⚠️ Bạn có chắc chắn muốn xóa bảng '{$table}' trong database không?", false)) {
            //     $this->info('❌ Hủy thao tác.');
            //     return Command::SUCCESS;
            // }

            try {
                // Tạm thời tắt kiểm tra khóa ngoại
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::statement("DROP TABLE IF EXISTS `$table`");
                DB::statement('SET FOREIGN_KEY_CHECKS=1');

                $this->info("🗑️ Đã xóa bảng '{$table}' thành công (đã bỏ qua khóa ngoại).");
            } catch (\Exception $e) {
                $this->error("❌ Lỗi khi xóa bảng '{$table}': " . $e->getMessage());
                DB::statement('SET FOREIGN_KEY_CHECKS=1'); // bật lại dù lỗi
                return Command::FAILURE;
            }
        }

        // Xóa dòng trong bảng migrations
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

        if (!empty($migrationNames)) {
            foreach ($migrationNames as $migrationName) {
                DB::table('migrations')->where('migration', $migrationName)->delete();
                $this->info("🧹 Đã xóa dòng migration: {$migrationName}");
            }
            $this->info("✅ Hoàn tất — bạn có thể chạy lại php artisan migrate.");
        } else {
            $this->warn("⚠️ Không tìm thấy migration nào chứa Schema::create('{$table}').");
        }

        return Command::SUCCESS;
    }

    protected function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }
}
