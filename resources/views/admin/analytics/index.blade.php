@extends('admin.layouts.app')
@section('title','Analytics')
@section('header','Analytics')
@section('content')
<section class="panel">
    <form method="GET" action="{{ route('admin.analytics.index') }}" class="grid gap-3 md:grid-cols-4">
        <div class="field">
            <label>Start Date</label>
            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}">
        </div>
        <div class="field">
            <label>End Date</label>
            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}">
        </div>
        <div class="flex items-end gap-2 md:col-span-2">
            <button class="btn-primary" type="submit">Apply Filter</button>
            <a class="btn-ghost" href="{{ route('admin.analytics.index') }}">Reset</a>
        </div>
    </form>
</section>

<section class="grid gap-4 mt-6 md:grid-cols-2">
    <div class="stat-card">
        <p>Total Revenue</p>
        <h3>${{ number_format((float) $totalRevenue, 2) }}</h3>
    </div>
    <div class="stat-card">
        <p>Total Orders</p>
        <h3>{{ $totalOrders }}</h3>
    </div>
</section>

<section class="grid gap-4 mt-6 lg:grid-cols-2">
    <article class="panel">
        <div class="panel-head">
            <h2 class="text-base font-semibold text-slate-900">Revenue Trend</h2>
        </div>
        <div style="height:340px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </article>

    <article class="panel">
        <div class="panel-head">
            <h2 class="text-base font-semibold text-slate-900">Profit vs Cost</h2>
        </div>
        <div class="mx-auto w-full max-w-[340px]" style="height:260px;">
            <canvas id="profitChart"></canvas>
        </div>
    </article>
</section>

<section class="grid gap-4 mt-6 lg:grid-cols-2">
    <article class="panel">
        <div class="panel-head">
            <h2 class="text-base font-semibold text-slate-900">Top Products</h2>
        </div>
        <div style="height:360px;">
            <canvas id="productsChart"></canvas>
        </div>
    </article>

    <article class="panel">
        <div class="panel-head">
            <h2 class="text-base font-semibold text-slate-900">Top Users</h2>
        </div>
        <div style="height:360px;">
            <canvas id="usersChart"></canvas>
        </div>
    </article>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(() => {
    const defaultGrid = { color: 'rgba(148, 163, 184, 0.2)' };
    const defaultTicks = { color: '#334155' };
    const filters = @json($filters ?? []);
    const query = new URLSearchParams(
        Object.entries(filters).filter(([, value]) => value !== null && value !== '')
    ).toString();
    const withQuery = (url) => query ? `${url}?${query}` : url;

    const renderRevenueChart = async () => {
        const response = await fetch(withQuery('{{ route('admin.analytics.revenue') }}'), { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Failed to load revenue data');

        const rows = await response.json();
        const labels = rows.map((item) => item.date);
        const values = rows.map((item) => Number(item.revenue || 0));

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (USD)',
                    data: values,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    x: { grid: defaultGrid, ticks: { ...defaultTicks, maxRotation: 45, minRotation: 35 } },
                    y: { grid: defaultGrid, ticks: defaultTicks, beginAtZero: true }
                }
            }
        });
    };

    const renderProductsChart = async () => {
        const response = await fetch(withQuery('{{ route('admin.analytics.products') }}'), { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Failed to load products data');

        const rows = await response.json();
        const labels = rows.map((item) => item.name_en);
        const values = rows.map((item) => Number(item.orders_count || 0));

        new Chart(document.getElementById('productsChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Orders',
                    data: values,
                    backgroundColor: '#0f766e',
                    borderRadius: 8,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false }, ticks: { ...defaultTicks, maxRotation: 30, minRotation: 20 } },
                    y: { grid: defaultGrid, ticks: defaultTicks, beginAtZero: true }
                }
            }
        });
    };

    const renderUsersChart = async () => {
        const response = await fetch(withQuery('{{ route('admin.analytics.users') }}'), { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Failed to load users data');

        const rows = await response.json();
        const labels = rows.map((item) => item.name);
        const values = rows.map((item) => Number(item.total_spent || 0));

        new Chart(document.getElementById('usersChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Total Spent (USD)',
                    data: values,
                    backgroundColor: '#ea580c',
                    borderRadius: 8,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false }, ticks: { ...defaultTicks, maxRotation: 30, minRotation: 20 } },
                    y: { grid: defaultGrid, ticks: defaultTicks, beginAtZero: true }
                }
            }
        });
    };

    const renderProfitChart = async () => {
        const response = await fetch(withQuery('{{ route('admin.analytics.profit') }}'), { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Failed to load profit data');

        const data = await response.json();

        new Chart(document.getElementById('profitChart'), {
            type: 'doughnut',
            data: {
                labels: ['Profit', 'Cost'],
                datasets: [{
                    data: [Number(data.profit || 0), Number(data.cost || 0)],
                    backgroundColor: ['#16a34a', '#f59e0b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    };

    const showError = (message) => {
        const html = `<div class="panel mt-4 text-sm text-red-600">${message}</div>`;
        document.querySelector('main')?.insertAdjacentHTML('afterbegin', html);
    };

    Promise.all([renderRevenueChart(), renderProductsChart(), renderUsersChart(), renderProfitChart()])
        .catch((error) => showError(error.message || 'Failed to load analytics charts'));
})();
</script>
@endpush
