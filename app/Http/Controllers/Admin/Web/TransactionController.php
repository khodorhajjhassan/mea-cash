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

    public function index(Request $request)
    {
        $transactions = WalletTransaction::query()
            ->with('wallet.user:id,name')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('description_en', 'like', "%{$q}%")
                        ->orWhere('description_ar', 'like', "%{$q}%")
                        ->orWhereHas('wallet.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->value()))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $filters = $request->only(['q', 'type']);

        return view('admin.transactions.index', compact('transactions', 'filters'));
    }

    public function user(Request $request, User $user)
    {
        $transactions = WalletTransaction::query()
            ->whereHas('wallet', fn ($query) => $query->where('user_id', $user->id))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('description_en', 'like', "%{$q}%")
                        ->orWhere('description_ar', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->value()))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $filters = $request->only(['q', 'type']);

        return view('admin.transactions.user', compact('transactions', 'user', 'filters'));
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
