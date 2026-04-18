<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ app()->getLocale() === 'ar' ? 'غير مصرح - MeaCash' : 'Access Denied - MeaCash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-hidden bg-background text-on-surface antialiased">
    <main class="relative flex min-h-screen items-center justify-center px-4 py-12">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(0,240,255,0.18),transparent_30%),radial-gradient(circle_at_80%_20%,rgba(254,0,254,0.14),transparent_28%),linear-gradient(135deg,rgba(17,19,25,1),rgba(12,14,20,1))]"></div>
        <div class="pointer-events-none absolute left-[10%] top-[18%] h-24 w-24 rounded-full border border-primary-container/15"></div>
        <div class="pointer-events-none absolute bottom-[16%] right-[12%] h-32 w-32 rotate-12 rounded-[2rem] border border-secondary-container/15"></div>

        <section class="relative w-full max-w-2xl overflow-hidden rounded-[36px] border border-outline-variant/20 bg-surface-container/80 p-8 text-center shadow-[0_35px_120px_rgba(0,0,0,0.65)] backdrop-blur-2xl md:p-12">
            <div class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-3xl border border-yellow-400/20 bg-yellow-400/10 text-yellow-300 shadow-[0_0_45px_rgba(250,204,21,0.15)]">
                <span class="material-symbols-outlined text-4xl">lock</span>
            </div>

            <p class="mb-3 font-label text-[11px] font-black uppercase tracking-[0.35em] text-primary-container">
                {{ app()->getLocale() === 'ar' ? 'رمز الخطأ 403' : 'Error Code 403' }}
            </p>
            <h1 class="font-headline text-4xl font-black uppercase tracking-tight text-on-surface md:text-6xl">
                {{ app()->getLocale() === 'ar' ? 'الدخول مرفوض' : 'Access Denied' }}
            </h1>
            <p class="mx-auto mt-5 max-w-lg text-sm leading-7 text-on-surface-variant">
                {{ app()->getLocale() === 'ar'
                    ? 'ليس لديك صلاحية لفتح هذه الصفحة. إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع الإدارة.'
                    : 'You do not have permission to open this page. If this feels wrong, please contact support or an administrator.' }}
            </p>

            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('store.home') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant/25 bg-surface-container-low px-6 py-4 font-headline text-xs font-black uppercase tracking-[0.2em] text-on-surface transition hover:border-primary-container/60 hover:text-primary-container">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Go Back' }}
                </a>
                <a href="{{ route('store.home') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-primary-fixed to-secondary-fixed-dim px-6 py-4 font-headline text-xs font-black uppercase tracking-[0.2em] text-on-primary-fixed transition hover:scale-[1.02] active:scale-[0.98]">
                    {{ app()->getLocale() === 'ar' ? 'الصفحة الرئيسية' : 'Home Page' }}
                    <span class="material-symbols-outlined text-base">home</span>
                </a>
            </div>
        </section>
    </main>
</body>
</html>
