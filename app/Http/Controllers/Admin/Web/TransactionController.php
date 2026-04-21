<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Order;
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

        $transactionQuery = WalletTransaction::query()
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
            ->when($request->string('direction')->value() === 'in', fn ($query) => $query->where('amount', '>', 0))
            ->when($request->string('direction')->value() === 'out', fn ($query) => $query->where('amount', '<', 0))
            ->whereDate('created_at', '>=', $startDate)
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate));

        $summaryQuery = clone $transactionQuery;

        $transactions = $transactionQuery
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $orderQuery = Order::query()
            ->whereDate('created_at', '>=', $startDate)
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate));

        $completedOrders = (clone $orderQuery)->where('status', OrderStatus::Completed->value);
        $refundedOrders = (clone $orderQuery)->where('status', OrderStatus::Refunded->value);
        $pendingOrders = (clone $orderQuery)->whereIn('status', [OrderStatus::Pending->value, OrderStatus::Processing->value]);

        $walletIn = (float) (clone $summaryQuery)->where('amount', '>', 0)->sum('amount');
        $walletOut = abs((float) (clone $summaryQuery)->where('amount', '<', 0)->sum('amount'));
        $grossProfit = (float) $completedOrders->sum('profit');

        $summary = [
            'wallet_in' => $walletIn,
            'wallet_out' => $walletOut,
            'net_wallet_flow' => $walletIn - $walletOut,
            'topups' => (float) (clone $summaryQuery)->where('type', 'topup')->where('amount', '>', 0)->sum('amount'),
            'purchases' => abs((float) (clone $summaryQuery)->where('type', 'purchase')->where('amount', '<', 0)->sum('amount')),
            'refunds' => (float) (clone $summaryQuery)->where('type', 'refund')->sum('amount'),
            'adjustments' => (float) (clone $summaryQuery)->where('type', 'admin_adjustment')->sum('amount'),
            'revenue' => (float) (clone $orderQuery)->where('status', OrderStatus::Completed->value)->sum('total_price'),
            'cost' => (float) (clone $orderQuery)->where('status', OrderStatus::Completed->value)->sum('cost_price'),
            'profit' => $grossProfit,
            'loss' => $grossProfit < 0 ? abs($grossProfit) : 0.0,
            'refunded_order_value' => (float) $refundedOrders->sum('total_price'),
            'pending_order_value' => (float) $pendingOrders->sum('total_price'),
        ];

        $filters = $request->only(['q', 'type', 'direction', 'from_date', 'to_date']);

        return view('admin.transactions.index', compact('transactions', 'filters', 'summary'));
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
