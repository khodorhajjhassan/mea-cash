<aside class="admin-sidebar">
    <div>
        <div class="brand-block">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">MeaCash</p>
            <h2 class="text-xl font-bold text-slate-900">Admin Panel</h2>
        </div>

        <nav class="mt-6 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>

            <p class="section-label">Catalog</p>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a>
            <a href="{{ route('admin.subcategories.index') }}" class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">Subcategories</a>
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Products</a>
            <a href="{{ route('admin.codes.index') }}" class="nav-link {{ request()->routeIs('admin.codes.*') ? 'active' : '' }}">Code inventory</a>

            <p class="section-label">Orders</p>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') || request()->routeIs('admin.orders.status') || request()->routeIs('admin.orders.refund') ? 'active' : '' }}">All orders</a>
            <a href="{{ route('admin.orders.pending') }}" class="nav-link {{ request()->routeIs('admin.orders.pending') || request()->routeIs('admin.orders.fulfill') || request()->routeIs('admin.orders.fail') ? 'active' : '' }}">Pending fulfillment</a>

            <p class="section-label">Wallet</p>
            <a href="{{ route('admin.topups.index') }}" class="nav-link {{ request()->routeIs('admin.topups.*') ? 'active' : '' }}">Top-up requests</a>
            <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">Transactions</a>

            <p class="section-label">Users</p>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.update') || request()->routeIs('admin.users.toggle') || request()->routeIs('admin.users.credit') ? 'active' : '' }}">All users</a>
            <a href="{{ route('admin.users.vip') }}" class="nav-link {{ request()->routeIs('admin.users.vip') ? 'active' : '' }}">VIP customers</a>

            <p class="section-label">Finance</p>
            <a href="{{ route('admin.payment-methods.index') }}" class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">Payment methods</a>
            <a href="{{ route('admin.suppliers.index') }}" class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">Suppliers</a>

            <p class="section-label">Analytics</p>
            <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">Analytics</a>

            <p class="section-label">Support</p>
            <a href="{{ route('admin.contact.index') }}" class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">Contact messages</a>
            <a href="{{ route('admin.feedback.index') }}" class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">Feedback</a>

            <p class="section-label">System</p>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Settings</a>
        </nav>
    </div>

    <div class="text-xs text-slate-500">
        <p>Modern catalog control with optimized media and safe CRUD workflows.</p>
    </div>
</aside>
