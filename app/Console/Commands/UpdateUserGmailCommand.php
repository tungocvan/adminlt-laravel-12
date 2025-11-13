<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\UserService;

class UpdateUserGmailCommand extends Command
{
    /**
     * Lệnh artisan.
     *
     * Có thể truyền ID hoặc email của user.
     */
    protected $signature = 'user:update-gmail 
                            {identifier : ID hoặc email của người dùng} 
                            {--password= : Mật khẩu ứng dụng Gmail (App Password)}';

    /**
     * Mô tả lệnh.
     */
    protected $description = 'Cập nhật thông tin Gmail của người dùng qua setOption() (hỗ trợ ID hoặc email)';

    /**
     * Thực thi command.
     */
    public function handle()
    {
        $identifier = $this->argument('identifier');
        $password = $this->option('password');

        // Kiểm tra password
        if (!$password) {
            $this->error('❌ Bạn phải truyền mật khẩu ứng dụng Gmail bằng --password="..."');
            return Command::FAILURE;
        }

        // Xác định user theo ID hoặc email
        $user = $this->findUser($identifier);

        if (!$user) {
            $this->error("❌ Không tìm thấy người dùng với giá trị: {$identifier}");
            return Command::FAILURE;
        }

        // Cập nhật Gmail option qua service
        $result = UserService::updateGmail($user->id, [
            'password' => $password,
        ]);

        // Hiển thị kết quả
        if ($result['status'] === 'success') {
            $this->newLine();
            $this->info('✅ ' . $result['message']);
            $this->line('────────────────────────────');
            $this->line('👤 User: ' . $user->name);
            $this->line('📧 Email: ' . $user->email);
            $this->line('🔑 Password: ' . str_repeat('*', strlen($password) - 4) . substr($password, -4));
            $this->line('⏰ Cập nhật lúc: ' . now()->toDateTimeString());
            $this->line('────────────────────────────');
            return Command::SUCCESS;
        }

        $this->error('❌ ' . $result['message']);
        return Command::FAILURE;
    }

    /**
     * Tìm user theo ID hoặc email.
     *
     * @param string $identifier
     * @return \App\Models\User|null
     */
    protected function findUser(string $identifier)
    {
        // Nếu là số -> tìm theo id
        if (is_numeric($identifier)) {
            return User::find((int) $identifier);
        }

        // Ngược lại tìm theo email
        return User::where('email', $identifier)->first();
    }
}
