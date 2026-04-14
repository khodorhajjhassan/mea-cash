<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
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
        $settings = AdminSetting::query()->where('group', 'general')->get()->pluck('value', 'key');
        $social = AdminSetting::query()->where('group', 'social')->get()->pluck('value', 'key');

        return view('admin.settings.general', compact('settings', 'social'));
    }

    public function seo()
    {
        $settings = AdminSetting::query()->where('group', 'seo')->get()->pluck('value', 'key');

        return view('admin.settings.seo', compact('settings'));
    }
}
