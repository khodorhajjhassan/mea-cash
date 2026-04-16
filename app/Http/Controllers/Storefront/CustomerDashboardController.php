<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TopupRequest;
use App\Models\PaymentMethod;
use App\Services\WalletService;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    /**
     * Dashboard overview: recent orders, wallet balance.
     */
    public function index()
    {
        $user = auth()->user();

        $recentOrders = Order::where('user_id', $user->id)
            ->with('product')
            ->latest()
            ->limit(5)
            ->get();

        $balance = $this->walletService->getBalance($user);

        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'processing', 'pending'])
            ->sum('total_price');

        return view('storefront.dashboard.index', compact(
            'recentOrders',
            'balance',
            'totalOrders',
            'totalSpent',
        ));
    }

    /**
     * Order history with pagination.
     */
    public function orders(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['product', 'package'])
            ->latest()
            ->paginate(15);

        return view('storefront.dashboard.orders', compact('orders'));
    }

    /**
     * Single order detail with fulfillment data.
     */
    public function orderDetail(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['product.subcategory.category', 'package', 'items'])
            ->firstOrFail();

        return view('storefront.dashboard.order-detail', compact('order'));
    }

    /**
     * Wallet page: balance + transactions + top-up form.
     */
    public function wallet()
    {
        $user = auth()->user();
        $balance = $this->walletService->getBalance($user);

        $transactions = $user->wallet?->transactions()
            ->latest('created_at')
            ->paginate(20) ?? collect();

        $pendingTopups = TopupRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('storefront.dashboard.wallet', compact(
            'balance',
            'transactions',
            'pendingTopups',
            'paymentMethods',
        ));
    }

    /**
     * Submit a top-up request.
     */
    public function submitTopup(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:omt,wish,usdt'],
            'amount_requested' => ['required', 'numeric', 'min:1', 'max:10000'],
            'receipt_image' => ['required', 'image', 'max:5120'],
        ]);

        // Store receipts on private disk so admin can access them through temporary signed URLs.
        $path = $request->file('receipt_image')->store('topup-receipts', 'private');

        TopupRequest::create([
            'user_id' => auth()->id(),
            'payment_method' => $validated['payment_method'],
            'amount_requested' => $validated['amount_requested'],
            'receipt_image_path' => $path,
        ]);

        return back()->with('success', __('storefront.wallet.topup_submitted'));
    }

    /**
     * Profile page.
     */
    public function profile()
    {
        return view('storefront.dashboard.profile', ['user' => auth()->user()]);
    }

    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'preferred_language' => ['required', 'in:ar,en'],
        ]);

        auth()->user()->update($validated);

        return back()->with('success', __('storefront.profile.updated'));
    }
}
