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
        if ($this->template) {
            $this->htmlContent = View::make($this->template, $this->templateData)->render();
        }

        $finalHtml = $this->htmlContent ?? ($this->textContent ?? 'No content'); 

        $email = $this->view('emails.raw-html', [
            'html' => $finalHtml
        ]);

        foreach ($this->fileAttachments as $file) {
            if (file_exists($file)) {
                $email->attach($file);
            }
        }

        return $email;
    }
}
