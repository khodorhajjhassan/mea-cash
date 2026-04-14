<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
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
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->value()))
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
            ->whereIn('status', ['pending', 'processing'])
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
            'status' => ['required', 'in:pending,processing,completed,failed,refunded'],
        ]);

        try {
            $order->update(['status' => $data['status']]);

            return back()->with('success', 'Order status updated.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to update order status.');
        }
    }

    public function fulfill(Request $request, Order $order)
    {
        $data = $request->validate([
            'fulfillment_type' => ['required', 'in:key,account,topup,note'],
            'keys' => ['required_if:fulfillment_type,key', 'nullable', 'string'],
            'account_user' => ['required_if:fulfillment_type,account', 'nullable', 'string'],
            'account_pass' => ['required_if:fulfillment_type,account', 'nullable', 'string'],
            'account_link' => ['nullable', 'url'],
            'transaction_id' => ['required_if:fulfillment_type,topup', 'nullable', 'string'],
            'admin_note' => ['nullable', 'string'],
            'notify_email' => ['sometimes', 'boolean'],
            'notify_whatsapp' => ['sometimes', 'boolean'],
        ]);

        try {
            DB::transaction(function () use ($order, $data): void {
                $fulfillment = [
                    'type' => $data['fulfillment_type'],
                    'admin_note' => $data['admin_note'] ?? '',
                    'data' => match ($data['fulfillment_type']) {
                        'key' => ['keys' => $data['keys']],
                        'account' => [
                            'user' => $data['account_user'],
                            'pass' => $data['account_pass'],
                            'link' => $data['account_link'],
                        ],
                        'topup' => ['transaction_id' => $data['transaction_id']],
                        default => [],
                    },
                ];

                $order->update([
                    'status' => 'completed',
                    'fulfilled_at' => now(),
                    'fulfillment_data' => array_merge($order->fulfillment_data ?? [], ['fulfillment' => $fulfillment]),
                ]);

                if (!empty($data['notify_email'])) {
                    \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderFulfilledMail($order));
                }
            });

            $response = back()->with('success', 'Order fulfilled successfully.');

            if (!empty($data['notify_whatsapp']) && $order->user?->phone) {
                $message = "Hello {$order->user->name}, your order #{$order->order_number} for {$order->product->name_en} has been fulfilled! check your dashboard for details.";
                $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $order->user->phone) . "?text=" . urlencode($message);
                
                return redirect()->away($whatsappUrl);
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
            $order->update(['status' => 'failed']);

            return back()->with('success', 'Order marked as failed.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to mark order failed.');
        }
    }

    public function refund(Order $order)
    {
        try {
            $order->update(['status' => 'refunded']);

            return back()->with('success', 'Order marked as refunded.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to process refund.');
        }
    }
}
