<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle locale switching via ?lang= query parameter or session.
     * Stores the chosen locale in the session for persistence.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['en', 'ar'];

        // Check query param first
        if ($request->has('lang') && in_array($request->query('lang'), $supported, true)) {
            $locale = $request->query('lang');
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }
        // Then session
        elseif (session()->has('locale') && in_array(session('locale'), $supported, true)) {
            app()->setLocale(session('locale'));
        }
        // Then authenticated user preference
        elseif ($request->user()?->preferred_language && in_array($request->user()->preferred_language, $supported, true)) {
            $locale = $request->user()->preferred_language;
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
