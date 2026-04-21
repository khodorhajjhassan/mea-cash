<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        [$startAt, $endAt, $hasDateFilter] = $this->resolveDateRange($request);

        $ordersQuery = Order::query();
        $completedOrdersQuery = Order::query()->where('status', \App\Enums\OrderStatus::Completed);
        
        $this->applyDateRange($ordersQuery, $startAt, $endAt, $hasDateFilter);
        $this->applyDateRange($completedOrdersQuery, $startAt, $endAt, $hasDateFilter);

        $totalRevenue = (float) $completedOrdersQuery->sum('total_price');
        $totalProfit = (float) $completedOrdersQuery->sum('profit');
        $totalOrders = (int) $ordersQuery->count();

        $filters = [
            'start_date' => $startAt?->toDateString(),
            'end_date' => $endAt?->toDateString(),
        ];

        return view('admin.analytics.index', compact('totalRevenue', 'totalProfit', 'totalOrders', 'filters'));
    }

    public function revenue(Request $request): JsonResponse
    {
        [$startAt, $endAt] = $this->resolveDateRange($request);
        
        // Use database grouping for efficiency instead of a PHP loop
        $data = Order::query()
            ->where('status', \App\Enums\OrderStatus::Completed)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing days with zero revenue to ensure a smooth chart scale
        $days = collect(CarbonPeriod::create($startAt->startOfDay(), $endAt->startOfDay()));
        $points = $days->map(function ($date) use ($data) {
            $dateStr = $date->toDateString();
            $row = $data->firstWhere('date', $dateStr);
            
            return [
                'date' => $dateStr,
                'revenue' => (float) ($row->revenue ?? 0),
            ];
        });

        return response()->json($points);
    }

    public function products(Request $request): JsonResponse
    {
        [$startAt, $endAt] = $this->resolveDateRange($request);

        // Fix: Only count completed orders in product analytics
        $data = Product::query()->withCount([
            'orders as orders_count' => function ($query) use ($startAt, $endAt): void {
                $query->where('status', \App\Enums\OrderStatus::Completed)
                      ->whereBetween('created_at', [$startAt, $endAt]);
            },
        ])
            ->orderByDesc('orders_count')
            ->limit(10)
            ->get(['id', 'name_en']);

        return response()->json($data);
    }

    public function profit(Request $request): JsonResponse
    {
        [$startAt, $endAt] = $this->resolveDateRange($request);

        $profitQuery = Order::query()->where('status', \App\Enums\OrderStatus::Completed);
        $this->applyDateRange($profitQuery, $startAt, $endAt, true);

        $profit = $profitQuery->sum('profit');
        $cost = $profitQuery->sum('cost_price');

        return response()->json([
            'profit' => (float) $profit,
            'cost' => (float) $cost,
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        [$startAt, $endAt] = $this->resolveDateRange($request);

        $completedOrdersFilter = function ($query) use ($startAt, $endAt): void {
            $query->where('status', \App\Enums\OrderStatus::Completed)
                  ->whereBetween('created_at', [$startAt, $endAt]);
        };

        $data = User::query()
            ->where('is_admin', false)
            ->whereHas('orders', $completedOrdersFilter)
            ->select(['id', 'name'])
            ->withCount([
                'orders as orders_count' => $completedOrdersFilter,
            ])
            ->withSum([
                'orders as total_spent' => $completedOrdersFilter,
            ], 'total_price')
            ->orderByDesc('orders_count')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return response()->json($data);
    }

    private function resolveDateRange(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $hasDateFilter = !empty($validated['start_date']) || !empty($validated['end_date']);

        $end = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'])
            : Carbon::today();

        $start = !empty($validated['start_date'])
            ? Carbon::parse($validated['start_date'])
            : ($hasDateFilter ? $end->copy()->subDays(29) : Carbon::today()->subDays(29));

        if ($start->diffInDays($end) > 180) {
            $start = $end->copy()->subDays(180);
        }

        return [$start->startOfDay(), $end->endOfDay(), $hasDateFilter];
    }

    private function applyDateRange($query, ?Carbon $startAt, ?Carbon $endAt, bool $hasDateFilter): void
    {
        if (!$hasDateFilter || !$startAt || !$endAt) {
            return;
        }

        $query->whereBetween('created_at', [$startAt, $endAt]);
    }
}
