<?php

namespace App\Events;

use App\Models\AlertUser;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class AlertUserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alert;

    /**
     * Tạo event với AlertUser instance.
     */
    public function __construct(AlertUser $alert)
    {
        $this->alert = $alert;
    }
}
