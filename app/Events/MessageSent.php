<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // ✅ Gửi ngay lập tức
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {

        return new Channel('chat');
    }

    public function broadcastAs()
    {
        
        return 'MessageSent';
    }
}
