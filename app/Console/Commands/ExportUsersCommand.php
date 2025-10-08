<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TnvUserHelper;

class ExportUsersCommand extends Command
{
    protected $signature = 'export:users 
                            {ids?* : Danh sách ID user, ví dụ: 1 2 3 (để trống = tất cả)} 
                            {--fields= : Danh sách field muốn export, ví dụ: id,name,email} 
                            {--title= : Tiêu đề hiển thị trên đầu file Excel (tuỳ chọn)} 
                            {--footer= : Ghi chú hoặc người lập bảng ở cuối file (tuỳ chọn)}';

    protected $description = 'Xuất danh sách user ra file Excel (hỗ trợ tiêu đề và footer).';

    public function handle()
    {
        $this->info('🔄 Đang thực hiện xuất danh sách người dùng...');

        $ids = $this->argument('ids');
        $fieldsOption = $this->option('fields');
        $title = $this->option('title') ?: 'BÁO CÁO DANH SÁCH NGƯỜI DÙNG';
         // ⚡ Mặc định footer nếu không truyền
         $footer = $this->option('footer') ?? 'NGƯỜI LẬP BẢNG';

        $fields = [];
        if (!empty($fieldsOption)) {
            $fields = array_map('trim', explode(',', $fieldsOption));
        }

        $result = TnvUserHelper::exportUsersToExcel($ids, $fields, $title, $footer);

        if (!$result['status']) {
            $this->error('❌ ' . $result['message']);
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('✅ Xuất file Excel thành công!');
        $this->line('📄 File: ' . $result['path']);
        $this->line('👥 Tổng số user: ' . $result['count']);
        $this->line('📋 Trường xuất: ' . implode(', ', $result['fields']));
        if ($footer) {
            $this->line('🖋️ Ghi chú footer: ' . $footer);
        }
        $this->newLine();

        return Command::SUCCESS;
    }
}
