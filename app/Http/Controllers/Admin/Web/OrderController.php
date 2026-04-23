<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\FulfillOrderRequest;
use App\Services\OrderFulfillmentService;
use App\Services\EmailNotificationService;
use App\Enums\OrderStatus;
use App\Notifications\UserNotification;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderFulfillmentService $fulfillmentService,
        private readonly EmailNotificationService $emails,
    )
    {
    }

    public function index(Request $request)
    {
        $startDate = $request->filled('from_date') ? $request->date('from_date') : now()->subMonths(2);
        $endDate = $request->filled('to_date') ? $request->date('to_date') : null;

        $orders = Order::query()
            ->with('user:id,name', 'product:id,name_en')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('order_number', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name_en', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->value))
            ->whereDate('created_at', '>=', $startDate)
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'status', 'from_date', 'to_date']);

        return view('admin.orders.index', compact('orders', 'filters'));
    }

    public function pending(Request $request)
    {
        $startDate = $request->filled('from_date') ? $request->date('from_date') : now()->subMonths(2);
        
        $orders = Order::query()
            ->pending()
            ->with(['user', 'product', 'package'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('order_number', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name_en', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->value))
            ->whereDate('created_at', '>=', $startDate)
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'status', 'from_date']);

        return view('admin.orders.pending', compact('orders', 'filters'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user:id,name,email,phone', 
            'product:id,name_en,image,subcategory_id', 
            'product.subcategory:id,product_type_id', 
            'product.subcategory.productTypeDefinition:id,key,schema',
            'package:id,name_en',
            'items'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function status(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', new \Illuminate\Validation\Rules\Enum(OrderStatus::class)],
            'notify_email' => ['sometimes', 'boolean'],
        ]);

        try {
            $order->update(['status' => $data['status']]);
            $order->loadMissing('user');

            $status = $order->status->value ?? (string) $order->status;
            $order->user?->notify(new UserNotification([
                'type' => 'Order Status',
                'message' => "Order #{$order->order_number} status changed to {$status}.",
                'link' => route('store.orders.detail', $order->order_number),
                'icon' => 'inventory_2',
            ]));

            if (!empty($data['notify_email']) && $order->user) {
                $this->emails->toUser($order->user, [
                    'en' => [
                        'subject' => "Order Status Updated: #{$order->order_number}",
                        'title' => 'Order status updated',
                        'message' => "Your order status changed to {$status}.",
                        'action_url' => route('store.orders.detail', $order->order_number),
                        'action_text' => 'View Order',
                    ],
                    'ar' => [
                        'subject' => "تم تحديث حالة الطلب: #{$order->order_number}",
                        'title' => 'تم تحديث حالة الطلب',
                        'message' => "تم تغيير حالة طلبك إلى {$status}.",
                        'action_url' => route('store.orders.detail', $order->order_number),
                        'action_text' => 'عرض الطلب',
                    ],
                ]);
            }

            return back()->with('success', 'Order status updated.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to update order status.');
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        return $this->status($request, $order);
    }

    public function fulfill(FulfillOrderRequest $request, Order $order)
    {
        try {
            $this->fulfillmentService->fulfill($order, $request->validated());

            $response = back()->with('success', 'Order fulfilled successfully.');

            if (!empty($request->notify_whatsapp) && $order->user?->phone) {
                $message = "Hello {$order->user->name}, your order #{$order->order_number} for {$order->product->name_en} has been fulfilled! check your dashboard for details.";
                $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $order->user->phone) . "?text=" . urlencode($message);
                
                $response->with('whatsapp_url', $whatsappUrl);
            }

            return $response;
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to fulfill order: ' . $exception->getMessage());
        }
    }

    public function fail(Request $request, Order $order)
    {
        $data = $request->validate([
            'notify_email' => ['sometimes', 'boolean'],
        ]);

        try {
            $this->fulfillmentService->markAsFailed($order);
            $order->loadMissing('user');
            $order->user?->notify(new UserNotification([
                'type' => 'Order Failed',
                'message' => "Order #{$order->order_number} was marked as failed.",
                'link' => route('store.orders.detail', $order->order_number),
                'icon' => 'error',
            ]));

            if (!empty($data['notify_email']) && $order->user) {
                $this->emails->toUser($order->user, [
                    'en' => [
                        'subject' => "Order Failed: #{$order->order_number}",
                        'title' => 'Order marked as failed',
                        'message' => 'Your order was marked as failed. Please contact support if you need help.',
                        'action_url' => route('store.orders.detail', $order->order_number),
                        'action_text' => 'View Order',
                    ],
                    'ar' => [
                        'subject' => "فشل الطلب: #{$order->order_number}",
                        'title' => 'تم وضع الطلب كفاشل',
                        'message' => 'تم وضع طلبك كفاشل. يرجى التواصل مع الدعم إذا كنت بحاجة للمساعدة.',
                        'action_url' => route('store.orders.detail', $order->order_number),
                        'action_text' => 'عرض الطلب',
                    ],
                ]);
            }

            return back()->with('success', 'Order marked as failed.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to mark order failed.');
        }
    }

    public function refund(Request $request, Order $order)
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
            'notify_email' => ['sometimes', 'boolean'],
            'notify_whatsapp' => ['sometimes', 'boolean'],
        ]);

        try {
            $this->fulfillmentService->processRefund(
                $order, 
                $data['notes'] ?? null, 
                !empty($data['notify_email'])
            );

            $response = back()->with('success', 'Order marked as refunded and money returned to wallet.');

            if (!empty($data['notify_whatsapp']) && $order->user?->phone) {
                $message = "Hello {$order->user->name}, your order #{$order->order_number} has been refunded. The amount of \${$order->total_price} has been returned to your wallet. " . ($data['notes'] ? "Note: {$data['notes']}" : "");
                $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $order->user->phone) . "?text=" . urlencode($message);
                
                $response->with('whatsapp_url', $whatsappUrl);
            }

            return $response;
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to process refund: ' . $exception->getMessage());
        }
    }
}
