<?php

namespace App\Services;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Get a setting value by key with forever caching.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            return AdminSetting::query()->where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Clear the cache for a specific setting.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void
    {
        Cache::forget("setting:{$key}");
    }

    /**
     * Get all settings grouped by category (optional optimization).
     */
    public function getAllCached(): array
    {
        return Cache::rememberForever('settings:all', function () {
            return AdminSetting::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear all settings cache.
     */
    public function clearAll(): void
    {
        Cache::forget('settings:all');
        // Note: For individual keys, we rely on the Observer to clear them specifically.
    }
}
