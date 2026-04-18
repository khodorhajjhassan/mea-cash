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
    
    // For Subcategory, we show "Starting from" price or lowest package price
    $price = null;
    if ($model instanceof \App\Models\Subcategory) {
        $price = $model->products->flatMap->packages->min('selling_price') ?? $model->products->min('selling_price');
    } else {
        $price = $model->packages->min('selling_price') ?? $model->selling_price;
    }
@endphp

<div {{ $attributes->merge(['class' => 'group bg-surface-container-low rounded-2xl overflow-hidden transition-all duration-300 hover:scale-105 border border-transparent hover:border-primary-container/30 hover:shadow-[0_0_40px_rgba(0,240,255,0.15)]']) }}
     data-slug="{{ $model->slug }}"
     @if($modalSlug) onclick="openSubcategoryModal(@js($modalSlug), @js($modalProductId))" @endif>
    
    <div class="aspect-square relative overflow-hidden bg-surface-container-lowest">
        <img class="w-full h-full object-contain p-6 transition-transform duration-500 group-hover:scale-110" 
             src="{{ $image }}" 
             alt="{{ $name }}"
             onerror="this.onerror=null;this.src=@js($fallbackImage);">
        
        @if($model->is_featured)
            <div class="absolute top-3 {{ $locale == 'ar' ? 'left-3' : 'right-3' }} bg-secondary-container text-on-secondary-container px-2 py-1 rounded text-[10px] font-label font-black border border-secondary-container/30 uppercase tracking-tighter shadow-[0_0_10px_rgba(254,0,254,0.5)]">
                {{ $locale == 'ar' ? 'مميز' : 'HOT' }}
            </div>
        @endif

        <div class="absolute bottom-3 {{ $locale == 'ar' ? 'right-3' : 'left-3' }} bg-background/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-label text-primary-container border border-primary-container/30 uppercase tracking-tighter">
            {{ $locale == 'ar' ? 'تسليم فوري' : 'Instant Delivery' }}
        </div>
    </div>

    <div class="p-4">
        <h3 class="font-headline font-bold text-sm tracking-wide mb-1 uppercase group-hover:text-primary-container transition-colors truncate">
            {{ $name }}
        </h3>
        <p class="text-xs text-on-surface-variant mb-4 truncate">{{ $categoryName }}</p>
        
        <div class="flex justify-between items-center">
            <div class="flex flex-col">
                @if($price)
                    <span class="text-[10px] text-outline uppercase tracking-widest font-bold">{{ $locale == 'ar' ? 'تبدأ من' : 'From' }}</span>
                    <span class="text-primary-container font-black text-lg leading-none">${{ number_format($price, 2) }}</span>
                @endif
            </div>
            
            <button class="p-2 bg-surface-container-highest rounded-lg text-secondary-container hover:bg-secondary-container hover:text-on-secondary transition-all shadow-inner active:scale-90" aria-label="{{ $locale == 'ar' ? 'Ø´Ø±Ø§Ø¡' : 'Buy' }}">
                <span class="material-symbols-outlined text-sm">bolt</span>
            </button>
        </div>
    </div>
</div>
