<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Tên channel để broadcast (kênh công khai).
     */
    public function broadcastOn(): array
    {
        return [new Channel('users')];
    }

    /**
     * Dữ liệu gửi đi cho frontend khi broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => 'oki',          
        ];
    }

    /**
     * Tên của event gửi đi (mặc định là tên class).
     */
    public function broadcastAs(): string
    {
        return 'UserRegistered';
    }
}
