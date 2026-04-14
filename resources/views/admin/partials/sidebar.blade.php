<aside class="admin-sidebar overflow-y-auto ">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between lg:block">
            <div class="brand-block flex-1">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">MeaCash</p>
                <h2 class="text-xl font-bold text-slate-900">Admin Panel</h2>
            </div>
            <button id="sidebar-close" class="p-2 text-slate-400 hover:text-slate-900 lg:hidden"
                aria-label="Close Menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="mt-6 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">{{ __('admin.sidebar.dashboard') }}</a>

            <p class="section-label">{{ __('admin.sidebar.catalog') }}</p>
            <a href="{{ route('admin.categories.index') }}"
                class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">{{ __('admin.sidebar.categories') }}</a>
            <a href="{{ route('admin.subcategories.index') }}"
                class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">{{ __('admin.sidebar.subcategories') }}</a>
            <a href="{{ route('admin.product-types.index') }}"
                class="nav-link {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">{{ __('admin.sidebar.product_types') }}</a>
            <a href="{{ route('admin.products.index') }}"
                class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">{{ __('admin.sidebar.products') }}</a>

            <p class="section-label">{{ __('admin.sidebar.orders_group') }}</p>
            <a href="{{ route('admin.orders.index') }}"
                class="nav-link {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') || request()->routeIs('admin.orders.status') || request()->routeIs('admin.orders.refund') ? 'active' : '' }}">{{ __('admin.sidebar.all_orders') }}</a>
            <a href="{{ route('admin.orders.pending') }}"
                class="nav-link {{ request()->routeIs('admin.orders.pending') || request()->routeIs('admin.orders.fulfill') || request()->routeIs('admin.orders.fail') ? 'active' : '' }}">{{ __('admin.sidebar.pending_orders') }}</a>

            <p class="section-label">{{ __('admin.sidebar.wallet_group') }}</p>
            <a href="{{ route('admin.topups.index') }}"
                class="nav-link {{ request()->routeIs('admin.topups.*') ? 'active' : '' }}">{{ __('admin.sidebar.topups') }}</a>
            <a href="{{ route('admin.transactions.index') }}"
                class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">{{ __('admin.sidebar.transactions') }}</a>

            <p class="section-label">{{ __('admin.sidebar.users_group') }}</p>
            <a href="{{ route('admin.users.index') }}"
                class="nav-link {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.update') || request()->routeIs('admin.users.edit') || request()->routeIs('admin.users.credit') ? 'active' : '' }}">{{ __('admin.sidebar.all_users') }}</a>

            <p class="section-label">{{ __('admin.sidebar.finance_group') }}</p>
            <a href="{{ route('admin.payment-methods.index') }}"
                class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">{{ __('admin.sidebar.payment_methods') }}</a>
            <a href="{{ route('admin.suppliers.index') }}"
                class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">{{ __('admin.sidebar.suppliers') }}</a>

            <p class="section-label">{{ __('admin.sidebar.analytics') }}</p>
            <a href="{{ route('admin.analytics.index') }}"
                class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">{{ __('admin.sidebar.analytics') }}</a>

            <p class="section-label">{{ __('admin.sidebar.support_group') }}</p>
            <a href="{{ route('admin.contact.index') }}"
                class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">{{ __('admin.sidebar.messages') }}</a>
            <a href="{{ route('admin.feedback.index') }}"
                class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">{{ __('admin.sidebar.feedback') }}</a>

            <p class="section-label">{{ __('admin.sidebar.system_group') }}</p>
            <a href="{{ route('admin.notifications.index') }}"
                class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">{{ __('admin.sidebar.alerts') }}</a>
            <a href="{{ route('admin.settings.index') }}"
                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">{{ __('admin.sidebar.settings') }}</a>
        </nav>
    </div>

    <div class="text-xs text-slate-500 mt-10 lg:block hidden">
        <p>Modern Dashboard, built by <a class="text-blue-500 hover:text-blue-700" target="_blank"
                href="https://broadstark.com">Broadstark</a>.</p>
    </div>
</aside>