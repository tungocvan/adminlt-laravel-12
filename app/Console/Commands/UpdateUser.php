<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TnvUserHelper;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUser extends Command
{
    /**
     * Cú pháp:
     * php artisan user:update {id_or_email_or_username}
     *                         [--name=] [--email=] [--username=]
     *                         [--password=] [--c_password=]
     *                         [--old_password=]
     *                         [--birthdate=] [--role=]
     *                         [--verified=] [--is_admin=]
     *                         [--referral_code=]
     */
    protected $signature = 'user:update
        {id_or_email_or_username : ID, email hoặc username của user}
        {--name= : Họ tên người dùng}
        {--email= : Email người dùng}
        {--username= : Tên đăng nhập}
        {--password= : Mật khẩu mới}
        {--c_password= : Xác nhận mật khẩu mới}
        {--old_password= : Mật khẩu cũ (dùng để xác nhận nếu muốn thay mật khẩu)}
        {--birthdate= : Ngày sinh (dd/mm/yyyy hoặc yyyy-mm-dd)}
        {--role= : Tên vai trò (role_name)}
        {--verified= : Xác minh email (1 hoặc 0)}
        {--is_admin= : Cờ admin (1 hoặc 0)}
        {--referral_code= : Mã giới thiệu}';

    protected $description = 'Cập nhật thông tin người dùng (CLI) - có thể truyền ID, email hoặc username';

    public function handle()
    {
        $input = $this->argument('id_or_email_or_username');

        // 🔍 Xác định user ID dựa vào input
        $userId = $this->resolveUserId($input);

        if (!$userId) {
            $this->error("❌ Không tìm thấy người dùng với giá trị: {$input}");
            return 1;
        }

        $data = [];

        // --- Thu thập dữ liệu từ options ---
        $fields = [
            'name', 'email', 'username', 'password', 'c_password',
            'role', 'referral_code', 'birthdate'
        ];

        foreach ($fields as $opt) {
            $value = $this->option($opt);
            if (!is_null($value)) {
                $key = $opt === 'role' ? 'role_name' : $opt;
                $data[$key] = $value;
            }
        }

        // --- Xử lý password ---
        $newPassword = $this->option('password');
        $confirmPassword = $this->option('c_password');
        $oldPassword = $this->option('old_password');

        if (!is_null($newPassword)) {
            if (!is_null($confirmPassword) && $newPassword !== $confirmPassword) {
                $this->error("❌ Mật khẩu xác nhận không khớp!");
                return 1;
            }

            if (!is_null($oldPassword)) {
                $user = User::find($userId);
                if (!Hash::check($oldPassword, $user->password)) {
                    $this->error("❌ Mật khẩu cũ không chính xác. Không thể đổi mật khẩu!");
                    return 1;
                }
            }

            $data['password'] = $newPassword;
        }

        // --- Ép kiểu boolean/int cho flags ---
        if (!is_null($this->option('verified'))) {
            $data['verified'] = (bool) $this->option('verified');
        }

        if (!is_null($this->option('is_admin'))) {
            $data['is_admin'] = (int) $this->option('is_admin');
        }

        $this->info("🔄 Đang cập nhật user ID #{$userId}...");

        // --- Gọi helper updateUser() ---
        $result = TnvUserHelper::updateUser($userId, $data);

        if ($result['status'] === 'success') {
            $this->info("✅ {$result['message']}");
            $this->line("🧾 Thông tin chi tiết:");
            foreach ($result['data'] as $key => $value) {
                if (is_array($value)) $value = implode(', ', $value);
                $this->line(" - {$key}: {$value}");
            }
        } else {
            $this->error("❌ {$result['message']}");
            if (!empty($result['errors'])) {
                foreach ($result['errors']->toArray() as $field => $msg) {
                    $this->line("   • {$field}: " . implode(', ', $msg));
                }
            }
        }

        return 0;
    }

    protected function resolveUserId(string $input): ?int
    {
        if (is_numeric($input)) {
            return User::find((int)$input)?->id;
        }

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $input)->value('id');
        }

        return User::where('username', $input)->value('id');
    }
}
