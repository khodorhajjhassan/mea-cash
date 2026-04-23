<?php

namespace App\Enums;

use App\Models\ProductType as ProductTypeModel;

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

    public static function fromTemplate(?ProductTypeModel $template): self
    {
        if (! $template) {
            return self::FixedPackage;
        }

        $key = strtolower((string) $template->key);
        $modes = collect($template->schema['modes'] ?? [])
            ->filter(fn ($mode) => is_string($mode) && $mode !== '')
            ->map(fn (string $mode) => strtolower($mode))
            ->values()
            ->all();

        return match (true) {
            in_array($key, ['account-id-required', 'account-login-credentials'], true) => self::AccountTopup,
            in_array($key, ['quantity-or-price', 'biggoexample'], true) => self::CustomQuantity,
            in_array($key, ['package-only', 'final-package-only'], true) => self::FixedPackage,
            in_array('quantity', $modes, true) => self::CustomQuantity,
            in_array('account', $modes, true) => self::AccountTopup,
            in_array('package', $modes, true) => self::FixedPackage,
            default => self::FixedPackage,
        };
    }
}
