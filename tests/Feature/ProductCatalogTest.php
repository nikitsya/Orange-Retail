<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_can_open_customer_catalog(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Product::query()->create([
            'sku' => 'CAT-APPLE-001',
            'barcode' => '5391234567004',
            'name' => 'Customer Apples',
            'brand' => 'Tesco',
            'category' => 'Fresh Fruit',
            'subcategory' => 'Apples',
            'image_url' => null,
            'unit_type' => 'pack',
            'pack_size' => '6 apples',
        ]);

        $this->actingAs($user)
            ->get('/catalog')
            ->assertOk()
            ->assertSee('Browse supermarket products')
            ->assertSee('Customer Apples');
    }

    public function test_customer_catalog_search_matches_brand_and_category(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Product::query()->create([
            'sku' => 'CAT-MILK-001',
            'barcode' => '5391234567005',
            'name' => 'Fresh Milk',
            'brand' => 'Avonmore',
            'category' => 'Dairy',
            'subcategory' => 'Milk',
            'image_url' => null,
            'unit_type' => 'carton',
            'pack_size' => '1 litre',
        ]);

        Product::query()->create([
            'sku' => 'CAT-BREAD-001',
            'barcode' => '5391234567006',
            'name' => 'Brown Bread',
            'brand' => 'Brennans',
            'category' => 'Bakery',
            'subcategory' => 'Bread',
            'image_url' => null,
            'unit_type' => 'each',
            'pack_size' => null,
        ]);

        $this->actingAs($user)
            ->get('/catalog?search=Dairy')
            ->assertOk()
            ->assertSee('Fresh Milk')
            ->assertDontSee('Brown Bread');

        $this->actingAs($user)
            ->get('/catalog?search=Avonmore')
            ->assertOk()
            ->assertSee('Fresh Milk')
            ->assertDontSee('Brown Bread');
    }

    public function test_regular_user_can_open_product_details_page(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $product = Product::query()->create([
            'sku' => 'CAT-PASTA-001',
            'barcode' => '5391234567007',
            'name' => 'Italian Pasta',
            'brand' => 'Barilla',
            'category' => 'Grocery',
            'subcategory' => 'Pasta',
            'image_url' => 'https://example.com/images/pasta.jpg',
            'unit_type' => 'pack',
            'pack_size' => '500g',
        ]);

        $this->actingAs($user)
            ->get("/catalog/{$product->id}")
            ->assertOk()
            ->assertSee('Italian Pasta')
            ->assertSee('Barilla')
            ->assertSee('Open image in a new tab');
    }

    public function test_regular_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $product = Product::query()->create([
            'sku' => 'CART-SOUP-001',
            'barcode' => '5391234567008',
            'name' => 'Tomato Soup',
            'brand' => 'Heinz',
            'category' => 'Grocery',
            'subcategory' => 'Soup',
            'image_url' => null,
            'unit_type' => 'tin',
            'pack_size' => '400g',
        ]);

        $this->actingAs($user)
            ->post("/cart/{$product->id}")
            ->assertRedirect('/cart');

        $this->actingAs($user)
            ->get('/cart')
            ->assertOk()
            ->assertSee('Your cart')
            ->assertSee('Tomato Soup')
            ->assertSee('1');
    }

    public function test_regular_user_can_remove_product_from_cart(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $product = Product::query()->create([
            'sku' => 'CART-TEA-001',
            'barcode' => '5391234567009',
            'name' => 'Breakfast Tea',
            'brand' => 'Lyons',
            'category' => 'Drinks',
            'subcategory' => 'Tea',
            'image_url' => null,
            'unit_type' => 'box',
            'pack_size' => '80 bags',
        ]);

        $this->actingAs($user)->withSession([
            'cart' => [
                (string)$product->id => ['quantity' => 1],
            ],
        ])->delete("/cart/{$product->id}")
            ->assertRedirect('/cart');

        $this->actingAs($user)
            ->get('/cart')
            ->assertOk()
            ->assertSee('Your cart is empty')
            ->assertDontSee('Breakfast Tea');
    }

    public function test_catalog_is_paginated_by_twenty_products_per_page(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        foreach (range(1, 21) as $index) {
            Product::query()->create([
                'sku' => sprintf('CAT-PAG-%03d', $index),
                'barcode' => sprintf('5391234568%03d', $index),
                'name' => sprintf('Paged Product %02d', $index),
                'brand' => 'Orange Retail',
                'category' => 'Drinks',
                'subcategory' => 'Soft Drinks',
                'image_url' => null,
                'unit_type' => 'bottle',
                'pack_size' => '500ml',
            ]);
        }

        $this->actingAs($user)
            ->get('/catalog')
            ->assertOk()
            ->assertSee('Paged Product 01')
            ->assertSee('Paged Product 20')
            ->assertDontSee('Paged Product 21')
            ->assertSee('?page=2', false);

        $this->actingAs($user)
            ->get('/catalog?page=2')
            ->assertOk()
            ->assertSee('Paged Product 21')
            ->assertDontSee('Paged Product 01');
    }

    public function test_catalog_pagination_shows_a_centered_five_page_window(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        foreach (range(1, 160) as $index) {
            Product::query()->create([
                'sku' => sprintf('CAT-WIN-%03d', $index),
                'barcode' => sprintf('5399999999%03d', $index),
                'name' => sprintf('Window Product %03d', $index),
                'brand' => 'Orange Retail',
                'category' => 'Treats & Snacks',
                'subcategory' => 'Biscuits',
                'image_url' => null,
                'unit_type' => 'pack',
                'pack_size' => '1 pack',
            ]);
        }

        $this->actingAs($user)
            ->get('/catalog?page=3')
            ->assertOk()
            ->assertSee('?page=1', false)
            ->assertSee('?page=2', false)
            ->assertSee('?page=3', false)
            ->assertSee('?page=4', false)
            ->assertSee('?page=5', false)
            ->assertDontSee('?page=6', false);

        $this->actingAs($user)
            ->get('/catalog?page=5')
            ->assertOk()
            ->assertDontSee('?page=1', false)
            ->assertDontSee('?page=2', false)
            ->assertSee('?page=3', false)
            ->assertSee('?page=4', false)
            ->assertSee('?page=5', false)
            ->assertSee('?page=6', false)
            ->assertSee('?page=7', false)
            ->assertDontSee('?page=8', false);
    }
}
