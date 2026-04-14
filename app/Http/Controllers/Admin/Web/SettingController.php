<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

use App\Http\Requests\StoreSettingRequest;
use App\Services\SettingsService;
use Exception;

class SettingController extends Controller
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    public function index()
    {
        $request = request();

        $settings = AdminSetting::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($q): void {
                    $inner->where('group', 'like', "%{$q}%")
                        ->orWhere('key', 'like', "%{$q}%")
                        ->orWhere('value', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('group'), fn ($query) => $query->where('group', $request->string('group')->value()))
            ->orderBy('group')
            ->orderBy('key')
            ->paginate(30)
            ->withQueryString();

        $filters = $request->only(['q', 'group']);

        return view('admin.settings.index', compact('settings', 'filters'));
    }

    public function update(Request $request)
    {
        // Handle both single update and bulk update from StoreSettingRequest logic
        if ($request->has('settings')) {
            $data = $request->validate([
                'settings' => ['required', 'array'],
                'settings.*' => ['nullable', 'string'],
            ]);

            foreach ($data['settings'] as $key => $value) {
                AdminSetting::query()->where('key', $key)->update(['value' => $value]);
            }
            
            return back()->with('success', 'Settings bulk updated.');
        }

        $data = $request->validate([
            'group' => ['required', 'string', 'max:100'],
            'key' => ['required', 'string', 'max:150'],
            'value' => ['nullable', 'string'],
        ]);

        AdminSetting::query()->updateOrCreate(
            ['key' => $data['key']],
            ['group' => $data['group'], 'value' => $data['value']]
        );

        return back()->with('success', 'Setting saved.');
    }

    public function general()
    {
        $all = $this->settingsService->getAllCached();
        
        // Filter keys in PHP for speed since we have the cached list
        $settings = array_intersect_key($all, array_flip(['site_name', 'site_email', 'site_phone']));
        $social = array_filter($all, fn($k) => str_starts_with($k, 'social_'), ARRAY_FILTER_USE_KEY);

        return view('admin.settings.general', compact('settings', 'social'));
    }

    public function seo()
    {
        $all = $this->settingsService->getAllCached();
        $settings = array_filter($all, fn($k) => str_starts_with($k, 'seo_'), ARRAY_FILTER_USE_KEY);

        return view('admin.settings.seo', compact('settings'));
    }
}

