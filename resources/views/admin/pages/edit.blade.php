@extends('admin.layouts.app')
@section('title', 'Dynamic Pages')
@section('header', 'Content Management')

@section('content')
<div class="grid gap-6">
    <section class="panel">
        <div class="panel-head border-b border-slate-100 pb-3">
            <h2 class="text-lg font-semibold text-slate-800">Legal & Informational Pages</h2>
            <p class="text-xs text-slate-500">Edit the content of your site's main static pages using the rich text editor below.</p>
        </div>

        <form method="POST" action="{{ route('admin.pages.update') }}" class="mt-6 space-y-8">
            @csrf

            <!-- About Us Section -->
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-1 bg-indigo-500 rounded-full"></div>
                    <label class="text-base font-bold text-slate-900">About Us Page</label>
                </div>
                <div class="editor-container">
                    <textarea name="page_about" id="editor-about" class="hidden">{{ $settings['page_about'] ?? '' }}</textarea>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Terms & Conditions Section -->
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-1 bg-amber-500 rounded-full"></div>
                    <label class="text-base font-bold text-slate-900">Terms & Conditions</label>
                </div>
                <div class="editor-container">
                    <textarea name="page_terms" id="editor-terms" class="hidden">{{ $settings['page_terms'] ?? '' }}</textarea>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Privacy Policy Section -->
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-1 bg-emerald-500 rounded-full"></div>
                    <label class="text-base font-bold text-slate-900">Privacy Policy</label>
                </div>
                <div class="editor-container">
                    <textarea name="page_privacy" id="editor-privacy" class="hidden">{{ $settings['page_privacy'] ?? '' }}</textarea>
                </div>
            </div>

            <div class="pt-4 sticky bottom-0 bg-white/80 backdrop-blur-md pb-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="btn-primary py-3 px-12 font-bold uppercase tracking-widest shadow-lg shadow-indigo-100">Save All Changes</button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    const editorConfig = {
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'blockQuote', 'insertTable', 'undo', 'redo'
            ]
        }
    };

    document.querySelectorAll('textarea[name^="page_"]').forEach(textarea => {
        ClassicEditor
            .create(textarea, editorConfig)
            .catch(error => {
                console.error(error);
            });
    });
</script>
<style>
    .ck-editor__editable {
        min-height: 250px;
        border-bottom-left-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
        background-color: #f8fafc !important;
    }
    .ck-toolbar {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        border-bottom: 0 !important;
        background-color: #ffffff !important;
    }
    .editor-container {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }
</style>
@endpush
@endsection
