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

        if (! is_file($path)) {
            throw new RuntimeException("JSON file not found: {$path}");
        }

        try {
            $payload = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new RuntimeException('Unable to decode Irish supermarket products JSON.', 0, $exception);
        }

        if (! is_array($payload) || ! isset($payload['products']) || ! is_array($payload['products'])) {
            throw new RuntimeException('Invalid JSON structure: expected a top-level "products" array.');
        }

        $products = collect($payload['products'])
            ->map(function (array $product): array {
                return [
                    'sku' => (string) $product['sku'],
                    'barcode' => $product['barcode'] ?: null,
                    'name' => (string) $product['name'],
                    'brand' => (string) $product['brand'],
                    'category' => (string) $product['category'],
                    'subcategory' => (string) $product['subcategory'],
                    'description' => (string) $product['description'],
                    'image_url' => $product['image_url'] ?: null,
                    'unit_type' => (string) $product['unit_type'],
                    'pack_size' => $product['pack_size'] ?: null,
                    'weight_value' => $product['weight_value'] !== null
                        ? (float) $product['weight_value']
                        : null,
                    'weight_unit' => $product['weight_unit'] ?: null,
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
                    'description',
                    'image_url',
                    'unit_type',
                    'pack_size',
                    'weight_value',
                    'weight_unit',
                ],
            );
        }
    }
}
