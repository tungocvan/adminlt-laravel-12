<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Modules\Order\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

 
    public function build()
    {
       

        return $this->subject("Đơn hàng #{$this->order->id} đã xác nhận")
                    ->markdown('emails.orders.confirmed') // ✅ dùng markdown thay vì view
                    ->with([
                        'order' => $this->order,            
                        'pdfLink' => asset("storage/{$this->order->link_download}"),
                    ]);
    }

}
