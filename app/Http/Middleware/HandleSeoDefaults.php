<?php

namespace App\Http\Middleware;

use App\Services\SeoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleSeoDefaults
{
    public function __construct(private readonly SeoService $seoService)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only share SEO data for frontend routes
        if (!$request->is('admin/*') && !$request->is('api/*')) {
            $seoData = $this->seoService->forPage('MeaCash');
            View::share('seo', $seoData);
        }

        return $next($request);
    }
}
