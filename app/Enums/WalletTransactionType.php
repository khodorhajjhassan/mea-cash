<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case Topup = 'topup';
    case Purchase = 'purchase';
    case Refund = 'refund';
    case AdminAdjustment = 'admin_adjustment';
}
