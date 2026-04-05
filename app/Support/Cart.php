<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

class Cart
{
    /**
     * @param array<string, array{quantity?: int}> $cart
     * @return \Illuminate\Support\Collection<int, array{product: \App\Models\Product, quantity: int, line_total: float}>
     */
    public static function items(array $cart): Collection
    {
        $productIds = collect(array_keys($cart))
            ->map(static fn(string $productId): int => (int)$productId)
            ->filter(static fn(int $productId): bool => $productId > 0)
            ->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        return $productIds
            ->map(function (int $productId) use ($cart, $products): ?array {
                $product = $products->get($productId);

                if (!$product) {
                    return null;
                }

                $quantity = max(1, (int)($cart[(string)$productId]['quantity'] ?? 1));

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => round(((float)($product->price_value ?? 0)) * $quantity, 2),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @param \Illuminate\Support\Collection<int, array{quantity: int}> $items
     */
    public static function itemCount(Collection $items): int
    {
        return (int)$items->sum('quantity');
    }

    /**
     * @param \Illuminate\Support\Collection<int, array{line_total: float}> $items
     */
    public static function subtotal(Collection $items): float
    {
        return (float)$items->sum('line_total');
    }
}
