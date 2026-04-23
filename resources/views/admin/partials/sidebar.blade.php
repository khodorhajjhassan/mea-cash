@php
    $hasPendingOrders = \App\Models\Order::where('status', 'pending')->exists();
    $hasPendingTopups = \App\Models\TopupRequest::where('status', 'pending')->exists();
    $hasUnreadContacts = \App\Models\ContactMessage::where('is_read', false)->exists();
    $canManageRoles = auth()->check() && auth()->user()->hasRole('super-admin');
    $showSystemGroup = $canManageRoles || (auth()->check() && auth()->user()->canAny(['notifications.index', 'settings.general']));
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

        @if(auth()->check())
        <div class="mt-6 mb-2 px-1">
            <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-sm font-bold text-indigo-600">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs font-medium text-slate-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
        @endif

        <nav class="mt-4 space-y-3" x-data="{ 
            openGroup: '{{ $groupOpen('admin.categories.*', 'admin.subcategories.*', 'admin.product-types.*', 'admin.products.*') ? 'catalog' : (
                $groupOpen('admin.banners.*', 'admin.faqs.*', 'admin.homepage-sections.*', 'admin.pages.*') ? 'cms' : (
                $groupOpen('admin.orders.*') ? 'orders' : (
                $groupOpen('admin.topups.*', 'admin.transactions.*') ? 'wallet' : (
                $groupOpen('admin.users.*') ? 'users' : (
                $groupOpen('admin.payment-methods.*', 'admin.suppliers.*') ? 'finance' : (
                $groupOpen('admin.analytics.*') ? 'analytics' : (
                $groupOpen('admin.contact.*', 'admin.feedback.*') ? 'support' : (
                $groupOpen('admin.notifications.*', 'admin.roles.*', 'admin.settings.*') ? 'system' : ''
            )))))))) }}'
        }">
            <!-- Dashboard Link -->
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} !text-base !font-bold py-3">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>{{ __('admin.sidebar.dashboard') }}</span>
            </a>

            <!-- Catalog Group -->
            @canany(['categories.index', 'subcategories.index', 'product-types.index', 'products.index'])
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'catalog' ? '' : 'catalog')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.categories.*', 'admin.subcategories.*', 'admin.product-types.*', 'admin.products.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'catalog'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            {{ __('admin.sidebar.catalog') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'catalog'" x-collapse class="sidebar-dropdown-content">
                        @can('categories.index')
                            <a href="{{ route('admin.categories.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.categories') }}</span>
                            </a>
                        @endcan
                        @can('subcategories.index')
                            <a href="{{ route('admin.subcategories.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.subcategories') }}</span>
                            </a>
                        @endcan
                        @can('products.index')
                            <a href="{{ route('admin.products.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.products') }}</span>
                            </a>
                        @endcan
                        @can('product-types.index')
                            <a href="{{ route('admin.product-types.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.product_types') }}</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- CMS Group -->
            @canany(['cms.index', 'homepage-sections.index', 'banners.index', 'faqs.index', 'pages.edit'])
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'cms' ? '' : 'cms')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.banners.*', 'admin.faqs.*', 'admin.homepage-sections.*', 'admin.pages.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'cms'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a2 2 0 012-2h5v8H4V5zm9-2h5a2 2 0 012 2v4h-7V3zM4 13h7v8H6a2 2 0 01-2-2v-6zm9-2h7v8a2 2 0 01-2 2h-5V11z" />
                            </svg>
                            {{ __('admin.sidebar.cms_group') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'cms'" x-collapse class="sidebar-dropdown-content">
                        @canany(['cms.index', 'homepage-sections.index'])
                            <a href="{{ route('admin.homepage-sections.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.homepage-sections.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.homepage_sections') }}</span>
                            </a>
                        @endcanany
                        @canany(['cms.index', 'banners.index'])
                            <a href="{{ route('admin.banners.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.banners') }}</span>
                            </a>
                        @endcanany
                        @canany(['cms.index', 'faqs.index'])
                            <a href="{{ route('admin.faqs.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.faqs') }}</span>
                            </a>
                        @endcanany
                        @canany(['cms.index', 'pages.edit'])
                            <a href="{{ route('admin.pages.edit') }}" class="sidebar-child-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.pages') }}</span>
                            </a>
                        @endcanany
                    </div>
                </div>
            @endcanany

            <!-- Orders Group -->
            @canany(['orders.index', 'orders.pending'])
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'orders' ? '' : 'orders')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.orders.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'orders'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                            </svg>
                            {{ __('admin.sidebar.orders_group') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'orders'" x-collapse class="sidebar-dropdown-content">
                        @can('orders.index')
                            <a href="{{ route('admin.orders.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.all_orders') }}</span>
                            </a>
                        @endcan
                        @can('orders.pending')
                            <a href="{{ route('admin.orders.pending') }}" class="sidebar-child-link {{ request()->routeIs('admin.orders.pending') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.pending_orders') }}</span>
                                @if($hasPendingOrders)
                                    <span class="ms-auto h-2 w-2 rounded-full bg-rose-500"></span>
                                @endif
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- Wallet Group -->
            @canany(['topups.index', 'transactions.index'])
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'wallet' ? '' : 'wallet')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.topups.*', 'admin.transactions.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'wallet'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m4-3h-8m8 0l-3-3m3 3l-3 3" />
                            </svg>
                            {{ __('admin.sidebar.wallet_group') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'wallet'" x-collapse class="sidebar-dropdown-content">
                        @can('topups.index')
                            <a href="{{ route('admin.topups.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.topups.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.topups') }}</span>
                                @if($hasPendingTopups)
                                    <span class="ms-auto h-2 w-2 rounded-full bg-rose-500"></span>
                                @endif
                            </a>
                        @endcan
                        @can('transactions.index')
                            <a href="{{ route('admin.transactions.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.transactions') }}</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- Users Group -->
            @can('users.index')
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'users' ? '' : 'users')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.users.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'users'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm8 0a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('admin.sidebar.users_group') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'users'" x-collapse class="sidebar-dropdown-content">
                        <a href="{{ route('admin.users.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <span>{{ __('admin.sidebar.all_users') }}</span>
                        </a>
                    </div>
                </div>
            @endcan

            <!-- Finance Group -->
            @canany(['payment-methods.index', 'suppliers.index'])
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'finance' ? '' : 'finance')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.payment-methods.*', 'admin.suppliers.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'finance'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2m8-6a8 8 0 11-16 0 8 8 0 0116 0z" />
                            </svg>
                            {{ __('admin.sidebar.finance_group') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'finance'" x-collapse class="sidebar-dropdown-content">
                        @can('payment-methods.index')
                            <a href="{{ route('admin.payment-methods.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.payment_methods') }}</span>
                            </a>
                        @endcan
                        @can('suppliers.index')
                            <a href="{{ route('admin.suppliers.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.suppliers') }}</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- Analytics -->
            @can('analytics.index')
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'analytics' ? '' : 'analytics')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.analytics.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'analytics'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm6 0V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10m10 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14" />
                            </svg>
                            {{ __('admin.sidebar.analytics') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'analytics'" x-collapse class="sidebar-dropdown-content">
                        <a href="{{ route('admin.analytics.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <span>{{ __('admin.sidebar.analytics') }}</span>
                        </a>
                    </div>
                </div>
            @endcan

            <!-- Support Group -->
            @canany(['contact.index', 'feedback.index'])
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'support' ? '' : 'support')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.contact.*', 'admin.feedback.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'support'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m-12.728 0a9 9 0 010-12.728m9.9 2.828a5 5 0 010 7.072m-7.072 0a5 5 0 010-7.072M12 13a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                            {{ __('admin.sidebar.support_group') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'support'" x-collapse class="sidebar-dropdown-content">
                        @can('contact.index')
                            <a href="{{ route('admin.contact.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.messages') }}</span>
                                @if($hasUnreadContacts)
                                    <span class="ms-auto h-2 w-2 rounded-full bg-rose-500"></span>
                                @endif
                            </a>
                        @endcan
                        @can('feedback.index')
                            <a href="{{ route('admin.feedback.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.feedback') }}</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            <!-- System Group -->
            @if($showSystemGroup)
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'system' ? '' : 'system')" 
                        class="sidebar-parent-btn {{ $groupOpen('admin.notifications.*', 'admin.roles.*', 'admin.settings.*') ? 'active' : '' }}"
                        :aria-expanded="openGroup === 'system'">
                        <span class="sidebar-parent-text">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            {{ __('admin.sidebar.system_group') }}
                        </span>
                        <svg class="sidebar-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openGroup === 'system'" x-collapse class="sidebar-dropdown-content">
                        @can('notifications.index')
                            <a href="{{ route('admin.notifications.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.alerts') }}</span>
                            </a>
                        @endcan
                        @if($canManageRoles)
                            <a href="{{ route('admin.roles.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.roles') }}</span>
                            </a>
                        @endif
                        @can('settings.general')
                            <a href="{{ route('admin.settings.index') }}" class="sidebar-child-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <span>{{ __('admin.sidebar.settings') }}</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endif
        </nav>
    </div>

    <div class="text-xs text-slate-500 mt-10 hidden lg:block">
        <p>Modern Dashboard, built by <a class="text-blue-500 hover:text-blue-700" target="_blank"
                href="https://broadstark.com">Broadstark</a>.</p>
    </div>
</aside>
