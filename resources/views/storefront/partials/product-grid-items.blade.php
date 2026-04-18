@forelse($products as $product)
    <x-noir.product-card :model="$product" class="animate-fade-in" />
@empty
    <div class="col-span-full glass-panel p-12 md:p-20 text-center rounded-3xl">
        <span class="material-symbols-outlined text-6xl text-primary-container/70 animate-pulse">search_off</span>
        <p class="mt-4 text-xl font-bold text-on-surface">{{ __('No products available') }}</p>
        <p class="mt-2 text-on-surface-variant">{{ __('Try another category or come back later.') }}</p>
    </div>
@endforelse
