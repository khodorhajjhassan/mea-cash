<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\NotifyAdmins;

class ContactMessage extends Model
{
    use HasFactory, NotifyAdmins;

    public function toAdminNotification(): array
    {
        return [
            'type' => 'Contact Message',
            'message' => "New message from {$this->name}: {$this->subject}",
            'link' => route('admin.contact.show', $this),
            'icon' => 'contact',
        ];
    }

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }
}
