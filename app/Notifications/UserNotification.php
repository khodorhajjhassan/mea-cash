<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly array $data)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->data['type'] ?? 'Notification',
            'message' => $this->data['message'] ?? '',
            'link' => $this->data['link'] ?? null,
            'icon' => $this->data['icon'] ?? 'notifications',
        ];
    }
}
