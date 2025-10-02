<?php

namespace App\Mail;

use App\Models\ContactOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactOrder $order;

    public function __construct(ContactOrder $order)
    {
        $this->order = $order;
    }

    
    public function build()
    {
      //  dd($this->order);
        return $this->subject("Liên hệ mới từ {$this->order->full_name}")
                    ->markdown('emails.contact-order', [
                        'order' => $this->order
                    ]);
    }

}
