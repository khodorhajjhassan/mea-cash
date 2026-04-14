<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\FulfillOrderRequest;
use App\Services\OrderFulfillmentService;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    public function __construct(private readonly OrderFulfillmentService $fulfillmentService)
    {
    }

    public function index(Request $request)
    {
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
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'status']);

        return view('admin.orders.index', compact('orders', 'filters'));
    }

    public function pending(Request $request)
    {
        $orders = Order::query()
            ->with(['user:id,name', 'product:id,name_ar,name_en,image,subcategory_id', 'product.subcategory:id,name_en', 'package:id,name_en'])
            ->whereIn('status', [OrderStatus::Pending, OrderStatus::Processing])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('order_number', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name_en', 'like', "%{$q}%"));
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q']);

        return view('admin.orders.pending', compact('orders', 'filters'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user:id,name,email,phone', 
            'product:id,name_en,image,subcategory_id,product_type', 
            'product.subcategory:id,product_type_id', 
            'product.subcategory.productTypeDefinition:id,key,schema',
            'product.formFields:id,product_id,field_key,label_en',
            'package:id,name_en',
            'items'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function status(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', new \Illuminate\Validation\Rules\Enum(OrderStatus::class)],
        ]);

        try {
            $order->update(['status' => $data['status']]);

            return back()->with('success', 'Order status updated.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to update order status.');
        }
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

    public function fail(Order $order)
    {
        try {
            $this->fulfillmentService->markAsFailed($order);

            return back()->with('success', 'Order marked as failed.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to mark order failed.');
        }
    }

    public function refund(Order $order)
    {
        try {
            $this->fulfillmentService->processRefund($order);

            return back()->with('success', 'Order marked as refunded.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to process refund.');
        }
    }
}

