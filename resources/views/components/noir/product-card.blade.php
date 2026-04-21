@props(['model'])

@php
    $locale = app()->getLocale();
    $name = $model->{"name_$locale"} ?? $model->name_en;
    $categoryName = $model instanceof \App\Models\Subcategory
        ? ($model->category?->{"name_$locale"} ?? $model->category?->name_en ?? '')
        : ($model->subcategory?->{"name_$locale"} ?? $model->subcategory?->name_en ?? '');
    $modalSlug = $model instanceof \App\Models\Subcategory ? $model->slug : $model->subcategory?->slug;
    $modalProductId = $model instanceof \App\Models\Product ? $model->id : null;
    $avatarImage = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0D1117&color=00D4FF&size=512';
    $subcategoryImage = $model instanceof \App\Models\Product ? $model->subcategory?->image : null;

    $image = match (true) {
        $model->image && str_starts_with($model->image, 'http') => $model->image,
        $model instanceof \App\Models\Product && $subcategoryImage && str_starts_with($subcategoryImage, 'http') => $subcategoryImage,
        (bool) $model->image => \Illuminate\Support\Facades\Storage::url($model->image),
        default => $avatarImage,
    };

    $fallbackImage = $model instanceof \App\Models\Product && $subcategoryImage && str_starts_with($subcategoryImage, 'http')
        ? $subcategoryImage
        : $avatarImage;

    $price = null;
    if ($model instanceof \App\Models\Subcategory) {
        $price = $model->products->flatMap->packages->min('selling_price') ?? $model->products->min('selling_price');
    } else {
        $price = $model->packages->min('selling_price') ?? $model->selling_price;
    }

    $deliveryType = $model instanceof \App\Models\Subcategory
        ? $model->delivery_type
        : ($model->delivery_type ?? $model->subcategory?->delivery_type);
    $deliveryMinutes = $model instanceof \App\Models\Subcategory
        ? $model->delivery_time_minutes
        : ($model->delivery_time_minutes ?? $model->subcategory?->delivery_time_minutes);
    $deliveryLabels = [
        'instant' => ['en' => 'Instant', 'ar' => 'فوري'],
        'fast' => ['en' => 'Fast', 'ar' => 'سريع'],
        'timed' => ['en' => 'Timed', 'ar' => 'مجدول'],
        'slow' => ['en' => 'Slow', 'ar' => 'بطيء'],
        'manual' => ['en' => 'Manual', 'ar' => 'يدوي'],
    ];
    $deliveryLabel = $deliveryType ? ($deliveryLabels[$deliveryType][$locale] ?? ucfirst(str_replace('_', ' ', $deliveryType))) : null;
    if ($deliveryLabel && $deliveryMinutes) {
        $deliveryLabel .= ' · '.$deliveryMinutes.'m';
    }
@endphp

<div {{ $attributes->merge(['class' => 'group overflow-hidden cursor-pointer rounded-xl border border-transparent bg-surface-container-low transition-all duration-300 hover:-translate-y-1 hover:border-primary-container/30 hover:shadow-[0_0_40px_rgba(0,240,255,0.15)] sm:rounded-2xl']) }} data-slug="{{ $model->slug }}" @if($modalSlug)
onclick="openSubcategoryModal(@js($modalSlug), @js($modalProductId))" @endif>

    <div class="relative aspect-square overflow-hidden bg-surface-container-lowest sf-skeleton">
        <img class="h-full w-full object-contain p-3 transition-transform duration-500 group-hover:scale-110 sm:p-6 sf-img-loading"
            src="{{ $image }}" alt="{{ $name }}" loading="lazy" decoding="async"
            onload="this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');"
            onerror="this.onerror=null;this.src=@js($fallbackImage); this.classList.add('sf-img-loaded'); this.parentElement.classList.remove('sf-skeleton');">

        @if($model->is_featured)
            <div
                class="absolute top-2 {{ $locale == 'ar' ? 'left-2 sm:left-3' : 'right-2 sm:right-3' }} rounded border border-secondary-container/30 bg-secondary-container px-1.5 py-0.5 font-label text-[8px] font-black uppercase tracking-tighter text-on-secondary-container shadow-[0_0_10px_rgba(254,0,254,0.5)] sm:top-3 sm:px-2 sm:py-1 sm:text-[10px]">
                {{ $locale == 'ar' ? 'مميز' : 'HOT' }}
            </div>
        @endif

        @if($deliveryLabel)
            <div
                class="absolute bottom-2 {{ $locale == 'ar' ? 'right-2 sm:right-3' : 'left-2 sm:left-3' }} rounded border border-primary-container/30 bg-background/60 px-1.5 py-0.5 font-label text-[8px] uppercase tracking-tighter text-primary-container backdrop-blur-md sm:bottom-3 sm:px-2 sm:py-1 sm:text-[10px]">
                {{ $deliveryLabel }}
            </div>
        @endif
    </div>

    <div class="p-2 sm:p-4">
        <h3
            class="mb-1 truncate font-headline text-[11px] font-bold uppercase leading-tight tracking-wide transition-colors group-hover:text-primary-container sm:text-sm">
            {{ $name }}
        </h3>
        <p class="mb-2 truncate text-[10px] text-on-surface-variant sm:mb-4 sm:text-xs">{{ $categoryName }}</p>

        <div class="flex items-center justify-between gap-1">
            <div class="min-w-0">
                @if($price)
                    <span
                        class="hidden text-[10px] font-bold uppercase tracking-widest text-outline sm:block">{{ $locale == 'ar' ? 'تبدأ من' : 'From' }}</span>
                    <span
                        class="block truncate text-sm font-black leading-none text-primary-container sm:text-lg">${{ number_format($price, 2) }}</span>
                @endif
            </div>

            <button
                class="rounded-lg bg-surface-container-highest p-1.5 text-secondary-container shadow-inner transition-all hover:bg-secondary-container hover:text-on-secondary active:scale-90 sm:p-2"
                aria-label="{{ $locale == 'ar' ? 'شراء' : 'Buy' }}">
                <span class="material-symbols-outlined text-[14px] sm:text-sm">bolt</span>
            </button>
        </div>
    </div>
</div>
