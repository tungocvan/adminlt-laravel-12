<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserCreate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    /**
     * Create a new event instance.
     */
    public function __construct($user)
    {
        $this->user = $user ;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('users'),
        ];
    }
    public function broadcastAs()
    {
        return 'create';
    }
    public function broadcastWith(): array
    {
        return [
            'message' => "[{$this->user->created_at}] New User Received with email '{$this->user->email}'."
        ];
    }
}
