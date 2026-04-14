<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
<div class="admin-shell">
    @include('admin.partials.sidebar')

    <div class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ __('admin.header.welcome') }}</p>
                <h1 class="text-lg font-semibold text-slate-900">@yield('header', __('admin.sidebar.dashboard'))</h1>
            </div>
            <div class="flex items-center gap-4">
                <!-- Notifications -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <div class="relative">
                    <button id="notification-bell" class="relative p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php($unreadCount = auth()->user()?->unreadNotifications->count() ?? 0)
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <div id="notification-dropdown" class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-3 w-80 rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 z-[100] overflow-hidden hidden">
                        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-800">{{ __('admin.notifications.title') }}</h3>
                            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider hover:text-indigo-800">{{ __('admin.notifications.mark_all_read') }}</button>
                            </form>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @php($notifications = auth()->user()?->notifications()->latest()->limit(6)->get() ?? collect())
                            @forelse($notifications as $notification)
                                <a href="{{ $notification->data['link'] ?? '#' }}" class="block p-4 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0 {{ $notification->read_at ? 'opacity-60' : '' }}">
                                    <div class="flex gap-3">
                                        <div class="h-8 w-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                                             @if(($notification->data['icon'] ?? '') == 'order')
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                             @elseif(($notification->data['icon'] ?? '') == 'wallet')
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                             @else
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                             @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-bold text-slate-800 leading-tight">{{ $notification->data['type'] ?? 'Notification' }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-sm">No notifications yet.</div>
                            @endforelse
                        </div>
                        <a href="{{ route('admin.notifications.index') }}" class="block p-3 text-center text-[10px] font-bold text-indigo-600 uppercase tracking-widest bg-slate-50 border-t border-slate-100 hover:bg-slate-100 transition-colors">
                            {{ __('admin.common.view_all') }}
                        </a>
                    </div>
                </div>

                <script>
                    document.getElementById('notification-bell').addEventListener('click', function() {
                        document.getElementById('notification-dropdown').classList.toggle('hidden');
                    });
                    document.addEventListener('click', function(event) {
                        const bell = document.getElementById('notification-bell');
                        const dropdown = document.getElementById('notification-dropdown');
                        if (!bell.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                </script>

                <a href="{{ route('admin.dashboard') }}" class="chip">{{ __('admin.header.home') }}</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn-danger-outline">{{ __('admin.header.logout') }}</button>
                </form>
            </div>
        </header>

        @include('admin.partials.alerts')

        <main class="admin-content">
            @yield('content')
        </main>
    </div>
</div>

<div id="delete-modal" class="modal-backdrop hidden" aria-hidden="true">
    <div class="modal-card">
        <h2 class="text-lg font-semibold text-slate-900">Confirm Delete</h2>
        <p id="delete-modal-text" class="mt-2 text-sm text-slate-600">Are you sure you want to delete this record?</p>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" class="btn-ghost" data-close-delete-modal>Cancel</button>
            <form id="delete-modal-form" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
