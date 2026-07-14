<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
        public string $mailLocale = 'en',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailLocale === 'ar'
                ? 'رمز إعادة تعيين كلمة المرور - MeaCash'
                : 'Your MeaCash password reset code',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.password-reset-otp',
        );
    }
}
