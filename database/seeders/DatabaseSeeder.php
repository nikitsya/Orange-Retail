<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'nikita@gmail.com'],
            [
                'name' => 'Nikita',
                'password' => Hash::make('smiichyk123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'user@supermarket.test'],
            [
                'name' => 'Store User',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
        );

        $products = [
            [
                'name' => 'Fresh Apples',
                'sku' => 'FRU-001',
                'category' => 'Produce',
                'price' => 2.49,
                'stock' => 120,
                'description' => 'Crisp apples prepared for daily shelf restocking.',
                'is_active' => true,
            ],
            [
                'name' => 'Whole Grain Bread',
                'sku' => 'BAK-014',
                'category' => 'Bakery',
                'price' => 3.59,
                'stock' => 42,
                'description' => 'Popular bakery item with steady morning demand.',
                'is_active' => true,
            ],
            [
                'name' => 'Organic Milk',
                'sku' => 'DAI-020',
                'category' => 'Dairy',
                'price' => 4.19,
                'stock' => 35,
                'description' => 'Chilled dairy product with expiration-sensitive stock.',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            );
        }
    }
}
