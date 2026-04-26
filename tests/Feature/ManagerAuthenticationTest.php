<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

    public function test_google_redirect_route_starts_the_oauth_flow(): void
    {
        config()->set('services.google.client_id', 'google-client-id');
        config()->set('services.google.client_secret', 'google-client-secret');
        config()->set('services.google.redirect', 'http://localhost/auth/google/callback');

        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect();
        $this->assertStringStartsWith(
            'https://accounts.google.com/o/oauth2/v2/auth?',
            $response->headers->get('Location'),
        );
        $this->assertNotEmpty(session('google_oauth_state'));
    }

    public function test_google_callback_creates_a_regular_user_and_logs_them_in(): void
    {
        config()->set('services.google.client_id', 'google-client-id');
        config()->set('services.google.client_secret', 'google-client-secret');
        config()->set('services.google.redirect', 'http://localhost/auth/google/callback');

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'google-access-token',
                'token_type' => 'Bearer',
            ]),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-user-123',
                'name' => 'Google Customer',
                'email' => 'google.customer@example.com',
                'email_verified' => true,
                'picture' => 'https://example.com/google-avatar.png',
            ]),
        ]);

        $response = $this->withSession([
            'google_oauth_state' => 'known-state',
        ])->get('/auth/google/callback?state=known-state&code=oauth-code');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Google Customer',
            'email' => 'google.customer@example.com',
            'role' => 'user',
            'google_id' => 'google-user-123',
        ]);
    }

    public function test_google_callback_links_an_existing_regular_user_by_email(): void
    {
        config()->set('services.google.client_id', 'google-client-id');
        config()->set('services.google.client_secret', 'google-client-secret');
        config()->set('services.google.redirect', 'http://localhost/auth/google/callback');

        $user = User::factory()->create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
            'role' => 'user',
            'google_id' => null,
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'google-access-token',
                'token_type' => 'Bearer',
            ]),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-user-456',
                'name' => 'Existing Customer',
                'email' => 'existing@example.com',
                'email_verified' => true,
                'picture' => 'https://example.com/google-avatar-2.png',
            ]),
        ]);

        $response = $this->withSession([
            'google_oauth_state' => 'known-state',
        ])->get('/auth/google/callback?state=known-state&code=oauth-code');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user->fresh());

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'existing@example.com',
            'role' => 'user',
            'google_id' => 'google-user-456',
        ]);
    }

    public function test_google_callback_rejects_admin_accounts(): void
    {
        config()->set('services.google.client_id', 'google-client-id');
        config()->set('services.google.client_secret', 'google-client-secret');
        config()->set('services.google.redirect', 'http://localhost/auth/google/callback');

        $admin = User::factory()->create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'google-access-token',
                'token_type' => 'Bearer',
            ]),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-admin-123',
                'name' => 'Store Admin',
                'email' => 'admin@example.com',
                'email_verified' => true,
                'picture' => 'https://example.com/admin-avatar.png',
            ]),
        ]);

        $response = $this->withSession([
            'google_oauth_state' => 'known-state',
        ])->get('/auth/google/callback?state=known-state&code=oauth-code');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => 'admin@example.com',
            'role' => 'admin',
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

        $this->get('/products')
            ->assertOk()
            ->assertSee('data-open-delete-modal', false)
            ->assertSee('id="delete-product-modal"', false)
            ->assertSee('Yes, delete product');

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

    public function test_admin_can_open_inventory_modal_for_a_product_from_the_catalog_link(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::query()->create([
            'sku' => 'TES-MOZZ-001',
            'barcode' => '5391234567012',
            'name' => 'Creamfields Mozzarella 210G',
            'brand' => 'Creamfields',
            'category' => 'Fresh Food',
            'subcategory' => 'Cheese',
            'image_url' => 'https://example.com/images/mozzarella.jpg',
            'unit_type' => 'weight',
            'pack_size' => '210G',
            'price_value' => 0.75,
            'unit_price_display' => '€6.00/kg DR.WT',
            'stock' => 125,
            'minimum_stock_level' => 10,
            'is_active' => true,
        ]);

        $inventoryEditUrl = route('products.index', [
            'search' => $product->sku,
            'edit' => $product->id,
        ]);

        $this->actingAs($admin)
            ->get("/catalog/{$product->id}")
            ->assertOk()
            ->assertSee($inventoryEditUrl);

        $this->actingAs($admin)
            ->get($inventoryEditUrl)
            ->assertOk()
            ->assertSee('class="modal is-open"', false)
            ->assertSee("id=\"product-modal-{$product->id}\"", false)
            ->assertSee('Creamfields Mozzarella 210G');
    }
}
