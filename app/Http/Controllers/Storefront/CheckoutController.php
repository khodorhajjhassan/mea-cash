<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPackage;
use App\Services\CartService;
use App\Services\WalletService;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly WalletService $walletService,
    ) {}

    /**
     * Show checkout page with cart summary + wallet balance.
     */
    public function show()
    {
        $items = $this->cartService->get();

        if (empty($items)) {
            return redirect()->route('store.home')->with('error', __('storefront.checkout.empty_cart'));
        }

        $user = auth()->user();
        $balance = $this->walletService->getBalance($user);
        $total = $this->cartService->total();

        return view('storefront.checkout', [
            'items' => $items,
            'total' => $total,
            'balance' => $balance,
            'hasSufficientBalance' => bccomp($balance, (string) $total, 2) >= 0,
        ]);
    }

    /**
     * Process checkout: debit wallet, create orders.
     */
    public function process(Request $request)
    {
        $items = $this->cartService->get();

        if (empty($items)) {
            return redirect()->route('store.home')->with('error', __('storefront.checkout.empty_cart'));
        }

        $user = auth()->user();
        $total = $this->cartService->total();

        try {
            $orders = DB::transaction(function () use ($items, $user, $total) {
                // Check total balance first
                $balance = $this->walletService->getBalance($user);
                if (bccomp($balance, (string) $total, 2) === -1) {
                    throw new InsufficientBalanceException("Insufficient balance to complete purchase.");
                }

                $createdOrders = [];

                foreach ($items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $package = $item['package_id'] ? ProductPackage::find($item['package_id']) : null;

                    $unitPrice = $item['unit_price'];
                    $costPrice = $item['cost_price'];
                    $quantity = $item['quantity'];
                    $totalPrice = $unitPrice * $quantity;
                    $totalCost = $costPrice * $quantity;
                    $profit = $totalPrice - $totalCost;

                    $orderNumber = 'MC-' . strtoupper(Str::random(8));

                    $order = Order::create([
                        'order_number' => $orderNumber,
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'package_id' => $package?->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'cost_price' => $totalCost,
                        'profit' => $profit,
                        'status' => OrderStatus::Pending,
                        'delivery_type' => $product->delivery_type,
                        'fulfillment_data' => !empty($item['form_data'])
                            ? ['user_input' => $item['form_data']]
                            : null,
                    ]);

                    // Debit wallet for this order
                    $locale = app()->getLocale();
                    $description = "Purchase: {$product->{"name_{$locale}"}} - Order #{$orderNumber}";
                    $this->walletService->debit(
                        user: $user,
                        amount: (string) $totalPrice,
                        description: $description,
                        reference: $order,
                    );

                    $createdOrders[] = $order;
                }

                return $createdOrders;
            });

            // Clear cart after successful checkout
            $this->cartService->clear();

            // Redirect to first order confirmation (or a summary page)
            $firstOrder = $orders[0];

            return redirect()->route('store.confirmation', $firstOrder->order_number)
                ->with('success', __('storefront.checkout.success'));
        } catch (InsufficientBalanceException) {
            return back()->with('error', __('storefront.checkout.insufficient_balance'));
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', __('storefront.checkout.failed'));
        }
    }

    /**
     * Order confirmation page.
     */
    public function confirmation(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['product.subcategory.category', 'package'])
            ->firstOrFail();

        return view('storefront.confirmation', compact('order'));
    }
}
