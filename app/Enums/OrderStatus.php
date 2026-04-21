<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Reported = 'reported';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Canceled = 'canceled';
}
