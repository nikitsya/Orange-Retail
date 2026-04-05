<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
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

        $this->call(IrishSupermarketProductsSeeder::class);

        if (StockMovement::query()->doesntExist()) {
            Product::query()
                ->orderBy('id')
                ->limit(24)
                ->get()
                ->each(function (Product $product): void {
                    StockMovement::query()->create([
                        'product_id' => $product->id,
                        'user_id' => null,
                        'type' => 'seed_restock',
                        'quantity_change' => $product->stock,
                        'note' => 'Initial stock import from the seeded catalog.',
                        'occurred_at' => $product->last_restocked_at ?? now()->subDays(2),
                    ]);
                });
        }
    }
}
