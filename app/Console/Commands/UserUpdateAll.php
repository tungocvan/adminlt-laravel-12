<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TnvUserHelper;
use App\Models\User;
use Carbon\Carbon;

class UserUpdateAll extends Command
{
    /**
     * Ví dụ:
     * php artisan user:update-all all --role="Admin" --verified=1 --except=1,2,5
     */
    protected $signature = 'user:update-all 
        {ids : Danh sách user ID, cách nhau bằng dấu phẩy hoặc "all" để áp dụng toàn bộ} 
        {--role= : Tên vai trò mới (vd: Admin)} 
        {--verified= : Xác minh email (1 để xác minh, 0 để bỏ xác minh)} 
        {--is_admin= : Đặt quyền admin (0|1)} 
        {--password= : Mật khẩu mới cho tất cả} 
        {--birthdate= : Ngày sinh (dd/mm/yyyy hoặc yyyy-mm-dd)} 
        {--username= : Cập nhật username} 
        {--name= : Cập nhật name} 
        {--email= : Cập nhật email (cẩn thận, có thể trùng)}
        {--except= : Danh sách ID cần bỏ qua, cách nhau bằng dấu phẩy}';

    protected $description = 'Cập nhật hàng loạt người dùng bằng cách truyền danh sách ID hoặc "all" (có thể bỏ qua vài ID với --except=).';

    public function handle()
    {
        $input = trim($this->argument('ids'));
        $exceptIds = array_filter(explode(',', (string) $this->option('except')));
        $ids = [];

        // ✅ Hỗ trợ từ khóa "all" hoặc "*"
        if (in_array(strtolower($input), ['all', '*'])) {
            $ids = User::pluck('id')->toArray();

            // Bỏ qua ID chỉ định
            if (!empty($exceptIds)) {
                $ids = array_diff($ids, $exceptIds);
            }

            if (empty($ids)) {
                $this->error('❌ Không còn người dùng nào để cập nhật sau khi loại trừ.');
                return;
            }

            $this->warn("⚠️ Bạn đang cập nhật " . count($ids) . " người dùng (đã loại bỏ ID: " . implode(', ', $exceptIds ?: ['Không có']) . ")");
            if (!$this->confirm('👉 Bạn có chắc chắn muốn tiếp tục?')) {
                $this->info('🛑 Đã hủy thao tác.');
                return;
            }
        } else {
            $ids = array_filter(explode(',', $input));

            // Nếu có except, cũng bỏ qua trong danh sách thủ công
            if (!empty($exceptIds)) {
                $ids = array_diff($ids, $exceptIds);
            }

            if (empty($ids)) {
                $this->error('❌ Không có ID hợp lệ để cập nhật.');
                return;
            }
        }

        // Gom dữ liệu cập nhật
        $data = [];
        $options = ['role', 'is_admin', 'password', 'birthdate', 'username', 'name', 'email'];

        foreach ($options as $opt) {
            $val = $this->option($opt);
            if ($val !== null) {
                $key = $opt === 'role' ? 'role_name' : $opt;
                $data[$key] = $val;
            }
        }

        // ✅ Xử lý verified thành email_verified_at
        if (!is_null($this->option('verified'))) {
            $verified = (int) $this->option('verified');
            $data['email_verified_at'] = $verified === 1 ? Carbon::now() : null;
        }

        if (empty($data)) {
            $this->warn('⚠️ Không có trường nào được cung cấp để cập nhật.');
            return;
        }

        $this->info("🔄 Đang cập nhật " . count($ids) . " người dùng...");

        $result = TnvUserHelper::updateAllUser($ids, $data);

        if ($result['status'] === 'success') {
            $this->info("✅ {$result['message']}");
            $this->line('🧾 Dữ liệu cập nhật:');
            foreach ($result['data'] as $key => $val) {
                $this->line(" - {$key}: " . (is_array($val) ? json_encode($val) : $val));
            }
        } else {
            $this->error("❌ {$result['message']}");
        }
    }
}
