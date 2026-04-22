<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_admin || ! $request->user()->is_active) {
            if ($request->user() && ! $request->user()->is_active) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')
                    ->withErrors(['email' => 'Your account is inactive. Please contact support.']);
            }

            abort(403, 'Unauthorized admin access.');
        }

        return $next($request);
    }
}
