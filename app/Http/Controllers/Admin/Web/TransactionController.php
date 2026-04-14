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
        $startDate = $request->filled('from_date') ? $request->date('from_date') : now()->subMonths(2);
        $endDate = $request->filled('to_date') ? $request->date('to_date') : null;

        $transactions = WalletTransaction::query()
            ->with('wallet.user:id,name', 'processor:id,name')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('description_en', 'like', "%{$q}%")
                        ->orWhere('description_ar', 'like', "%{$q}%")
                        ->orWhereHas('wallet.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->value()))
            ->whereDate('created_at', '>=', $startDate)
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $filters = $request->only(['q', 'type', 'from_date', 'to_date']);

        return view('admin.transactions.index', compact('transactions', 'filters'));
    }

    public function show(WalletTransaction $transaction)
    {
        $transaction->load(['wallet.user', 'processor', 'reference']);

        return view('admin.transactions.show', compact('transaction'));
    }

    public function user(Request $request, User $user)
    {
        $transactions = WalletTransaction::query()
            ->whereHas('wallet', fn ($query) => $query->where('user_id', $user->id))
            ->with('processor:id,name')
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
                $this->walletService->credit($user, $amount, $data['description'], null, auth()->id());
            } else {
                $this->walletService->debit($user, abs($amount), $data['description'], null, auth()->id());
            }

            return back()->with('success', 'Wallet adjustment processed.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to process adjustment: ' . $exception->getMessage());
        }
    }
}
