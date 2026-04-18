@php $locale = app()->getLocale(); @endphp
@foreach($products as $sub)
@php
    $name = $sub->{"name_{$locale}"};
    $catName = $sub->category?->{"name_{$locale}"} ?? '';
@endphp
<div data-subcategory-slug="{{ $sub->slug }}" class="sf-subcategory-card group cursor-pointer animate-fade-in">
    <div class="sf-product-card-img">
        @if($sub->is_featured)
            <span class="sf-hot-badge">{{ $locale == 'ar' ? '🔥 مميز' : '🔥 Featured' }}</span>
        @endif
        @if($sub->image)
            @php
                $imgUrl = str_starts_with($sub->image, 'http') ? $sub->image : \Illuminate\Support\Facades\Storage::url($sub->image);
            @endphp
            <img src="{{ $imgUrl }}" alt="{{ $name }}" loading="lazy" class="object-contain p-4">
        @else
            <div class="w-full h-full flex items-center justify-center text-4xl opacity-40">🎮</div>
        @endif
    </div>
    <div class="sf-product-card-body">
        <h3 class="sf-product-card-name">{{ $name }}</h3>
        <p class="sf-product-card-cat">{{ $catName }}</p>
    </div>
</div>
@endforeach
