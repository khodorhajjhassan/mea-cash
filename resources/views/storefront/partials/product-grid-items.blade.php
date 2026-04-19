@forelse($products as $product)
    <x-noir.product-card :model="$product" class="animate-fade-in" />
@empty
    <div class="col-span-full glass-panel p-8 text-center rounded-3xl md:p-20">
        <span class="material-symbols-outlined text-5xl text-primary-container/70 animate-pulse md:text-6xl">search_off</span>
        <p class="mt-4 text-xl font-bold text-on-surface">{{ __('No products available') }}</p>
        <p class="mt-2 text-on-surface-variant">{{ __('Try another category or come back later.') }}</p>
    </div>
@endforelse
