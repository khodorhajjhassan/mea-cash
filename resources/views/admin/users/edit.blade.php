@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('header', 'Edit: ' . $user->name)
@section('content')

<div class="max-w-2xl">
    <section class="panel">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 md:grid-cols-2">
                <div class="field">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="field">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="field">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="field">
                    <label>Preferred Language</label>
                    <select name="preferred_language">
                        <option value="en" @selected(old('preferred_language', $user->preferred_language) == 'en')>English</option>
                        <option value="ar" @selected(old('preferred_language', $user->preferred_language) == 'ar')>Arabic</option>
                    </select>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="field">
                    <label>New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="••••••••">
                </div>
                <div class="field">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••">
                </div>
            </div>

            <div class="flex flex-wrap gap-8 items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div class="flex items-center gap-3">
                    <label class="font-bold text-slate-700 text-sm">Account Status</label>
                    <select name="is_active" class="py-1 px-3 text-xs font-bold rounded-lg w-auto">
                        <option value="1" @selected(old('is_active', $user->is_active) == 1)>Active</option>
                        <option value="0" @selected(old('is_active', $user->is_active) == 0)>Disabled</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <label class="font-bold text-slate-700 text-sm">Administrative Role</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_admin" value="0">
                        <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin) == 1) class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-xs font-bold text-indigo-600 uppercase">Is Admin</span>
                    </label>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex gap-3">
                <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost flex-1">Cancel</a>
                <button type="submit" class="btn-primary flex-1 py-3 font-bold uppercase">Save Changes</button>
            </div>
        </form>
    </section>
</div>

@endsection
