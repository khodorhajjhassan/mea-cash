<?php

namespace App\Enums;

enum FulfillmentType: string
{
    case Key = 'key';
    case Account = 'account';
    case Topup = 'topup';
    case Note = 'note';
}
