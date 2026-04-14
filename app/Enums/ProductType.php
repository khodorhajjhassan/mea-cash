<?php

namespace App\Enums;

enum ProductType: string
{
    case FixedPackage = 'fixed_package';
    case AccountTopup = 'account_topup';
    case CustomQuantity = 'custom_quantity';
}
