<?php
// MODULE: Proposal
// STEP 6: NOTIFICATION (EMAIL)
// Laravel 12 | Livewire 3.1

/* =====================================================
 | NOTIFICATION: ProposalCreatedNotification
 ===================================================== */

namespace Modules\Proposal\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Proposal\Models\Proposal;

class ProposalCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Proposal $proposal) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Có đề xuất mới cần duyệt')
            ->line('Một đề xuất mới đã được gửi.')
            ->line('Tiêu đề: ' . $this->proposal->title)
            ->action('Xem đề xuất', url('/admin/proposals/' . $this->proposal->id));
    }
}

/* =====================================================
 | NOTIFICATION: ProposalApprovedNotification
 ===================================================== */

class ProposalApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Proposal $proposal) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Đề xuất đã được duyệt')
            ->line('Đề xuất của bạn đã được duyệt.')
            ->line('Tiêu đề: ' . $this->proposal->title)
            ->action('Xem chi tiết', url('/admin/proposals/' . $this->proposal->id));
    }
}

/* =====================================================
 | NOTIFICATION: ProposalRejectedNotification
 ===================================================== */

class ProposalRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Proposal $proposal) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Đề xuất bị từ chối')
            ->line('Đề xuất của bạn đã bị từ chối và cần chỉnh sửa.')
            ->line('Tiêu đề: ' . $this->proposal->title)
            ->action('Xem đề xuất', url('/admin/proposals/' . $this->proposal->id));
    }
}

/* =====================================================
 | SERVICE UPDATE: ProposalService (excerpt)
 ===================================================== */

// use Illuminate\Support\Facades\Notification;
// use Modules\Proposal\Notifications\{
//     ProposalCreatedNotification,
//     ProposalApprovedNotification,
//     ProposalRejectedNotification
// };

// After create(): notify first approver role
// After approve(): notify creator or next approver
// After reject(): notify creator

