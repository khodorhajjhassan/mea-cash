@extends('admin.layouts.app')
 
@section('title', 'FAQs')
@section('header', 'FAQs')
 
@section('content')
<section class="panel">
    <div class="panel-head">
        <h2 class="text-lg font-semibold text-slate-900">FAQ List</h2>
        <a href="{{ route('admin.faqs.create') }}" class="btn-primary">Add FAQ</a>
    </div>
 
    <div class="table-wrap mt-6">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Sort</th>
                <th>Question (EN/AR)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($faqs as $faq)
                <tr>
                    <td>#{{ $faq->sort_order }}</td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $faq->question_en }}</div>
                        <div class="text-xs text-slate-500">{{ $faq->question_ar }}</div>
                    </td>
                    <td>
                        <span class="status-pill {{ $faq->is_active ? 'ok' : 'off' }}">
                            {{ $faq->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="flex gap-2">
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn-ghost" title="Edit">Edit</a>
                        <button type="button" class="btn-danger-outline js-delete-button" 
                                data-action="{{ route('admin.faqs.destroy', $faq) }}" 
                                data-name="FAQ #{{ $faq->id }}">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-8">No FAQs found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
 
    <div class="mt-4">{{ $faqs->links() }}</div>
</section>
@endsection
