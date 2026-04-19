<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the editor for dynamic pages.
     */
    public function edit()
    {
        $keys = ['page_about', 'page_terms', 'page_privacy', 'page_refunds'];
        $settings = AdminSetting::whereIn('key', $keys)->get()->pluck('value', 'key');

        return view('admin.pages.edit', compact('settings'));
    }

    /**
     * Update dynamic pages content.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'page_about' => ['nullable', 'string'],
            'page_terms' => ['nullable', 'string'],
            'page_privacy' => ['nullable', 'string'],
            'page_refunds' => ['nullable', 'string'],
        ]);

        foreach ($data as $key => $value) {
            AdminSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'pages']
            );
        }

        return back()->with('success', 'Pages updated successfully.');
    }
}
