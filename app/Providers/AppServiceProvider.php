<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        URL::defaults([
            'locale' => request()->route('locale')
                ?? request()->segment(1)
                ?? app()->getLocale()
                ?? config('app.locale'),
        ]);

        \App\Models\AdminSetting::observe(\App\Observers\AdminSettingObserver::class);

        // Shared site settings for storefront chrome
        \Illuminate\Support\Facades\View::composer(
            ['components.noir.header', 'components.noir.footer', 'components.noir.mobile-nav'],
            function ($view): void {
                $settings = app(\App\Services\SettingsService::class)->getAllCached();
                $view->with('siteSettings', [
                    'site_name'        => $settings['site_name'] ?? 'MeaCash',
                    'site_email'       => $settings['site_email'] ?? '',
                    'site_phone'       => $settings['site_phone'] ?? '',
                    'social_facebook'  => $settings['social_facebook'] ?? '',
                    'social_instagram' => $settings['social_instagram'] ?? '',
                    'social_twitter'   => $settings['social_twitter'] ?? '',
                    'social_whatsapp'  => $settings['social_whatsapp'] ?? '',
                    'social_tiktok'    => $settings['social_tiktok'] ?? '',
                ]);
            }
        );

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
