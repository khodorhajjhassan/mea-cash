<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\TopupRequest;
use App\Models\PaymentMethod;
use App\Notifications\UserNotification;
use App\Services\SettingsService;
use App\Services\EmailNotificationService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerDashboardController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly SettingsService $settingsService,
        private readonly EmailNotificationService $emails,
    ) {}

    /**
     * Dashboard overview: recent orders, wallet balance.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $user = auth()->user();
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

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

        $marketplaceQuery = Order::query()
            ->where('user_id', $user->id)
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to));

        $marketplaceStats = [
            'orders' => (clone $marketplaceQuery)->count(),
            'spent' => (float) (clone $marketplaceQuery)
                ->whereIn('status', ['completed', 'processing', 'pending'])
                ->sum('total_price'),
            'completed' => (clone $marketplaceQuery)->where('status', 'completed')->count(),
            'pending' => (clone $marketplaceQuery)->whereIn('status', ['pending', 'processing'])->count(),
            'products' => (clone $marketplaceQuery)->distinct('product_id')->count('product_id'),
        ];

        return view('storefront.dashboard.index', compact(
            'recentOrders',
            'balance',
            'totalOrders',
            'totalSpent',
            'marketplaceStats',
            'filters',
        ));
    }

    /**
     * Order history with pagination.
     */
    public function orders(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', 'string', Rule::in(array_map(fn (OrderStatus $status) => $status->value, OrderStatus::cases()))],
        ]);

        $orders = Order::where('user_id', auth()->id())
            ->with(['product', 'package'])
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('storefront.dashboard.orders', compact('orders', 'filters'));
    }

    /**
     * Single order detail with fulfillment data.
     */
    public function orderDetail(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['product.subcategory.productTypeDefinition:id,name,key,schema', 'product.subcategory.category', 'package', 'items', 'feedback', 'report'])
            ->firstOrFail();

        if ((int) $order->user_id !== (int) auth()->id()) {
            abort_unless((bool) auth()->user()?->is_admin, 404);

            return redirect()->route('admin.orders.show', $order);
        }

        $supportReportDelayHours = max(0, (int) $this->settingsService->get('support_report_delay_hours', 4));

        return view('storefront.dashboard.order-detail', compact('order', 'supportReportDelayHours'));
    }

    public function submitFeedback(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 404);
        abort_unless(in_array($order->status, [OrderStatus::Completed, OrderStatus::Reported], true), 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($order->feedback()->exists()) {
            return back()->with('error', $request->boolean('is_ar') ? 'تم إرسال تقييم لهذا الطلب مسبقاً.' : 'Feedback was already submitted for this order.');
        }

        $report = Feedback::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'type' => 'feedback',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', $request->boolean('is_ar') ? 'شكراً لك، تم حفظ تقييمك.' : 'Thank you, your feedback was saved.');
    }

    public function submitReport(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 404);
        abort_unless(in_array($order->status, [OrderStatus::Completed, OrderStatus::Reported], true), 403);

        if ($order->status === OrderStatus::Refunded || $order->reports()->where('status', 'refunded')->exists()) {
            return back()->with('error', $request->boolean('is_ar') ? 'لا يمكن فتح بلاغ جديد بعد استرداد الطلب.' : 'A refunded order cannot open a new support report.');
        }

        $windowHours = max(0, (int) $this->settingsService->get('support_report_delay_hours', 4));
        $expiresAt = $windowHours > 0
            ? ($order->fulfilled_at ?? $order->created_at)->copy()->addHours($windowHours)
            : null;

        if ($expiresAt !== null && now()->gt($expiresAt)) {
            return back()->with('error', $request->boolean('is_ar')
                ? 'انتهت مدة فتح بلاغ الدعم لهذا الطلب.'
                : 'The support report window for this order has expired.');
        }

        $validated = $request->validate([
            'issue_type' => ['required', 'string', Rule::in(['key_not_working', 'account_problem', 'wrong_details', 'other'])],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        if ($order->reports()->whereIn('status', ['open', 'reviewing'])->exists()) {
            return back()->with('error', $request->boolean('is_ar') ? 'يوجد بلاغ مفتوح لهذا الطلب بالفعل.' : 'A report is already open for this order.');
        }

        Feedback::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'type' => 'report',
            'rating' => 1,
            'issue_type' => $validated['issue_type'],
            'comment' => $validated['comment'],
            'status' => 'open',
        ]);

        $order->update(['status' => OrderStatus::Reported]);

        $order->loadMissing('product', 'user');
        $this->emails->toUser($order->user, [
            'en' => [
                'subject' => "Support Report Received: #{$order->order_number}",
                'title' => 'We received your report',
                'message' => 'Our support team received your order report and will review it as soon as possible.',
                'action_url' => route('store.orders.detail', $order->order_number),
                'action_text' => 'View Reported Order',
                'details' => [
                    'Order' => '#'.$order->order_number,
                    'Issue' => str_replace('_', ' ', $report->issue_type ?? ''),
                ],
            ],
            'ar' => [
                'subject' => "تم استلام البلاغ: #{$order->order_number}",
                'title' => 'تم استلام بلاغك',
                'message' => 'استلم فريق الدعم بلاغك وسيتم مراجعته في أقرب وقت ممكن.',
                'action_url' => route('store.orders.detail', $order->order_number),
                'action_text' => 'عرض الطلب',
                'details' => [
                    'الطلب' => '#'.$order->order_number,
                    'المشكلة' => str_replace('_', ' ', $report->issue_type ?? ''),
                ],
            ],
        ]);

        return back()->with('success', $request->boolean('is_ar') ? 'تم إرسال البلاغ إلى الدعم.' : 'Your report was sent to support.');
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

        $topupRequests = TopupRequest::where('user_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('storefront.dashboard.wallet', compact(
            'balance',
            'transactions',
            'topupRequests',
            'paymentMethods',
        ));
    }

    /**
     * Submit a top-up request.
     */
    public function submitTopup(Request $request)
    {
        $activeMethods = PaymentMethod::query()
            ->where('is_active', true)
            ->pluck('method')
            ->all();

        $validated = $request->validate([
            'payment_method' => ['required', 'string', Rule::in($activeMethods)],
            'amount_requested' => ['required', 'numeric', 'min:1', 'max:10000'],
            'receipt_image' => ['required', 'image', 'max:5120'],
        ]);

        // Store receipts on private disk so admin can access them through temporary signed URLs.
        $path = $request->file('receipt_image')->store('topup-receipts', 'private');

        $topup = TopupRequest::create([
            'user_id' => auth()->id(),
            'payment_method' => $validated['payment_method'],
            'amount_requested' => $validated['amount_requested'],
            'receipt_image_path' => $path,
        ]);

        auth()->user()->notify(new UserNotification([
            'type' => 'Top-Up Submitted',
            'message' => "Your top-up request #{$topup->id} was submitted and is pending review.",
            'link' => route('store.wallet'),
            'icon' => 'payments',
        ]));

        $this->emails->toUser(auth()->user(), [
            'en' => [
                'subject' => "Top-up Submitted: #{$topup->id}",
                'title' => 'Your top-up is pending',
                'message' => 'We received your wallet top-up request and it is pending admin review.',
                'action_url' => route('store.wallet'),
                'action_text' => 'View Wallet',
                'details' => [
                    'Amount' => '$'.number_format((float) $topup->amount_requested, 2),
                    'Payment method' => strtoupper($topup->payment_method),
                ],
            ],
            'ar' => [
                'subject' => "تم إرسال طلب الشحن: #{$topup->id}",
                'title' => 'طلب شحن المحفظة قيد الانتظار',
                'message' => 'استلمنا طلب شحن المحفظة وهو بانتظار مراجعة الإدارة.',
                'action_url' => route('store.wallet'),
                'action_text' => 'عرض المحفظة',
                'details' => [
                    'المبلغ' => '$'.number_format((float) $topup->amount_requested, 2),
                    'طريقة الدفع' => strtoupper($topup->payment_method),
                ],
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('storefront.wallet.topup_submitted'),
                'topup' => [
                    'id' => $topup->id,
                    'amount_requested' => number_format((float) $topup->amount_requested, 2),
                    'payment_method' => strtoupper($topup->payment_method),
                    'status' => $topup->status,
                    'status_label' => app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending',
                ],
            ], 201);
        }

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
