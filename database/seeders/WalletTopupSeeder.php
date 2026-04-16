<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Database\Seeder;

class WalletTopupSeeder extends Seeder
{
    public function __construct(private readonly WalletService $walletService) {}

    public function run(): void
    {
        $user = User::where('email', 'test@meacash.com')->first();
        if ($user) {
            $this->walletService->credit(
                user: $user,
                amount: '500.00',
                description: 'Demo top-up for testing storefront'
            );
        }

        $admin = User::where('email', 'admin@mouradvalley.com')->first();
        if ($admin) {
            $this->walletService->credit(
                user: $admin,
                amount: '1000.00',
                description: 'Admin balance top-up'
            );
        }
    }
}
