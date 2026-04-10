<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $this->ensureCustomer($request);
        $this->trimCheckoutInput($request);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'shipping_address_line_1' => ['required', 'string', 'max:255'],
            'shipping_address_line_2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_county' => ['nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = Cart::items($request->session()->get('cart', []));

        if ($items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($validated, $items, $request): Order {
            $lockedProducts = Product::query()
                ->whereIn('id', $items->pluck('product.id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $preparedItems = $items->map(function (array $item) use ($lockedProducts): array {
                /** @var \App\Models\Product|null $product */
                $product = $lockedProducts->get($item['product']->id);
                $quantity = (int)$item['quantity'];

                if (!$product || !$product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'One or more products are no longer available.',
                    ]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "{$product->name} only has {$product->stock} item(s) left in stock.",
                    ]);
                }

                $unitPrice = round((float)($product->price_value ?? 0), 2);
                $lineTotal = round($unitPrice * $quantity, 2);

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            });

            $subtotal = round((float)$preparedItems->sum('line_total'), 2);

            $order = Order::query()->create([
                'user_id' => $request->user()->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => Order::STATUS_PENDING,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'shipping_address_line_1' => $validated['shipping_address_line_1'],
                'shipping_address_line_2' => $validated['shipping_address_line_2'] ?: null,
                'shipping_city' => $validated['shipping_city'],
                'shipping_county' => $validated['shipping_county'] ?: null,
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'notes' => $validated['notes'] ?: null,
                'item_count' => (int)$preparedItems->sum('quantity'),
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'placed_at' => now(),
            ]);

            $order->items()->createMany($preparedItems->map(function (array $item): array {
                /** @var \App\Models\Product $product */
                $product = $item['product'];

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_brand' => $product->brand,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ];
            })->all());

            $preparedItems->each(function (array $item) use ($request): void {
                /** @var \App\Models\Product $product */
                $product = $item['product'];

                $product->decrement('stock', $item['quantity']);
                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'type' => 'sale',
                    'quantity_change' => $item['quantity'] * -1,
                    'note' => 'Reserved by customer order checkout.',
                    'occurred_at' => now(),
                ]);
            });

            return $order->load('items');
        });

        $request->session()->forget('cart');

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Order placed successfully.');
    }

    protected function trimCheckoutInput(Request $request): void
    {
        $fields = [
            'customer_name',
            'customer_email',
            'shipping_address_line_1',
            'shipping_address_line_2',
            'shipping_city',
            'shipping_county',
            'shipping_postal_code',
            'notes',
        ];

        $request->merge(
            collect($fields)
                ->mapWithKeys(fn (string $field): array => [
                    $field => is_string($request->input($field))
                        ? trim($request->input($field))
                        : $request->input($field),
                ])
                ->all()
        );
    }

    protected function ensureCustomer(Request $request): void
    {
        if ($request->user()?->role === 'admin') {
            abort(403);
        }
    }

    public function create(Request $request): View|RedirectResponse
    {
        $this->ensureCustomer($request);

        $items = Cart::items($request->session()->get('cart', []));

        if ($items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => 'Add products to your cart before checkout.']);
        }

        return view('checkout.create', [
            'items' => $items,
            'itemCount' => Cart::itemCount($items),
            'subtotal' => Cart::subtotal($items),
        ]);
    }

    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()?->role === 'admin') {
            return redirect()->route('admin.orders.index');
        }

        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest('placed_at')
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $isAdmin = $request->user()?->role === 'admin';

        abort_unless($isAdmin || $order->user_id === $request->user()?->id, 403);

        $order->load('items', 'user');

        return view('orders.show', [
            'order' => $order,
            'isAdmin' => $isAdmin,
            'statuses' => Order::statuses(),
        ]);
    }

    public function adminIndex(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $status = trim($request->string('status')->toString());

        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->latest('placed_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
            'statuses' => Order::statuses(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::statuses())],
        ]);

        $newStatus = $validated['status'];

        if ($order->status === $newStatus) {
            return redirect()
                ->route('admin.orders.index')
                ->with('status', 'Order status was already up to date.');
        }

        if ($order->status === Order::STATUS_CANCELLED && $newStatus !== Order::STATUS_CANCELLED) {
            return redirect()
                ->route('admin.orders.index')
                ->withErrors(['status' => 'Cancelled orders cannot be reopened automatically because stock was already returned.']);
        }

        DB::transaction(function () use ($order, $newStatus, $request): void {
            $order->load('items.product');

            if ($newStatus === Order::STATUS_CANCELLED && $order->status !== Order::STATUS_CANCELLED) {
                $productIds = $order->items
                    ->pluck('product_id')
                    ->filter()
                    ->all();

                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $order->items->each(function ($item) use ($products, $request, $order): void {
                    $product = $products->get($item->product_id);

                    if ($product) {
                        $product->increment('stock', $item->quantity);
                        StockMovement::query()->create([
                            'product_id' => $product->id,
                            'user_id' => $request->user()?->id,
                            'type' => 'order_cancel_return',
                            'quantity_change' => $item->quantity,
                            'note' => "Stock returned after order {$order->order_number} was cancelled.",
                            'occurred_at' => now(),
                        ]);
                    }
                });
            }

            $order->update([
                'status' => $newStatus,
            ]);
        });

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'Order status updated successfully.');
    }
}
