<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\UserMailService;

class SendUserMailCommand extends Command
{
    protected $signature = 'user:sendmail
                            {identifier : ID hoặc email của user}
                            {--to= : email nhận, có thể nhiều email cách nhau dấu phẩy}
                            {--subject= : tiêu đề mail}
                            {--body= : nội dung text}
                            {--html= : nội dung html}
                            {--cc= : cc email, cách nhau dấu phẩy}
                            {--bcc= : bcc email, cách nhau dấu phẩy}
                            {--attach= : file đính kèm, cách nhau dấu phẩy}';

    protected $description = 'Gửi mail bằng Gmail của user đã lưu trong setOption()';

    public function handle()
    {
        $identifier = $this->argument('identifier');

        $user = $this->findUser($identifier);
        if (!$user) {
            $this->error("Không tìm thấy user: {$identifier}");
            return Command::FAILURE;
        }

        $to          = $this->option('to') ? explode(',', $this->option('to')) : null;
        $cc          = $this->option('cc') ? explode(',', $this->option('cc')) : [];
        $bcc         = $this->option('bcc') ? explode(',', $this->option('bcc')) : [];
        $attachments = $this->option('attach') ? explode(',', $this->option('attach')) : [];

        $subject = $this->option('subject') ?? 'Test mail';
        $body    = $this->option('body');
        $html    = $this->option('html');

        $result = UserMailService::sendUserMail($user, $to, $subject, $body, $html, $cc, $bcc, $attachments);

        if ($result['status'] === 'success') {
            $this->info($result['message']);
            return Command::SUCCESS;
        }

        $this->error($result['message']);
        return Command::FAILURE;
    }

    protected function findUser($identifier)
    {
        return is_numeric($identifier)
            ? User::find($identifier)
            : User::where('email', $identifier)->first();
    }
}
