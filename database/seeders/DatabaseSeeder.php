<?php

namespace Database\Seeders;

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
    }
}
