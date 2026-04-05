<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_checkout_and_view_order_history(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $product = Product::query()->create([
            'sku' => 'CHECKOUT-001',
            'barcode' => '5391000000001',
            'name' => 'Checkout Apples',
            'brand' => 'Orange Retail',
            'category' => 'Fresh Food',
            'subcategory' => 'Fruit',
            'image_url' => null,
            'unit_type' => 'pack',
            'pack_size' => '6 apples',
            'price_value' => 3.50,
            'unit_price_display' => '€0.58/each',
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post("/cart/{$product->id}")
            ->assertRedirect('/cart');

        $response = $this->actingAs($user)
            ->post('/checkout', [
                'customer_name' => 'Store User',
                'customer_email' => 'user@example.com',
                'shipping_address_line_1' => '12 Main Street',
                'shipping_address_line_2' => 'Apartment 3',
                'shipping_city' => 'Dublin',
                'shipping_county' => 'Dublin',
                'shipping_postal_code' => 'D01AA01',
                'notes' => 'Ring the bell.',
            ]);

        $order = Order::query()->firstOrFail();

        $response->assertRedirect("/orders/{$order->id}");

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
            'item_count' => 1,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 9,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity_change' => -1,
        ]);

        $this->actingAs($user)
            ->get('/orders')
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Order history');
    }

    public function test_admin_can_cancel_an_order_and_restore_stock(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'user',
        ]);

        $product = Product::query()->create([
            'sku' => 'ORDER-CANCEL-001',
            'barcode' => '5391000000002',
            'name' => 'Cancelled Product',
            'brand' => 'Orange Retail',
            'category' => 'Drinks',
            'subcategory' => 'Juice',
            'image_url' => null,
            'unit_type' => 'bottle',
            'pack_size' => '1 litre',
            'price_value' => 2.25,
            'unit_price_display' => '€2.25/l',
            'stock' => 3,
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'order_number' => 'ORD-TEST-100001',
            'status' => Order::STATUS_PENDING,
            'customer_name' => 'Customer',
            'customer_email' => 'customer@example.com',
            'shipping_address_line_1' => '1 Market Road',
            'shipping_address_line_2' => null,
            'shipping_city' => 'Cork',
            'shipping_county' => 'Cork',
            'shipping_postal_code' => 'T12AB12',
            'notes' => null,
            'item_count' => 2,
            'subtotal' => 4.50,
            'total' => 4.50,
            'placed_at' => now(),
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'product_brand' => $product->brand,
            'quantity' => 2,
            'unit_price' => 2.25,
            'line_total' => 4.50,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}", [
                'status' => Order::STATUS_CANCELLED,
            ])
            ->assertRedirect('/admin/orders');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CANCELLED,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'order_cancel_return',
            'quantity_change' => 2,
        ]);
    }

    public function test_admin_can_update_stock_center_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::query()->create([
            'sku' => 'STOCK-001',
            'barcode' => '5391000000003',
            'name' => 'Warehouse Milk',
            'brand' => 'Orange Retail',
            'category' => 'Fresh Food',
            'subcategory' => 'Milk',
            'image_url' => null,
            'unit_type' => 'carton',
            'pack_size' => '1 litre',
            'price_value' => 1.95,
            'unit_price_display' => '€1.95/l',
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/stock/{$product->id}", [
                'stock' => 12,
                'last_restocked_at' => '2026-04-05 10:00:00',
                'next_delivery_due_at' => '2026-04-09 09:30:00',
                'stock_note' => 'Supplier confirmed a new delivery window.',
            ])
            ->assertRedirect('/admin/stock');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 12,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'restock',
            'quantity_change' => 8,
        ]);

        $this->actingAs($admin)
            ->get('/admin/stock')
            ->assertOk()
            ->assertSee('Warehouse and delivery planning')
            ->assertSee('Warehouse Milk');
    }
}
