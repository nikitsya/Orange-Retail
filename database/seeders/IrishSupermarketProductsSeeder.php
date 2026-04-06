<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;
use Throwable;

class IrishSupermarketProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/supermarket_products.json');

        if (!is_file($path)) {
            throw new RuntimeException("JSON file not found: {$path}");
        }

        try {
            $payload = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new RuntimeException('Unable to decode Irish supermarket products JSON.', 0, $exception);
        }

        if (!is_array($payload) || !isset($payload['products']) || !is_array($payload['products'])) {
            throw new RuntimeException('Invalid JSON structure: expected a top-level "products" array.');
        }

        $products = collect($payload['products'])
            ->map(function (array $product): array {
                return [
                    'sku' => (string)$product['sku'],
                    'barcode' => $product['barcode'] ?: null,
                    'name' => (string)$product['name'],
                    'brand' => (string)$product['brand'],
                    'category' => (string)$product['category'],
                    'subcategory' => (string)$product['subcategory'],
                    'image_url' => $product['image_url'] ?: null,
                    'unit_type' => (string)$product['unit_type'],
                    'pack_size' => $product['pack_size'] ?: null,
                    'price_value' => $product['price_value'] !== null
                        ? (float)$product['price_value']
                        : null,
                    'unit_price_display' => $product['unit_price_display'] ?: null,
                    'stock' => 4 + ((((int)($product['id'] ?? 0)) * 3) % 28),
                    'minimum_stock_level' => 5 + ((((int)($product['id'] ?? 0)) % 4) * 2),
                    'is_active' => true,
                    'last_restocked_at' => now()->subDays((((int)($product['id'] ?? 1)) % 5) + 1),
                    'next_delivery_due_at' => now()->addDays((((int)($product['id'] ?? 1)) % 6) + 2),
                ];
            })
            ->values();

        foreach ($products->chunk(100) as $chunk) {
            Product::query()->upsert(
                $chunk->all(),
                ['sku'],
                [
                    'barcode',
                    'name',
                    'brand',
                    'category',
                    'subcategory',
                    'image_url',
                    'unit_type',
                    'pack_size',
                    'price_value',
                    'unit_price_display',
                    'stock',
                    'minimum_stock_level',
                    'is_active',
                    'last_restocked_at',
                    'next_delivery_due_at',
                ],
            );
        }
    }
}
