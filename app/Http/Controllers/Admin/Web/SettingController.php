<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = AdminSetting::query()->orderBy('group')->orderBy('key')->get();

        return view('admin.settings.index', compact('settings'));
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

    public function payment()
    {
        $settings = AdminSetting::query()->where('group', 'payment')->orderBy('key')->get();

        return view('admin.settings.payment', compact('settings'));
    }

    public function seo()
    {
        $settings = AdminSetting::query()->where('group', 'seo')->orderBy('key')->get();

        return view('admin.settings.seo', compact('settings'));
    }
}
