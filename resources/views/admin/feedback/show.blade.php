@extends('admin.layouts.app')
@section('title', 'Feedback Details')
@section('header', 'Feedback Details')
@section('content')

<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">User Feedback</h2>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.feedback.destroy', $feedback) }}" onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger-outline">Delete Feedback</button>
            </form>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div><p class="text-xs text-slate-500">User</p><p class="font-medium text-slate-900">{{ $feedback->user?->name ?? 'Guest' }}</p></div>
        <div><p class="text-xs text-slate-500">Order Number</p><p class="font-medium text-slate-900">{{ $feedback->order?->order_number ?? '-' }}</p></div>
        <div>
            <p class="text-xs text-slate-500">Rating</p>
            <div class="flex items-center mt-1">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="h-4 w-4 {{ $i <= $feedback->rating ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
                <span class="ml-2 text-sm font-medium text-slate-900">{{ $feedback->rating }}/5</span>
            </div>
        </div>
    </div>

    <div class="mt-6 border-t border-slate-100 pt-6">
        <p class="text-xs text-slate-500">Comment</p>
        <div class="mt-2 p-4 rounded-xl bg-slate-50 text-slate-800 italic leading-relaxed">
            "{{ $feedback->comment ?: 'No comment provided.' }}"
        </div>
    </div>
</section>
@endsection

