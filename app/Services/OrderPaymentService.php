<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class OrderPaymentService
{
    public function markStripeSessionPaid(string $checkoutSessionId, ?string $paymentIntentId = null): ?Order
    {
        return DB::transaction(function () use ($checkoutSessionId, $paymentIntentId): ?Order {
            $order = Order::query()
                ->where('stripe_checkout_session_id', $checkoutSessionId)
                ->lockForUpdate()
                ->first();

            if (! $order) {
                return null;
            }

            $order->update([
                'status' => $order->status === Order::STATUS_AWAITING_PAYMENT
                    ? Order::STATUS_PENDING
                    : $order->status,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'stripe_payment_intent_id' => $paymentIntentId ?: $order->stripe_payment_intent_id,
                'paid_at' => $order->paid_at ?: now(),
            ]);

            return $order->refresh();
        });
    }

    public function markStripeSessionProcessing(string $checkoutSessionId, ?string $paymentIntentId = null): ?Order
    {
        return DB::transaction(function () use ($checkoutSessionId, $paymentIntentId): ?Order {
            $order = Order::query()
                ->where('stripe_checkout_session_id', $checkoutSessionId)
                ->lockForUpdate()
                ->first();

            if (! $order || $order->payment_status === Order::PAYMENT_STATUS_PAID) {
                return $order;
            }

            $order->update([
                'payment_status' => Order::PAYMENT_STATUS_PROCESSING,
                'stripe_payment_intent_id' => $paymentIntentId ?: $order->stripe_payment_intent_id,
            ]);

            return $order->refresh();
        });
    }

    public function cancelStripeSessionOrder(string $checkoutSessionId, string $paymentStatus): ?Order
    {
        return DB::transaction(function () use ($checkoutSessionId, $paymentStatus): ?Order {
            $order = Order::query()
                ->where('stripe_checkout_session_id', $checkoutSessionId)
                ->lockForUpdate()
                ->first();

            if (! $order || $order->payment_status === Order::PAYMENT_STATUS_PAID) {
                return $order;
            }

            return $this->cancelOrderAndRestoreStock(
                $order,
                null,
                $paymentStatus,
                "Stock returned after Stripe payment was {$paymentStatus} for order {$order->order_number}."
            );
        });
    }

    public function cancelOrderAndRestoreStock(
        Order $order,
        ?int $userId = null,
        ?string $paymentStatus = null,
        ?string $stockNote = null
    ): Order {
        return DB::transaction(function () use ($order, $userId, $paymentStatus, $stockNote): Order {
            $order = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $wasCancelled = $order->status === Order::STATUS_CANCELLED;

            if (! $wasCancelled) {
                $order->load('items.product');

                $productIds = $order->items
                    ->pluck('product_id')
                    ->filter()
                    ->all();

                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $order->items->each(function ($item) use ($products, $userId, $order, $stockNote): void {
                    $product = $products->get($item->product_id);

                    if ($product) {
                        $product->increment('stock', $item->quantity);
                        StockMovement::query()->create([
                            'product_id' => $product->id,
                            'user_id' => $userId,
                            'type' => 'order_cancel_return',
                            'quantity_change' => $item->quantity,
                            'note' => $stockNote ?: "Stock returned after order {$order->order_number} was cancelled.",
                            'occurred_at' => now(),
                        ]);
                    }
                });
            }

            $updates = [
                'status' => Order::STATUS_CANCELLED,
            ];

            if ($paymentStatus) {
                $updates['payment_status'] = $paymentStatus;
            }

            $order->update($updates);

            return $order->refresh();
        });
    }
}
