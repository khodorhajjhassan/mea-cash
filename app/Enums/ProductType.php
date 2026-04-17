<?php

namespace App\Enums;

enum ProductType: string
{
    case FixedPackage = 'fixed_package';
    case AccountTopup = 'account_topup';
    case CustomQuantity = 'custom_quantity';
    case ManualService = 'manual_service';

    public function label(): string
    {
        return match ($this) {
            self::FixedPackage => 'Key',
            self::AccountTopup => 'Account',
            self::CustomQuantity => 'Top Up',
            self::ManualService => 'Manual Service',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
