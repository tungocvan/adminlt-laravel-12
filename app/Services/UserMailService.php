<?php

namespace App\Services;

use App\Mail\UserHtmlMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class UserMailService
{
    public static function sendUserMail(
        $user,
        $to,
        string $subject,
        ?string $body = null,
        ?string $html = null,
        array $cc = [],
        array $bcc = [],
        array $attachments = [],
        ?string $bladeTemplate = null,
        array $bladeData = []
    ): array {
        
        $gmail = $user->getOption('profile');

        if (!$gmail || empty($gmail['mail_password'])) {
            return self::error("User chưa cấu hình Gmail hoặc thiếu mật khẩu ứng dụng.");
        }

        // Cấu hình mailer runtime
        self::configureUserMailer($user->email, $gmail['mail_password']);

        try {
            $email = new UserHtmlMail(
                subjectLine:  $subject,
                htmlContent:  $html,
                textContent:  $body,
                template:     $bladeTemplate,
                templateData: $bladeData,
                fileAttachments: $attachments 
            );

            Mail::mailer('user_gmail')
                ->to($to)
                ->cc($cc)
                ->bcc($bcc)
                ->send($email);

            return self::success("Mail đã gửi thành công!");

        } catch (\Exception $e) {
            return self::error("Lỗi khi gửi mail: " . $e->getMessage());
        }
    }



    // ============================
    // HÀM HỖ TRỢ
    // ============================

    private static function configureUserMailer(string $email, string $appPass): void
    {
        Config::set('mail.mailers.user_gmail', [
            'transport'  => 'smtp',
            'host'       => 'smtp.gmail.com',
            'port'       => 587,
            'encryption' => 'tls',
            'username'   => $email,        // Gmail yêu cầu
            'password'   => $appPass,      // App Password
        ]);
    }

    private static function success(string $msg): array
    {
        return ['status' => 'success', 'message' => $msg];
    }

    private static function error(string $msg): array
    {
        return ['status' => 'error', 'message' => $msg];
    }
}
