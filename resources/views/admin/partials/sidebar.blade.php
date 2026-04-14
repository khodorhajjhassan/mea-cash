<aside class="admin-sidebar">
    <div>
        <div class="brand-block">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">MeaCash</p>
            <h2 class="text-xl font-bold text-slate-900">Admin Panel</h2>
        </div>

        <nav class="mt-6 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">{{ __('admin.sidebar.dashboard') }}</a>

            <p class="section-label">{{ __('admin.sidebar.catalog') }}</p>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">{{ __('admin.sidebar.categories') }}</a>
            <a href="{{ route('admin.subcategories.index') }}" class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">{{ __('admin.sidebar.subcategories') }}</a>
            <a href="{{ route('admin.product-types.index') }}" class="nav-link {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">{{ __('admin.sidebar.product_types') }}</a>
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">{{ __('admin.sidebar.products') }}</a>

            <p class="section-label">{{ __('admin.sidebar.orders_group') }}</p>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') || request()->routeIs('admin.orders.status') || request()->routeIs('admin.orders.refund') ? 'active' : '' }}">{{ __('admin.sidebar.all_orders') }}</a>
            <a href="{{ route('admin.orders.pending') }}" class="nav-link {{ request()->routeIs('admin.orders.pending') || request()->routeIs('admin.orders.fulfill') || request()->routeIs('admin.orders.fail') ? 'active' : '' }}">{{ __('admin.sidebar.pending_orders') }}</a>

            <p class="section-label">{{ __('admin.sidebar.wallet_group') }}</p>
            <a href="{{ route('admin.topups.index') }}" class="nav-link {{ request()->routeIs('admin.topups.*') ? 'active' : '' }}">{{ __('admin.sidebar.topups') }}</a>
            <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">{{ __('admin.sidebar.transactions') }}</a>

            <p class="section-label">{{ __('admin.sidebar.users_group') }}</p>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.update') || request()->routeIs('admin.users.edit') || request()->routeIs('admin.users.credit') ? 'active' : '' }}">{{ __('admin.sidebar.all_users') }}</a>

            <p class="section-label">{{ __('admin.sidebar.finance_group') }}</p>
            <a href="{{ route('admin.payment-methods.index') }}" class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">{{ __('admin.sidebar.payment_methods') }}</a>
            <a href="{{ route('admin.suppliers.index') }}" class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">{{ __('admin.sidebar.suppliers') }}</a>

            <p class="section-label">{{ __('admin.sidebar.analytics') }}</p>
            <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">{{ __('admin.sidebar.analytics') }}</a>

            <p class="section-label">{{ __('admin.sidebar.support_group') }}</p>
            <a href="{{ route('admin.contact.index') }}" class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">{{ __('admin.sidebar.messages') }}</a>
            <a href="{{ route('admin.feedback.index') }}" class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">{{ __('admin.sidebar.feedback') }}</a>

            <p class="section-label">{{ __('admin.sidebar.system_group') }}</p>
            <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">{{ __('admin.sidebar.alerts') }}</a>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">{{ __('admin.sidebar.settings') }}</a>
        </nav>
    </div>

    <div class="text-xs text-slate-500">
        <p>Modern catalog control with optimized media and safe CRUD workflows.</p>
    </div>
</aside>
