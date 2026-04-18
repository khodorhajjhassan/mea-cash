@extends('admin.layouts.app')

@section('title', 'Homepage Sections')
@section('header', 'Homepage Sections')

@section('content')
<section class="panel">
    <div class="panel-head">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Dynamic Homepage Blocks</h2>
            <p class="mt-1 text-sm text-slate-500">Control storefront sections by selected products, one subcategory, multiple subcategories, or automatic product lists.</p>
        </div>
        <a href="{{ route('admin.homepage-sections.create') }}" class="btn-primary">Add Section</a>
    </div>

    <div class="table-wrap mt-6">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Sort</th>
                <th>Title</th>
                <th>Source</th>
                <th>Limit</th>
                <th>Status</th>
                <th>Schedule</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($sections as $section)
                <tr>
                    <td>#{{ $section->sort_order }}</td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $section->title_en }}</div>
                        <div class="text-xs text-slate-500">{{ $section->title_ar }}</div>
                    </td>
                    <td>
                        <div>{{ \App\Models\HomepageSection::sourceOptions()[$section->source_type] ?? $section->source_type }}</div>
                        <div class="text-xs text-slate-500">
                            @if($section->source_type === \App\Models\HomepageSection::SOURCE_SUBCATEGORIES)
                                {{ count($section->subcategory_ids ?? []) }} subcategories selected
                            @elseif($section->source_type === \App\Models\HomepageSection::SOURCE_MANUAL_PRODUCTS)
                                {{ count($section->product_ids ?? []) }} products selected
                            @else
                                {{ $section->subcategory?->name_en ?? 'Automatic' }}
                            @endif
                        </div>
                    </td>
                    <td>{{ $section->limit }}</td>
                    <td>
                        <span class="status-pill {{ $section->is_active ? 'ok' : 'off' }}">
                            {{ $section->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="text-xs text-slate-500">
                        @if($section->starts_at || $section->ends_at)
                            {{ $section->starts_at?->format('Y-m-d H:i') ?? 'Now' }} -> {{ $section->ends_at?->format('Y-m-d H:i') ?? 'Open' }}
                        @else
                            Always visible
                        @endif
                    </td>
                    <td class="flex gap-2">
                        <a href="{{ route('admin.homepage-sections.edit', $section) }}" class="btn-ghost">Edit</a>
                        <button type="button" class="btn-danger-outline js-delete-button"
                                data-action="{{ route('admin.homepage-sections.destroy', $section) }}"
                                data-name="{{ $section->title_en }}">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-8 text-center text-slate-500">No homepage sections found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sections->links() }}</div>
</section>
@endsection
