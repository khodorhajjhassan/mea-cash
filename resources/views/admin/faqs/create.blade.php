@extends('admin.layouts.app')
 
@section('title', 'Add New FAQ')
@section('header', 'Add New FAQ')
 
@section('content')
<div class="max-w-4xl mx-auto">
    <section class="panel">
        <div class="panel-head">
            <h2 class="text-lg font-semibold text-slate-900">FAQ Details</h2>
        </div>
 
        <form method="POST" action="{{ route('admin.faqs.store') }}" class="mt-6">
            @csrf
            <div class="grid gap-6">
                <div class="field">
                    <label for="question_en">Question (English)</label>
                    <input type="text" name="question_en" id="question_en" value="{{ old('question_en') }}" required>
                </div>
                <div class="field">
                    <label for="question_ar">Question (Arabic)</label>
                    <input type="text" name="question_ar" id="question_ar" value="{{ old('question_ar') }}" dir="rtl" required>
                </div>
                <div class="field">
                    <label for="answer_en">Answer (English)</label>
                    <textarea name="answer_en" id="answer_en" rows="5" required>{{ old('answer_en') }}</textarea>
                </div>
                <div class="field">
                    <label for="answer_ar">Answer (Arabic)</label>
                    <textarea name="answer_ar" id="answer_ar" rows="5" dir="rtl" required>{{ old('answer_ar') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="field">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', true))>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-slate-900">Active</span>
                        </label>
                    </div>
                </div>
            </div>
 
            <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.faqs.index') }}" class="btn-ghost">Cancel</a>
                <button type="submit" class="btn-primary">Create FAQ</button>
            </div>
        </form>
    </section>
</div>
@endsection
