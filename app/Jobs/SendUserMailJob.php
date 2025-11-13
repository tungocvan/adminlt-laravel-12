<?php

namespace App\Jobs;

use App\Services\UserMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $to;
    public $subject;
    public $body;
    public $html;
    public $cc;
    public $bcc;
    public $attachments;
    public $bladeTemplate;
    public $bladeData;

    /**
     * Create a new job instance.
     */
    public function __construct(
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
        $this->user = $user;
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->html = $html;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->attachments = $attachments;
        $this->bladeTemplate = $bladeTemplate;
        $this->bladeData = $bladeData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        UserMailService::sendUserMail(
            $this->user,
            $this->to,
            $this->subject,
            $this->body,
            $this->html,
            $this->cc,
            $this->bcc,
            $this->attachments,
            $this->bladeTemplate,
            $this->bladeData
        );
    }
}
