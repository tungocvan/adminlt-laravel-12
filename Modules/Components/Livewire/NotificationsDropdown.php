<?php

namespace Modules\Components\Livewire;

use Livewire\Component;
use App\Models\AlertUser;
use Illuminate\Support\Facades\Auth;

class NotificationsDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

    protected $listeners = [
        'alert-created' => 'loadNotifications', // lắng nghe sự kiện nội bộ
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (!Auth::check()) {
            $this->notifications = [];
            $this->unreadCount = 0;
            return;
        }

        $userId = Auth::id();

        $this->notifications = AlertUser::query()
            ->where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        $this->unreadCount = $this->notifications->where('is_read', false)->count();
    }

    public function markAsRead($alertId)
    {
        $alert = AlertUser::where('id', $alertId)
            ->where('user_id', Auth::id())
            ->first();

        if ($alert) {
            $alert->update(['is_read' => true]);
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('Components::livewire.notifications-dropdown');
    }
}
