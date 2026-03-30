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
            'name' => 'Inventory Item',
            'sku' => 'INV-100',
            'category' => 'General',
            'price' => 14.50,
            'stock' => 10,
            'description' => 'Seed product',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/products');
        $this->assertAuthenticatedAs($admin);

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
            'name' => 'Store Cheese',
            'sku' => 'DAI-501',
            'category' => 'Dairy',
            'price' => 7.25,
            'stock' => 18,
            'description' => 'Managed by the store team.',
            'is_active' => '1',
        ])->assertRedirect('/products');

        $product = Product::query()->where('sku', 'DAI-501')->firstOrFail();

        $this->put("/products/{$product->id}", [
            'name' => 'Store Cheese Premium',
            'sku' => 'DAI-501',
            'category' => 'Dairy',
            'price' => 8.10,
            'stock' => 22,
            'description' => 'Updated inventory record.',
            'is_active' => '1',
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Store Cheese Premium',
            'stock' => 22,
        ]);

        $this->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
