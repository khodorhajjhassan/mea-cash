<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\AdminNotification;
use App\Services\EmailNotificationService;
use Illuminate\Support\Facades\Notification;

trait NotifyAdmins
{
    protected static function bootNotifyAdmins(): void
    {
        static::created(function ($model) {
            $admins = User::query()
                ->where(function ($query): void {
                    $query->where('is_admin', true)
                        ->orWhereHas('roles', fn ($roleQuery) => $roleQuery->whereIn('name', ['admin', 'super-admin']));
                })
                ->where('is_active', true)
                ->get();

            if ($admins->isEmpty()) {
                return;
            }

            $payload = $model->toAdminNotification();
            Notification::send($admins, new AdminNotification($payload));
            app(EmailNotificationService::class)->toAdmins([
                'en' => [
                    'subject' => $payload['type'] ?? 'Admin Notification',
                    'title' => $payload['type'] ?? 'Admin Notification',
                    'message' => $payload['message'] ?? '',
                    'action_url' => $payload['link'] ?? null,
                    'action_text' => 'Open in Admin',
                ],
                'ar' => [
                    'subject' => $payload['type'] ?? 'تنبيه إداري',
                    'title' => $payload['type'] ?? 'تنبيه إداري',
                    'message' => $payload['message'] ?? '',
                    'action_url' => $payload['link'] ?? null,
                    'action_text' => 'فتح في الإدارة',
                ],
            ]);
        });
    }

    abstract public function toAdminNotification(): array;
}
