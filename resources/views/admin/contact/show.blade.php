@extends('admin.layouts.app')
@section('title', 'Contact Message')
@section('header', 'Contact Message')
@section('content')

<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">{{ $contact->subject ?: 'No Subject' }}</h2>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.contact.destroy', $contact) }}" onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger-outline">Delete Message</button>
            </form>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div><p class="text-xs text-slate-500">From Name</p><p class="font-medium text-slate-900">{{ $contact->name }}</p></div>
        <div><p class="text-xs text-slate-500">Email Address</p><p class="font-medium text-slate-900">{{ $contact->email }}</p></div>
        <div><p class="text-xs text-slate-500">Received At</p><p class="font-medium text-slate-900">{{ $contact->created_at->format('Y-m-d H:i') }}</p></div>
    </div>

    <div class="mt-6 border-t border-slate-100 pt-6">
        <p class="text-xs text-slate-500">Message Content</p>
        <div class="mt-2 p-4 rounded-xl bg-slate-50 text-slate-800 whitespace-pre-wrap leading-relaxed">
            {{ $contact->message }}
        </div>
    </div>
</section>
@endsection

