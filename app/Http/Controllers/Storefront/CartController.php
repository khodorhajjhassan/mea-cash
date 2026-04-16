<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    /**
     * Show cart page.
     */
    public function show()
    {
        return view('storefront.cart', [
            'items' => $this->cartService->get(),
            'total' => $this->cartService->total(),
        ]);
    }

    /**
     * Add item to cart (AJAX or redirect).
     */
    public function add(Request $request)
    {
        // 1. Basic structure validation
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'package_id' => ['nullable', 'integer', 'exists:product_packages,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'form_data' => ['nullable', 'array'],
            'selected_form' => ['nullable', 'string'],
        ]);

        $product = \App\Models\Product::with(['formFields', 'packages'])->findOrFail($request->product_id);
        $locale = app()->getLocale();

        // 2. Dynamic form field validation
        $rules = [];
        $attributes = [];

        foreach ($product->formFields as $field) {
            $formKey = $field->ui_meta['form_key'] ?? null;
            
            // Only validate fields for the selected form OR global fields (form_key is null)
            if ($formKey === null || $formKey === $request->selected_form) {
                $fieldRules = $field->validation_rules ?? [];
                
                if ($field->is_required && !in_array('required', $fieldRules)) {
                    array_unshift($fieldRules, 'required');
                }
                
                if (!empty($fieldRules)) {
                    $rules["form_data.{$field->field_key}"] = $fieldRules;
                    $attributes["form_data.{$field->field_key}"] = $field->{"label_{$locale}"} ?? $field->field_key;
                }
            }
        }

        // 3. Product-specific constraints
        if ($product->product_type?->value === 'custom_quantity') {
            $rules['quantity'] = [
                'required', 'integer', 
                'min:' . ($product->min_quantity ?? 1), 
                'max:' . ($product->max_quantity ?? 10000)
            ];
        }

        // Perform validation
        $validatedData = $request->validate($rules, [], $attributes);

        // 4. Add to cart
        $item = $this->cartService->add(
            productId: $product->id,
            packageId: $request->package_id,
            quantity: $request->quantity ?? 1,
            formData: $request->form_data ?? [],
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item' => $item,
                'count' => $this->cartService->count(),
                'total' => $this->cartService->total(),
            ]);
        }

        return back()->with('success', __('storefront.cart.added'));
    }

    /**
     * Remove item from cart.
     */
    public function remove(string $itemId, Request $request)
    {
        $this->cartService->remove($itemId);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $this->cartService->count(),
                'total' => $this->cartService->total(),
            ]);
        }

        return back()->with('success', __('storefront.cart.removed'));
    }

    /**
     * Clear all items.
     */
    public function clear(Request $request)
    {
        $this->cartService->clear();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'count' => 0, 'total' => 0]);
        }

        return back()->with('success', __('storefront.cart.cleared'));
    }
}
