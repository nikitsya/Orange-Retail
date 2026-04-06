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
                [
                    'minimum_range' => [$minimumRangeStart, $minimumRangeEnd],
                    'healthy_range' => [$healthyRangeStart, $healthyRangeEnd],
                ] = $this->inventoryProfile((string) $product['category']);

                $minimumStockLevel = random_int($minimumRangeStart, $minimumRangeEnd);
                $inventoryStateRoll = random_int(1, 100);

                if ($inventoryStateRoll <= 10) {
                    $stock = random_int(max(0, $minimumStockLevel - 20), max(0, $minimumStockLevel - 1));
                } elseif ($inventoryStateRoll <= 18) {
                    $stock = $minimumStockLevel;
                } else {
                    $stock = random_int(
                        max($minimumStockLevel + 5, $healthyRangeStart),
                        max($minimumStockLevel + 20, $healthyRangeEnd),
                    );
                }

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
                    'stock' => $stock,
                    'minimum_stock_level' => $minimumStockLevel,
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

    /**
     * @return array{minimum_range: array{int, int}, healthy_range: array{int, int}}
     */
    protected function inventoryProfile(string $category): array
    {
        return match ($category) {
            'Fresh Food' => [
                'minimum_range' => [20, 45],
                'healthy_range' => [55, 130],
            ],
            'Drinks' => [
                'minimum_range' => [30, 70],
                'healthy_range' => [90, 220],
            ],
            'Food Cupboard' => [
                'minimum_range' => [35, 80],
                'healthy_range' => [100, 240],
            ],
            'Treats & Snacks' => [
                'minimum_range' => [28, 65],
                'healthy_range' => [85, 190],
            ],
            'Household' => [
                'minimum_range' => [20, 50],
                'healthy_range' => [60, 150],
            ],
            'Pets' => [
                'minimum_range' => [16, 40],
                'healthy_range' => [45, 120],
            ],
            'Health & Beauty' => [
                'minimum_range' => [14, 35],
                'healthy_range' => [35, 90],
            ],
            'Baby & Toddler' => [
                'minimum_range' => [16, 42],
                'healthy_range' => [45, 110],
            ],
            'Home & Furniture' => [
                'minimum_range' => [6, 18],
                'healthy_range' => [15, 45],
            ],
            default => [
                'minimum_range' => [20, 45],
                'healthy_range' => [50, 120],
            ],
        };
    }
}
