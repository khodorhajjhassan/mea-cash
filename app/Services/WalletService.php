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
    public function credit(User $user, string $amount, string $description, ?Model $reference = null, ?int $processedBy = null, \App\Enums\WalletTransactionType $type = \App\Enums\WalletTransactionType::Topup): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $reference, $processedBy, $type): WalletTransaction {
            $wallet = Wallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => '0.00', 'currency' => 'USD']
            );

            $wallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            $balanceBefore = $wallet->balance;
            $wallet->increment('balance', $amount);

            return WalletTransaction::query()->create([
                'wallet_id' => $wallet->id,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->fresh()->balance,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'description_en' => $description,
                'processed_by' => $processedBy,
                'created_at' => now(),
            ]);
        });
    }

    public function debit(User $user, string $amount, string $description, ?Model $reference = null, ?int $processedBy = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $reference, $processedBy): WalletTransaction {
            $wallet = Wallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (bccomp((string)$wallet->balance, (string)$amount, 2) === -1) {
                throw new InsufficientBalanceException("Balance {$wallet->balance} is less than required {$amount}.");
            }

            $balanceBefore = $wallet->balance;
            $wallet->decrement('balance', $amount);

            return WalletTransaction::query()->create([
                'wallet_id' => $wallet->id,
                'type' => \App\Enums\WalletTransactionType::Purchase,
                'amount' => "-{$amount}",
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->fresh()->balance,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'description_en' => $description,
                'processed_by' => $processedBy,
                'created_at' => now(),
            ]);
        });
    }

    public function getBalance(User $user): string
    {
        return (string) ($user->wallet?->balance ?? '0.00');
    }
}
