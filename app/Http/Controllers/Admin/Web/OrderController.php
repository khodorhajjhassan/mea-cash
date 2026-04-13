<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()->with('user:id,name', 'product:id,name_en')->latest('id')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function pending()
    {
        $orders = Order::query()->with('user:id,name', 'product:id,name_en')->whereIn('status', ['pending', 'processing'])->latest('id')->paginate(20);

        return view('admin.orders.pending', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user:id,name,email', 'product:id,name_en', 'items']);

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

    public function fulfill(Order $order)
    {
        try {
            $order->update([
                'status' => 'completed',
                'fulfilled_at' => now(),
            ]);

            return back()->with('success', 'Order marked as fulfilled.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to fulfill order.');
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
