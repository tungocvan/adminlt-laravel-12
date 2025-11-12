<?php

namespace App\Listeners;

use App\Events\AlertUserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Livewire\Livewire;


class DispatchAlertToLivewire
{
    /**
     * Create the event listener.
     */
    public function __construct()   
    {
        //
    }

    /**
     * Handle the event.
     */
     public function handle(AlertUserCreated $event)
        {
            // Gửi event tới component Livewire
            //Livewire::dispatch('alert-created');
            //     ->to(\Modules\Components\Livewire\NotificationsDropdown::class);
        }
}
