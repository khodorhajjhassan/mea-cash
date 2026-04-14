@extends('admin.layouts.app')
@section('title', 'System Alerts')
@section('header', 'System Notifications Hub')
@section('content')

<section class="panel max-w-4xl mx-auto">
    <div class="panel-head border-b border-slate-100 pb-4 mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Recent Activity</h2>
            <p class="text-xs text-slate-500">Chronological history of all automated system events.</p>
        </div>
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn-ghost py-2 h-auto text-[10px] font-bold uppercase tracking-widest leading-none">Mark all as read</button>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($notifications as $notification)
        <div class="flex gap-4 p-4 rounded-2xl border border-slate-100 bg-white transition-all hover:border-indigo-100 hover:shadow-sm {{ $notification->read_at ? 'opacity-70 bg-slate-50/30' : 'ring-1 ring-indigo-50 ring-inset shadow-sm' }}">
            <div class="h-10 w-10 shrink-0 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                @if(($notification->data['icon'] ?? '') == 'order')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                @elseif(($notification->data['icon'] ?? '') == 'wallet')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif(($notification->data['icon'] ?? '') == 'star')
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                @else
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>

            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-500 mb-1 block">{{ $notification->data['type'] ?? 'System' }}</span>
                        <h3 class="text-sm font-bold text-slate-800 leading-tight">{{ $notification->data['message'] ?? '' }}</h3>
                    </div>
                    <span class="text-[10px] font-medium text-slate-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                
                <div class="mt-3 flex gap-3 items-center">
                    <a href="{{ route('admin.notifications.read', $notification->id) }}" class="btn-primary py-1.5 px-4 text-[10px] font-bold uppercase tracking-widest h-auto leading-none">View Details</a>
                    @if(!$notification->read_at)
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="py-20 text-center">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-300 mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            </div>
            <p class="text-slate-500 font-medium">No alerts found in the database.</p>
        </div>
        @endforelse

        <div class="mt-8 pt-6 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
    </div>
</section>

@endsection
