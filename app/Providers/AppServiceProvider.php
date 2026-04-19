<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\AdminSetting::observe(\App\Observers\AdminSettingObserver::class);

        \Illuminate\Support\Facades\View::composer('components.noir.header', function ($view): void {
            $view->with([
                'storeNotifications' => auth()->check()
                    ? auth()->user()->notifications()->latest()->limit(6)->get()
                    : collect(),
                'storeUnreadCount' => auth()->check() ? auth()->user()->unreadNotifications()->count() : 0,
            ]);
        });

        \Illuminate\Support\Facades\View::composer('components.noir.mobile-nav', function ($view): void {
            if (
                ! \Illuminate\Support\Facades\Schema::hasTable('categories')
                || ! \Illuminate\Support\Facades\Schema::hasTable('subcategories')
                || ! \Illuminate\Support\Facades\Schema::hasTable('products')
            ) {
                $mobileCategories = collect();
            } else {
                $mobileCategories = \App\Models\Category::query()
                    ->where('is_active', true)
                    ->with(['subcategories' => fn ($query) => $query
                        ->where('is_active', true)
                        ->whereHas('products', fn ($productQuery) => $productQuery->where('is_active', true))
                        ->orderBy('sort_order')])
                    ->orderBy('sort_order')
                    ->get();
            }

            $view->with('mobileCategories', $mobileCategories);
        });

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
