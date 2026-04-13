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
        $completedOrdersQuery = Order::query()->where('status', 'completed');
        $this->applyDateRange($ordersQuery, $startAt, $endAt, $hasDateFilter);
        $this->applyDateRange($completedOrdersQuery, $startAt, $endAt, $hasDateFilter);

        $totalRevenue = (float) $completedOrdersQuery->sum('total_price');
        $totalOrders = (int) $ordersQuery->count();

        $filters = [
            'start_date' => $startAt?->toDateString(),
            'end_date' => $endAt?->toDateString(),
        ];

        return view('admin.analytics.index', compact('totalRevenue', 'totalOrders', 'filters'));
    }

    public function revenue(Request $request): JsonResponse
    {
        [$startAt, $endAt, $hasDateFilter] = $this->resolveDateRange($request);
        if (!$hasDateFilter) {
            $endAt = Carbon::today()->endOfDay();
            $startAt = Carbon::today()->subDays(29)->startOfDay();
        }
        $start = $startAt->copy()->startOfDay();
        $end = $endAt->copy()->startOfDay();

        $points = collect(CarbonPeriod::create($start, $end))->map(function ($date) use ($startAt, $endAt) {
            $amount = Order::query()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startAt, $endAt])
                ->whereDate('created_at', $date)
                ->sum('total_price');

            return [
                'date' => $date->toDateString(),
                'revenue' => (float) $amount,
            ];
        });

        return response()->json($points);
    }

    public function products(Request $request): JsonResponse
    {
        [$startAt, $endAt, $hasDateFilter] = $this->resolveDateRange($request);

        $data = Product::query()->withCount([
            'orders as orders_count' => function ($query) use ($startAt, $endAt, $hasDateFilter): void {
                if ($hasDateFilter) {
                    $query->whereBetween('created_at', [$startAt, $endAt]);
                }
            },
        ])
            ->orderByDesc('orders_count')
            ->limit(10)
            ->get(['id', 'name_en']);

        return response()->json($data);
    }

    public function profit(Request $request): JsonResponse
    {
        [$startAt, $endAt, $hasDateFilter] = $this->resolveDateRange($request);

        $profitQuery = Order::query()->where('status', 'completed');
        $costQuery = Order::query()->where('status', 'completed');
        $this->applyDateRange($profitQuery, $startAt, $endAt, $hasDateFilter);
        $this->applyDateRange($costQuery, $startAt, $endAt, $hasDateFilter);

        $profit = $profitQuery->sum('profit');
        $cost = $costQuery->sum('cost_price');

        return response()->json([
            'profit' => (float) $profit,
            'cost' => (float) $cost,
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        [$startAt, $endAt, $hasDateFilter] = $this->resolveDateRange($request);

        $completedOrdersFilter = function ($query) use ($startAt, $endAt, $hasDateFilter): void {
            $query->where('status', 'completed');
            if ($hasDateFilter) {
                $query->whereBetween('created_at', [$startAt, $endAt]);
            }
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

        if (empty($validated['start_date']) && empty($validated['end_date'])) {
            return [null, null, false];
        }

        $start = !empty($validated['start_date'])
            ? Carbon::parse($validated['start_date'])
            : Carbon::parse($validated['end_date'])->subDays(29);
        $end = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'])
            : Carbon::today();

        if ($start->diffInDays($end) > 180) {
            $start = $end->copy()->subDays(180);
        }

        return [$start->startOfDay(), $end->endOfDay(), true];
    }

    private function applyDateRange($query, ?Carbon $startAt, ?Carbon $endAt, bool $hasDateFilter): void
    {
        if (!$hasDateFilter || !$startAt || !$endAt) {
            return;
        }

        $query->whereBetween('created_at', [$startAt, $endAt]);
    }
}
