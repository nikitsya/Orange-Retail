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
            'description' => 'Crisp apples for the customer catalog.',
            'image_url' => null,
            'unit_type' => 'pack',
            'pack_size' => '6 apples',
            'weight_value' => 0.90,
            'weight_unit' => 'kg',
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
            'description' => 'Whole milk carton.',
            'image_url' => null,
            'unit_type' => 'carton',
            'pack_size' => '1 litre',
            'weight_value' => null,
            'weight_unit' => null,
        ]);

        Product::query()->create([
            'sku' => 'CAT-BREAD-001',
            'barcode' => '5391234567006',
            'name' => 'Brown Bread',
            'brand' => 'Brennans',
            'category' => 'Bakery',
            'subcategory' => 'Bread',
            'description' => 'Sliced brown bread loaf.',
            'image_url' => null,
            'unit_type' => 'each',
            'pack_size' => null,
            'weight_value' => null,
            'weight_unit' => null,
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
}
