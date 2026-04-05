<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManagerAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_and_open_product_management(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        Product::query()->create([
            'sku' => 'INV-100',
            'barcode' => '5391234567001',
            'name' => 'Inventory Item',
            'brand' => 'Tesco',
            'category' => 'Fresh Fruit',
            'subcategory' => 'Apples',
            'image_url' => 'https://example.com/products/inventory-item.jpg',
            'unit_type' => 'pack',
            'pack_size' => '6 apples',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin Dashboard');

        $this->get('/products')
            ->assertOk()
            ->assertSee('Inventory list')
            ->assertSee('Inventory Item');
    }

    public function test_regular_user_is_redirected_to_the_user_dashboard(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('User Dashboard');
    }

    public function test_regular_user_cannot_access_product_management(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get('/products')
            ->assertForbidden();
    }

    public function test_guest_can_register_from_the_register_page(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'customer@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'New Customer',
            'email' => 'customer@example.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_can_create_update_and_delete_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $this->post('/products', [
            'sku' => 'TESCO-APP-001',
            'barcode' => '5391234567890',
            'name' => 'Tesco Gala Apples 6 Pack',
            'brand' => 'Tesco',
            'category' => 'Fresh Fruit',
            'subcategory' => 'Apples',
            'image_url' => 'https://example.com/images/apples.jpg',
            'price_value' => '3.49',
            'unit_price_display' => '€0.58/each',
            'unit_type' => 'pack',
            'pack_size' => '6 apples',
            'stock' => 24,
            'is_active' => '1',
        ])->assertRedirect('/products');

        $product = Product::query()->where('sku', 'TESCO-APP-001')->firstOrFail();

        $this->put("/products/{$product->id}", [
            'sku' => 'TESCO-APP-001',
            'barcode' => '5391234567890',
            'name' => 'Tesco Gala Apples 8 Pack',
            'brand' => 'Tesco',
            'category' => 'Fresh Fruit',
            'subcategory' => 'Apples',
            'image_url' => 'https://example.com/images/apples-8.jpg',
            'price_value' => '4.29',
            'unit_price_display' => '€0.54/each',
            'unit_type' => 'pack',
            'pack_size' => '8 apples',
            'stock' => 18,
            'is_active' => '1',
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Tesco Gala Apples 8 Pack',
            'pack_size' => '8 apples',
            'stock' => 18,
        ]);

        $this->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_can_search_products_by_name_and_saved_fields(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Product::query()->create([
            'sku' => 'TES-APPLE-001',
            'barcode' => '5391234567002',
            'name' => 'Tesco Gala Apples 6 Pack',
            'brand' => 'Tesco',
            'category' => 'Fresh Fruit',
            'subcategory' => 'Apples',
            'image_url' => null,
            'unit_type' => 'pack',
            'pack_size' => '6 apples',
        ]);

        Product::query()->create([
            'sku' => 'TES-BREAD-001',
            'barcode' => '5391234567003',
            'name' => 'Tesco Bakery Bread',
            'brand' => 'Tesco',
            'category' => 'Bakery',
            'subcategory' => 'Bread',
            'image_url' => null,
            'unit_type' => 'each',
            'pack_size' => null,
        ]);

        $this->actingAs($admin)
            ->get('/products?search=Apples')
            ->assertOk()
            ->assertSee('Tesco Gala Apples 6 Pack')
            ->assertDontSee('Tesco Bakery Bread');

        $this->actingAs($admin)
            ->get('/products?search=Bread')
            ->assertOk()
            ->assertSee('Tesco Bakery Bread')
            ->assertDontSee('Tesco Gala Apples 6 Pack');
    }
}
