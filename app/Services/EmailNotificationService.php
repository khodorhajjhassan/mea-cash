<?php

namespace App\Services;

use App\Mail\GenericNotificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailNotificationService
{
    public function toUser(User $user, array $content): void
    {
        if (! $user->email) {
            return;
        }

        $locale = $user->preferred_language ?: app()->getLocale();
        $payload = $content[$locale] ?? $content['en'] ?? reset($content);

        $this->send($user->email, $payload, $locale);
    }

    public function toAdmins(array $content): void
    {
        $payload = $content['en'] ?? reset($content);

        User::query()
            ->where(function ($query): void {
                $query->where('is_admin', true)
                    ->orWhereHas('roles', fn ($roleQuery) => $roleQuery->whereIn('name', ['admin', 'super-admin']));
            })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get()
            ->unique('email')
            ->each(fn (User $admin) => $this->send($admin->email, $payload, 'en'));
    }

    private function send(string $email, array $payload, string $locale): void
    {
        try {
            Mail::to($email)->queue(new GenericNotificationMail(
                subjectLine: $payload['subject'] ?? $payload['title'] ?? config('app.name'),
                title: $payload['title'] ?? $payload['subject'] ?? config('app.name'),
                message: $payload['message'] ?? '',
                mailLocale: $locale,
                actionUrl: $payload['action_url'] ?? null,
                actionText: $payload['action_text'] ?? null,
                details: $payload['details'] ?? [],
            ));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
