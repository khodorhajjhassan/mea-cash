<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::query()->where('status', 'completed')->sum('total_price');
        $totalOrders = Order::query()->count();

        return view('admin.analytics.index', compact('totalRevenue', 'totalOrders'));
    }

    public function revenue(): JsonResponse
    {
        $points = collect(range(0, 29))->map(function (int $day) {
            $date = Carbon::today()->subDays(29 - $day);
            $amount = Order::query()
                ->where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total_price');

            return [
                'date' => $date->toDateString(),
                'revenue' => (float) $amount,
            ];
        });

        return response()->json($points);
    }

    public function products(): JsonResponse
    {
        $data = Product::query()
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->limit(10)
            ->get(['id', 'name_en']);

        return response()->json($data);
    }

    public function profit(): JsonResponse
    {
        $profit = Order::query()->where('status', 'completed')->sum('profit');
        $cost = Order::query()->where('status', 'completed')->sum('cost_price');

        return response()->json([
            'profit' => (float) $profit,
            'cost' => (float) $cost,
        ]);
    }
}
