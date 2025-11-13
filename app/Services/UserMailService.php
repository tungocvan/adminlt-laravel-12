<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\View;

// Symfony Mime Parts
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Part\HtmlPart;

class UserMailService
{
    /**
     * Gửi mail bằng Gmail của user, fallback khi HtmlPart không tồn tại
     *
     * @param \App\Models\User $user
     * @param string|array $to
     * @param string $subject
     * @param string|null $body
     * @param string|null $html
     * @param array $cc
     * @param array $bcc
     * @param array $attachments
     * @param string|null $bladeTemplate
     * @param array $bladeData
     *
     * @return array
     */
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
    ) {
        $gmail = $user->getOption('gmail');

        if (!$gmail || empty($gmail['password'])) {
            return [
                'status' => 'error',
                'message' => 'User chưa cấu hình Gmail hoặc thiếu mật khẩu ứng dụng.'
            ];
        }

        // Cấu hình mailer runtime cho user
        Config::set('mail.mailers.user_gmail', [
            'transport'  => 'smtp',
            'host'       => 'smtp.gmail.com',
            'port'       => 587,
            'encryption' => 'tls',
            'username'   => $gmail['email'],
            'password'   => $gmail['password'],
            'timeout'    => null,
            'auth_mode'  => null,
        ]);

        try {
            Mail::mailer('user_gmail')->send([], [], function (Message $message) use (
                $to, $subject, $body, $html, $cc, $bcc, $attachments, $gmail, $bladeTemplate, $bladeData
            ) {
                $message->to($to);
                if (!empty($cc)) $message->cc($cc);
                if (!empty($bcc)) $message->bcc($bcc);

                $message->from($gmail['email'], $gmail['name'] ?? $gmail['email']);
                $message->subject($subject);

                // Nội dung mail
                $htmlContent = null;
                if ($bladeTemplate) {
                    $htmlContent = View::make($bladeTemplate, $bladeData)->render();
                } elseif ($html) {
                    $htmlContent = $html;
                }

                // Chọn Part phù hợp
                if (class_exists(HtmlPart::class) && $htmlContent) {
                    $message->setBody(new HtmlPart($htmlContent, 'utf-8'));
                } elseif ($htmlContent) {
                    // fallback: dùng TextPart nếu HtmlPart không tồn tại
                    $message->setBody(new TextPart($htmlContent, 'utf-8'));
                } elseif ($body) {
                    $message->setBody(new TextPart($body, 'utf-8'));
                } else {
                    $message->setBody(new TextPart('No content', 'utf-8'));
                }

                // Đính kèm
                foreach ($attachments as $filePath) {
                    if (file_exists($filePath)) {
                        $message->attach($filePath);
                    }
                }
            });

            return [
                'status' => 'success',
                'message' => 'Mail đã gửi thành công đến: ' . (is_array($to) ? implode(', ', $to) : $to)
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Lỗi khi gửi mail: ' . $e->getMessage()
            ];
        }
    }
}
