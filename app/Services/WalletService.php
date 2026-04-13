<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(User $user, float $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $reference): WalletTransaction {
            $wallet = Wallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'currency' => 'USD']
            );

            $wallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            $balanceBefore = (float) $wallet->balance;
            $wallet->increment('balance', $amount);

            return WalletTransaction::query()->create([
                'wallet_id' => $wallet->id,
                'type' => 'topup',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => (float) $wallet->fresh()->balance,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'description_en' => $description,
                'created_at' => now(),
            ]);
        });
    }

    public function debit(User $user, float $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $reference): WalletTransaction {
            $wallet = Wallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $wallet->balance < $amount) {
                throw new InsufficientBalanceException("Balance {$wallet->balance} is less than required {$amount}.");
            }

            $balanceBefore = (float) $wallet->balance;
            $wallet->decrement('balance', $amount);

            return WalletTransaction::query()->create([
                'wallet_id' => $wallet->id,
                'type' => 'purchase',
                'amount' => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after' => (float) $wallet->fresh()->balance,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'description_en' => $description,
                'created_at' => now(),
            ]);
        });
    }

    public function getBalance(User $user): float
    {
        return (float) $user->wallet?->balance;
    }
}
