<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle locale switching via /en, /ar, ?lang=, user preference, or session.
     * Stores the chosen locale in the session for persistence.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['en', 'ar'];
        $defaultLocale = 'en';

        $pathLocale = $request->route('locale') ?? $request->segment(1);
        $locale = null;

        if (in_array($pathLocale, $supported, true)) {
            $locale = $pathLocale;
        }
        // Check query param next
        elseif ($request->has('lang') && in_array($request->query('lang'), $supported, true)) {
            $locale = $request->query('lang');
        }
        // Then session
        elseif (session()->has('locale') && in_array(session('locale'), $supported, true)) {
            $locale = session('locale');
        }
        // Then authenticated user preference
        elseif ($request->user()?->preferred_language && in_array($request->user()->preferred_language, $supported, true)) {
            $locale = $request->user()->preferred_language;
        }

        $locale = $locale ?: $defaultLocale;
        session(['locale' => $locale]);
        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        if ($request->route() && $request->route()->hasParameter('locale')) {
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}
