<?php

namespace App\Services;

use App\Enums\FulfillmentType;
use App\Enums\OrderStatus;
use App\Exceptions\OutOfStockException;
use App\Models\Order;
use App\Models\ProductCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderFulfilledMail;

class OrderFulfillmentService
{
    /**
     * Fulfill an order with manual data or automated inventory.
     *
     * @param Order $order
     * @param array $data
     * @return Order
     * @throws \Throwable
     */
    public function fulfill(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $type = FulfillmentType::from($data['fulfillment_type']);
            
            // 1. Prepare fulfillment payload
            $fulfillmentPayload = [
                'type' => $type->value,
                'admin_note' => $data['admin_note'] ?? '',
                'data' => match ($type) {
                    FulfillmentType::Key => ['keys' => $data['keys'] ?? ''],
                    FulfillmentType::Account => [
                        'user' => $data['account_user'] ?? '',
                        'pass' => $data['account_pass'] ?? '',
                        'link' => $data['account_link'] ?? '',
                    ],
                    FulfillmentType::Topup => ['transaction_id' => $data['transaction_id'] ?? ''],
                    FulfillmentType::Note => ['note' => $data['admin_note'] ?? ''],
                },
            ];

            // 2. Handle Inventory Lock if it's a 'Key' fulfillment (optional automated logic if needed later)
            // For now, it's manual based on the current UI, but we'll implement the pattern for future-proofing
            // if ($type === FulfillmentType::Key && empty($data['keys'])) {
            //     $this->claimInventoryCodes($order);
            // }

            // 3. Update Order status and data
            $order->update([
                'status' => OrderStatus::Completed,
                'fulfilled_at' => now(),
                'fulfillment_data' => array_merge($order->fulfillment_data ?? [], ['fulfillment' => $fulfillmentPayload]),
            ]);

            // 4. Notifications
            if (!empty($data['notify_email'])) {
                Mail::to($order->user->email)->send(new OrderFulfilledMail($order));
            }

            return $order;
        });
    }

    /**
     * Future logic to automatically claim codes from inventory using lockForUpdate.
     */
    protected function claimInventoryCodes(Order $order): void
    {
        $requiredQuantity = $order->quantity;
        
        $codes = ProductCode::query()
            ->where('product_id', $order->product_id)
            ->whereNull('order_id')
            ->where('is_used', false)
            ->lockForUpdate() // Crucial for race conditions
            ->limit($requiredQuantity)
            ->get();

        if ($codes->count() < $requiredQuantity) {
            throw new OutOfStockException("Insufficient items in stock. Required: {$requiredQuantity}, Available: " . $codes->count());
        }

        foreach ($codes as $code) {
            $code->update([
                'order_id' => $order->id,
                'is_used' => true,
                'used_at' => now(),
            ]);
        }
    }

    public function markAsFailed(Order $order): Order
    {
        $order->update(['status' => OrderStatus::Failed]);
        return $order;
    }

    public function processRefund(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::Refunded]);
            // Logic to refund wallet balance should be called here too
            return $order;
        });
    }
}
