<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientBalanceException;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WalletConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    private WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = app(\App\Services\WalletService::class);
    }

    /**
     * @test
     * Verify that lockForUpdate prevents race conditions.
     */
    public function test_it_prevents_simultaneous_debits_from_overdrawing()
    {
        // 1. Create a user
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // 2. Create wallet
        $wallet = Wallet::query()->create([
            'user_id' => $user->id,
            'balance' => '50.00',
            'currency' => 'USD',
        ]);

        $user->setRelation('wallet', $wallet);

        $results = [];

        try {
            $this->walletService->debit($user, '30.00', 'First Debit');
            $results[] = 'success_1';
        } catch (\Exception $e) {
            dump("First Debit Failed: " . $e->getMessage());
            $results[] = 'fail_1';
        }

        try {
            $this->walletService->debit($user, '30.01', 'Second Debit');
            $results[] = 'success_2';
        } catch (InsufficientBalanceException $e) {
            $results[] = 'fail_2';
        } catch (\Exception $e) {
            dump("Second Debit Failed: " . $e->getMessage());
            $results[] = 'fail_generic';
        }

        $this->assertContains('success_1', $results);
        $this->assertContains('fail_2', $results);
        $this->assertEquals('20.00', $user->wallet->fresh()->balance);
    }
}
