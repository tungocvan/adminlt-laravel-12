<?php

namespace App\Mail;

//use App\Models\Order;
use Modules\Order\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        
        return $this->subject('Xác nhận đơn hàng #' . $this->order->id)
                    ->markdown('emails.orders.created');
    }
    
}
