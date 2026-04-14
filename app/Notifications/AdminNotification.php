<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    use Queueable;

    public function __construct(protected array $data)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->data['type'] ?? 'info',
            'message' => $this->data['message'] ?? '',
            'link' => $this->data['link'] ?? null,
            'icon' => $this->data['icon'] ?? 'bell',
        ];
    }
}
