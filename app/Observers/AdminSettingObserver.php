<?php

namespace App\Observers;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Cache;

class AdminSettingObserver
{
    /**
     * Handle the AdminSetting "saved" event.
     * Triggers for both created and updated records.
     */
    public function saved(AdminSetting $setting): void
    {
        $this->clearCache($setting);
    }

    /**
     * Handle the AdminSetting "updated" event.
     */
    public function updated(AdminSetting $setting): void
    {
        $this->clearCache($setting);
    }

    /**
     * Handle the AdminSetting "deleted" event.
     */
    public function deleted(AdminSetting $setting): void
    {
        $this->clearCache($setting);
    }

    protected function clearCache(AdminSetting $setting): void
    {
        Cache::forget("setting:{$setting->key}");
        Cache::forget('settings:all');
    }
}
