<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
<div class="mx-auto flex min-h-screen max-w-md items-center px-4">
    <section class="panel w-full">
        <h1 class="text-2xl font-bold text-slate-900">Admin Login</h1>
        <p class="mt-1 text-sm text-slate-500">Access control for MeaCash back office.</p>

        @if (session('success'))
            <div class="alert alert-success mt-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error mt-4">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error mt-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-5 space-y-3">
            @csrf
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <button class="btn-primary w-full justify-center" type="submit">Login as Admin</button>
        </form>

        <a href="{{ route('login') }}" class="mt-4 inline-flex text-sm text-sky-700 underline">User login</a>
    </section>
</div>
</body>
</html>
