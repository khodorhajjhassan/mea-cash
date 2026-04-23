<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPackage;
use App\Services\Media\ImageStorageService;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'meacash_cart';

    public function __construct(
        private readonly ImageStorageService $imageService,
    ) {}

    /**
     * Add a product/package to the cart.
     */
    public function add(int $productId, ?int $packageId, int $quantity = 1, array $formData = []): array
    {
        $product = Product::findOrFail($productId);
        $package = $packageId ? ProductPackage::findOrFail($packageId) : null;

        $locale = app()->getLocale();
        $resolvedType = $product->resolvedProductType();
        $price = $package
            ? $package->selling_price
            : $product->selling_price;
        $costPrice = $package ? $package->cost_price : $product->cost_price;

        $item = [
            'id' => uniqid('cart_', true),
            'product_id' => $product->id,
            'package_id' => $package?->id,
            'product_name' => $product->{"name_{$locale}"},
            'package_name' => $package ? $package->{"name_{$locale}"} : null,
            'image' => $product->image ? $this->imageService->url($product->image) : null,
            'unit_price' => (float) $price,
            'cost_price' => (float) $costPrice,
            'quantity' => $quantity,
            'form_data' => $formData,
        ];

        $cart = $this->get();
        $cart[] = $item;
        Session::put(self::SESSION_KEY, $cart);

        return $item;
    }

    /**
     * Remove an item by its cart ID.
     */
    public function remove(string $cartItemId): void
    {
        $cart = collect($this->get())->filter(fn ($item) => $item['id'] !== $cartItemId)->values()->all();
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Get all cart items.
     */
    public function get(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Get item count.
     */
    public function count(): int
    {
        return count($this->get());
    }

    /**
     * Calculate the total price.
     */
    public function total(): float
    {
        return collect($this->get())->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
    }

    /**
     * Clear the cart.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
