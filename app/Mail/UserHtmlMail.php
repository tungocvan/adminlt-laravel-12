<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class UserHtmlMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public ?string $htmlContent;
    public ?string $textContent;
    public ?string $template;
    public array $templateData;
    public array $fileAttachments; // ĐỔI TÊN

    public function __construct(
        string $subjectLine,
        ?string $htmlContent = null,
        ?string $textContent = null,
        ?string $template = null,
        array $templateData = [],
        array $fileAttachments = [] // ĐỔI TÊN
    ) {
        $this->subjectLine   = $subjectLine;
        $this->htmlContent   = $htmlContent;
        $this->textContent   = $textContent;
        $this->template      = $template;
        $this->templateData  = $templateData;
        $this->fileAttachments = $fileAttachments;

        $this->subject($subjectLine);
    }

    public function build()
    {
        // ✅ Render template nếu có
        if ($this->template) {
            $this->htmlContent = View::make($this->template, $this->templateData)->render();
        }

        $finalHtml = $this->htmlContent ?? ($this->textContent ?? 'No content');

        // ✅ Khởi tạo email với view raw-html
        $email = $this->view('emails.raw-html', [
            'html' => $finalHtml
        ])->subject($this->subject ?? 'No Subject');

        // ✅ Xử lý attachments
        if (!empty($this->fileAttachments)) {
            foreach ($this->fileAttachments as $file) {
                // Trường hợp file là mảng base64 ['name','content','mime']
                if (is_array($file) && isset($file['content'], $file['name'], $file['mime'])) {
                    $email->attachData(base64_decode($file['content']), $file['name'], [
                        'mime' => $file['mime'],
                    ]);
                } 
                // Trường hợp file là local path string
                elseif (is_string($file) && file_exists($file)) {
                    $email->attach($file);
                }
            }
        }

        return $email;
    }

}
