<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRefundedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $mailLocale;

    /**
     * Create a new message instance.
     */
    public function __construct(public \App\Models\Order $order)
    {
        $this->order->loadMissing('user');
        $this->mailLocale = $this->getMailLocale();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getMailLocale() === 'ar'
                ? 'تم استرداد الطلب: #'.($this->order->order_number ?: $this->order->id)
                : 'Order Refunded: #'.($this->order->order_number ?: $this->order->id),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.refunded',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    private function getMailLocale(): string
    {
        return $this->order->user?->preferred_language ?: app()->getLocale();
    }
}
