@extends('admin.layouts.app')

@section('title', __('roles.create'))
@section('header', __('roles.create'))

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ 
    selectedPermissions: [],
    selectAll(group, permissions) {
        permissions.forEach(p => {
            if (!this.selectedPermissions.includes(p)) {
                this.selectedPermissions.push(p);
            }
        });
    },
    deselectAll(group, permissions) {
        this.selectedPermissions = this.selectedPermissions.filter(p => !permissions.includes(p));
    }
}">
    <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <div class="max-w-md">
                <label for="name" class="block text-sm font-bold text-slate-700 uppercase tracking-wide">{{ __('roles.name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                       class="mt-2 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 bg-slate-50"
                       placeholder="{{ __('roles.role_name_placeholder') }}" required>
                @error('name')
                    <p class="mt-2 text-sm text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="space-y-6">
            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">{{ __('roles.permissions') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($permissions as $group => $groupPermissions)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
                        <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 capitalize">
                                {{ trans()->has('roles.modules.' . $group) ? __('roles.modules.' . $group) : \Illuminate\Support\Str::headline(str_replace('-', ' ', $group)) }}
                            </h4>
                            <div class="flex gap-2">
                                <button type="button" 
                                        @click="selectAll('{{ $group }}', {{ json_encode($groupPermissions->pluck('name')) }})"
                                        class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">
                                    {{ __('roles.select_all') }}
                                </button>
                                <span class="text-slate-300 text-[10px]">|</span>
                                <button type="button" 
                                        @click="deselectAll('{{ $group }}', {{ json_encode($groupPermissions->pluck('name')) }})"
                                        class="text-[10px] font-bold text-slate-500 hover:text-slate-700 uppercase tracking-widest">
                                    {{ __('roles.deselect_all') }}
                                </button>
                            </div>
                        </div>
                        <div class="p-5 flex-1 space-y-3">
                            @foreach($groupPermissions as $permission)
                                <label class="relative flex items-start cursor-pointer group">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                               x-model="selectedPermissions"
                                               class="h-4 w-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                                    </div>
                                    <div class="ms-3 text-sm">
                                        <span class="font-medium text-slate-700 group-hover:text-indigo-600 transition-colors">
                                            {{ \Illuminate\Support\Str::headline(str_replace(['-', $group . '.'], [' ', ''], $permission->name)) }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
            <a href="{{ route('admin.roles.index') }}" class="btn-ghost">{{ __('admin.common.cancel') }}</a>
            <button type="submit" class="btn-primary min-w-[120px]">{{ __('roles.save') }}</button>
        </div>
    </form>
</div>
@endsection
