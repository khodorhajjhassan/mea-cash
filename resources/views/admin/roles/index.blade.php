@extends('admin.layouts.app')

@section('title', __('roles.title'))
@section('header', __('roles.title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">{{ __('roles.title') }}</h2>
            <p class="text-sm text-slate-500 mt-1">{{ __('roles.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @can('roles.assign')
                <a href="{{ route('admin.roles.assignments') }}" class="btn-ghost">
                    <svg class="h-4 w-4 me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ __('roles.assignment.title') }}
                </a>
            @endcan
            @can('roles.create')
                <a href="{{ route('admin.roles.create') }}" class="btn-primary">
                    <svg class="h-4 w-4 me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('roles.create') }}
                </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        @if(!in_array($role->name, ['super-admin', 'admin']))
                            <div class="flex items-center gap-1">
                                @can('roles.edit')
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                        </svg>
                                    </a>
                                @endcan
                                @can('roles.delete')
                                    <button type="button" 
                                            class="p-2 text-slate-400 hover:text-rose-600 transition-colors"
                                            data-delete-url="{{ route('admin.roles.destroy', $role) }}"
                                            data-delete-text="Are you sure you want to delete the '{{ $role->name }}' role?">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                Protected
                            </span>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <h3 class="text-lg font-bold text-slate-900 capitalize">{{ str_replace('-', ' ', $role->name) }}</h3>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $role->users_count }} users assigned
                        </p>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @php($count = $role->permissions->count())
                        @foreach($role->permissions->take(3) as $permission)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 uppercase tracking-wider">
                                {{ str_replace('.', ': ', $permission->name) }}
                            </span>
                        @endforeach
                        @if($count > 3)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-slate-50 text-slate-600 uppercase tracking-wider">
                                +{{ $count - 3 }} more
                            </span>
                        @endif
                        @if($role->name === 'super-admin')
                             <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 uppercase tracking-wider">
                                ALL ACCESS
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('delete-modal');
        const modalForm = document.getElementById('delete-modal-form');
        const modalText = document.getElementById('delete-modal-text');
        
        document.querySelectorAll('[data-delete-url]').forEach(button => {
            button.addEventListener('click', () => {
                modalForm.action = button.dataset.deleteUrl;
                modalText.textContent = button.dataset.deleteText || 'Are you sure you want to delete this role?';
                modal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('[data-close-delete-modal]').forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });
    });
</script>
@endsection
