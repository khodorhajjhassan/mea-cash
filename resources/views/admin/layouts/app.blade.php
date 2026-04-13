<!DOCTYPE html>
<html lang="en">
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
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">MeaCash Control Center</p>
                <h1 class="text-lg font-semibold text-slate-900">@yield('header', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="chip">Admin Home</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn-danger-outline">Logout</button>
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
