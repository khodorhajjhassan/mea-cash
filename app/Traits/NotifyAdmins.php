<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

trait NotifyAdmins
{
    protected static function bootNotifyAdmins(): void
    {
        static::created(function ($model) {
            $admins = User::query()->where('is_admin', true)->get();
            if ($admins->isEmpty()) {
                return;
            }

            $payload = $model->toAdminNotification();
            Notification::send($admins, new AdminNotification($payload));
        });
    }

    abstract public function toAdminNotification(): array;
}
