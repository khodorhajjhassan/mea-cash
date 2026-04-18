@php
    $hasPendingOrders = \App\Models\Order::where('status', 'pending')->exists();
    $hasPendingTopups = \App\Models\TopupRequest::where('status', 'pending')->exists();
    $hasUnreadContacts = \App\Models\ContactMessage::where('is_read', false)->exists();
    $groupOpen = fn (...$patterns) => request()->routeIs(...$patterns);
@endphp

<aside class="admin-sidebar overflow-y-auto">
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

        <nav class="mt-6 space-y-2">
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>{{ __('admin.sidebar.dashboard') }}</span>
            </a>

            @canany(['categories.index', 'products.index'])
                <details class="group" {{ $groupOpen('admin.categories.*', 'admin.subcategories.*', 'admin.product-types.*', 'admin.products.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.catalog') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        @can('categories.index')
                            <a href="{{ route('admin.categories.index') }}"
                                class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>{{ __('admin.sidebar.categories') }}</span>
                            </a>
                            <a href="{{ route('admin.subcategories.index') }}"
                                class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span>{{ __('admin.sidebar.subcategories') }}</span>
                            </a>
                            <a href="{{ route('admin.product-types.index') }}"
                                class="nav-link {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span>{{ __('admin.sidebar.product_types') }}</span>
                            </a>
                        @endcan
                        @can('products.index')
                            <a href="{{ route('admin.products.index') }}"
                                class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span>{{ __('admin.sidebar.products') }}</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany

            @can('categories.index')
                <details class="group" {{ $groupOpen('admin.banners.*', 'admin.faqs.*', 'admin.homepage-sections.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.cms_group') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        <a href="{{ route('admin.homepage-sections.index') }}"
                            class="nav-link {{ request()->routeIs('admin.homepage-sections.*') ? 'active' : '' }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a2 2 0 012-2h5v8H4V5zm9-2h5a2 2 0 012 2v4h-7V3zM4 13h7v8H6a2 2 0 01-2-2v-6zm9-2h7v8a2 2 0 01-2 2h-5V11z" />
                            </svg>
                            <span>{{ __('admin.sidebar.homepage_sections') }}</span>
                        </a>
                        <a href="{{ route('admin.banners.index') }}"
                            class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ __('admin.sidebar.banners') }}</span>
                        </a>
                        <a href="{{ route('admin.faqs.index') }}"
                            class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('admin.sidebar.faqs') }}</span>
                        </a>
                    </div>
                </details>
            @endcan

            @canany(['orders.index', 'orders.pending'])
                <details class="group" {{ $groupOpen('admin.orders.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.orders_group') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        @can('orders.index')
                            <a href="{{ route('admin.orders.index') }}"
                                class="nav-link {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') || request()->routeIs('admin.orders.status') || request()->routeIs('admin.orders.refund') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span>{{ __('admin.sidebar.all_orders') }}</span>
                            </a>
                        @endcan
                        @can('orders.pending')
                            <a href="{{ route('admin.orders.pending') }}"
                                class="nav-link {{ request()->routeIs('admin.orders.pending') || request()->routeIs('admin.orders.fulfill') || request()->routeIs('admin.orders.fail') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('admin.sidebar.pending_orders') }}</span>
                                @if($hasPendingOrders)
                                    <span class="absolute end-3 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full bg-rose-500 shadow-sm"></span>
                                @endif
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany

            @canany(['topups.index', 'transactions.index'])
                <details class="group" {{ $groupOpen('admin.topups.*', 'admin.transactions.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.wallet_group') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        @can('topups.index')
                            <a href="{{ route('admin.topups.index') }}"
                                class="nav-link {{ request()->routeIs('admin.topups.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                                </svg>
                                <span>{{ __('admin.sidebar.topups') }}</span>
                                @if($hasPendingTopups)
                                    <span class="absolute end-3 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full bg-rose-500 shadow-sm"></span>
                                @endif
                            </a>
                        @endcan
                        @can('transactions.index')
                            <a href="{{ route('admin.transactions.index') }}"
                                class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span>{{ __('admin.sidebar.transactions') }}</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany

            @can('users.index')
                <details class="group" {{ $groupOpen('admin.users.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.users_group') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.update') || request()->routeIs('admin.users.edit') || request()->routeIs('admin.users.credit') ? 'active' : '' }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>{{ __('admin.sidebar.all_users') }}</span>
                        </a>
                    </div>
                </details>
            @endcan

            @canany(['payment-methods.index', 'suppliers.index'])
                <details class="group" {{ $groupOpen('admin.payment-methods.*', 'admin.suppliers.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.finance_group') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        @can('payment-methods.index')
                            <a href="{{ route('admin.payment-methods.index') }}"
                                class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span>{{ __('admin.sidebar.payment_methods') }}</span>
                            </a>
                        @endcan
                        @can('suppliers.index')
                            <a href="{{ route('admin.suppliers.index') }}"
                                class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>{{ __('admin.sidebar.suppliers') }}</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany

            @can('analytics.index')
                <details class="group" {{ $groupOpen('admin.analytics.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.analytics') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        <a href="{{ route('admin.analytics.index') }}"
                            class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ __('admin.sidebar.analytics') }}</span>
                        </a>
                    </div>
                </details>
            @endcan

            @canany(['contact.index', 'feedback.index'])
                <details class="group" {{ $groupOpen('admin.contact.*', 'admin.feedback.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.support_group') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        @can('contact.index')
                            <a href="{{ route('admin.contact.index') }}"
                                class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ __('admin.sidebar.messages') }}</span>
                                @if($hasUnreadContacts)
                                    <span class="absolute end-3 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rounded-full bg-rose-500 shadow-sm"></span>
                                @endif
                            </a>
                        @endcan
                        @can('feedback.index')
                            <a href="{{ route('admin.feedback.index') }}"
                                class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                <span>{{ __('admin.sidebar.feedback') }}</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany

            @canany(['settings.general', 'roles.index'])
                <details class="group" {{ $groupOpen('admin.notifications.*', 'admin.roles.*', 'admin.settings.*') ? 'open' : '' }}>
                    <summary class="section-label flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 hover:bg-slate-100 [&::-webkit-details-marker]:hidden">
                        <span>{{ __('admin.sidebar.system_group') }}</span>
                        <svg class="h-3.5 w-3.5 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1">
                        <a href="{{ route('admin.notifications.index') }}"
                            class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span>{{ __('admin.sidebar.alerts') }}</span>
                        </a>
                        @can('roles.index')
                            <a href="{{ route('admin.roles.index') }}"
                                class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="1 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>{{ __('admin.sidebar.roles') }}</span>
                            </a>
                        @endcan
                        @can('settings.general')
                            <a href="{{ route('admin.settings.index') }}"
                                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ __('admin.sidebar.settings') }}</span>
                            </a>
                        @endcan
                    </div>
                </details>
            @endcanany
        </nav>
    </div>

    <div class="text-xs text-slate-500 mt-10 hidden lg:block">
        <p>Modern Dashboard, built by <a class="text-blue-500 hover:text-blue-700" target="_blank"
                href="https://broadstark.com">Broadstark</a>.</p>
    </div>
</aside>
