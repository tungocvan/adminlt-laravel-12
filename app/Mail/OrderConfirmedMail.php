<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
//use App\Models\Order;
use Modules\Order\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // public function build()
    // {
    //     return $this->subject("Đơn hàng #{$this->order->id} đã xác nhận")
    //                 ->view('emails.order_confirmed')
    //                 ->with([
    //                     'order' => $this->order,
    //                     'pdfLink' => $this->order->link_download,
    //                 ]);
    // }
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
