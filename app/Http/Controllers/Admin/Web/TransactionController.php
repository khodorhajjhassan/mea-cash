<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private readonly WalletService $walletService)
    {
    }

    public function index()
    {
        $transactions = WalletTransaction::query()->with('wallet.user:id,name')->latest('id')->paginate(25);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function user(User $user)
    {
        $transactions = WalletTransaction::query()->whereHas('wallet', fn ($query) => $query->where('user_id', $user->id))->latest('id')->paginate(25);

        return view('admin.transactions.user', compact('transactions', 'user'));
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'not_in:0'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            $user = User::query()->findOrFail($data['user_id']);
            $amount = (float) $data['amount'];

            if ($amount > 0) {
                $this->walletService->credit($user, $amount, $data['description']);
            } else {
                $this->walletService->debit($user, abs($amount), $data['description']);
            }

            return back()->with('success', 'Wallet adjustment processed.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to process adjustment.');
        }
    }
}
