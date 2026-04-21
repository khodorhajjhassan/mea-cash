<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

use App\Http\Requests\StoreSettingRequest;
use App\Services\Media\ImageStorageService;
use App\Services\SettingsService;
use Exception;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    public function index()
    {
        $request = request();

        $settings = AdminSetting::query()
            ->where('group', '!=', 'pages')
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
                $this->settingsService->forget($key);
            }
            $this->settingsService->clearAll();
            
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

        $this->settingsService->forget($data['key']);
        $this->settingsService->clearAll();

        return back()->with('success', 'Setting saved.');
    }

    public function general()
    {
        $all = $this->settingsService->getAllCached();
        
        // Filter keys in PHP for speed since we have the cached list
        $settings = array_intersect_key($all, array_flip(['site_name', 'site_email', 'site_phone', 'support_report_delay_hours']));
        $social = array_filter($all, fn($k) => str_starts_with($k, 'social_'), ARRAY_FILTER_USE_KEY);
        $seo = array_intersect_key($all, array_flip(['meta_title', 'meta_description', 'meta_keywords']));

        return view('admin.settings.general', compact('settings', 'social', 'seo'));
    }

    public function seo()
    {
        $all = $this->settingsService->getAllCached();
        $settings = array_filter($all, fn($k) => str_starts_with($k, 'seo_') || str_starts_with($k, 'og_') || str_starts_with($k, 'twitter_') || str_starts_with($k, 'google_') || str_starts_with($k, 'facebook_') || str_starts_with($k, 'tiktok_') || str_starts_with($k, 'snapchat_') || str_starts_with($k, 'schema_') || str_contains($k, '_enabled'), ARRAY_FILTER_USE_KEY);

        return view('admin.settings.seo', compact('settings'));
    }

    public function updateSeo(Request $request, ImageStorageService $imageService)
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'image', 'max:5120'],
        ]);

        DB::transaction(function () use ($data, $imageService, $request) {
            foreach ($data['settings'] as $key => $value) {
                AdminSetting::query()->where('key', $key)->update(['value' => $value]);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    $oldPath = AdminSetting::query()->where('key', $key)->value('value');
                    $path = $imageService->storeAsWebp($file, 'settings/seo', $oldPath);
                    AdminSetting::query()->updateOrCreate(
                        ['key' => $key],
                        ['group' => 'seo', 'value' => $path]
                    );
                }
            }
        });

        $this->settingsService->clearAll();

        return back()->with('success', 'SEO settings updated successfully.');
    }
}
