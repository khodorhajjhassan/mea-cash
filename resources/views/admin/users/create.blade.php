@extends('admin.layouts.app')
@section('title', 'Create User')
@section('header', 'New Customer Account')
@section('content')

<section class="panel max-w-2xl mx-auto">
    <div class="panel-head border-b border-slate-100 pb-4 mb-6">
        <h2 class="text-lg font-bold text-slate-800 text-center w-full">Account Details</h2>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid gap-6 md:grid-cols-2">
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required>
            </div>
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
            </div>
            <div class="field">
                <label>Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+961 ...">
            </div>
            <div class="field">
                <label>Preferred Language</label>
                <select name="preferred_language" required>
                    <option value="en" @selected(old('preferred_language') == 'en')>English</option>
                    <option value="ar" @selected(old('preferred_language') == 'ar')>Arabic</option>
                </select>
            </div>
        </div>

        <hr class="border-slate-100">

        <div class="grid gap-6 md:grid-cols-2">
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="field">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="••••••••" required>
            </div>
        </div>

        <div class="flex flex-wrap gap-8 items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
            <div class="flex items-center gap-3">
                <label class="font-bold text-slate-700 text-sm">Account Status</label>
                <select name="is_active" class="py-1 px-3 text-xs font-bold rounded-lg w-auto">
                    <option value="1" @selected(old('is_active', 1) == 1)>Active</option>
                    <option value="0" @selected(old('is_active', 1) == 0)>Disabled</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <label class="font-bold text-slate-700 text-sm">Administrative Role</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin') == 1) class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-xs font-bold text-indigo-600 uppercase">Is Admin</span>
                </label>
            </div>
        </div>

        <div class="flex gap-4 pt-4">
            <a href="{{ route('admin.users.index') }}" class="btn-ghost flex-1 py-3 font-bold uppercase tracking-widest text-xs text-center">Cancel</a>
            <button type="submit" class="btn-primary flex-[2] py-3 font-bold uppercase tracking-widest text-xs shadow-lg shadow-indigo-100">Create Account</button>
        </div>
    </form>
</section>

@endsection
