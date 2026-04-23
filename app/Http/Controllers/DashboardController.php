<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest('placed_at')
            ->limit(5)
            ->get();

        $cartItems = Cart::items($request->session()->get('cart', []));

        return view('dashboard.index', [
            'orders' => $orders,
            'cartItemCount' => Cart::itemCount($cartItems),
            'cartSubtotal' => Cart::subtotal($cartItems),
            'favoritesCount' => $request->user()->favoriteProducts()->count(),
        ]);
    }

    public function admin(): View
    {
        $selectedPanel = Arr::first(
            ['pending-orders', 'low-stock', 'inactive-products'],
            fn (string $panel) => request()->string('panel')->toString() === $panel,
            'pending-orders',
        );

        $pendingOrders = Order::query()
            ->with('user')
            ->where('status', Order::STATUS_PENDING)
            ->latest('placed_at')
            ->limit(12)
            ->get();

        $lowStockItems = Product::query()
            ->atOrBelowMinimumStock()
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(12)
            ->get();

        $inactiveProductItems = Product::query()
            ->where('is_active', false)
            ->orderBy('name')
            ->limit(12)
            ->get();

        return view('admin.dashboard', [
            'productCount' => Product::query()->count(),
            'inactiveProducts' => Product::query()->where('is_active', false)->count(),
            'lowStockProducts' => Product::query()->atOrBelowMinimumStock()->count(),
            'pendingOrders' => Order::query()->where('status', Order::STATUS_PENDING)->count(),
            'incomingDeliveries' => Product::query()
                ->whereNotNull('next_delivery_due_at')
                ->whereBetween('next_delivery_due_at', [now(), now()->addDays(7)])
                ->count(),
            'pendingOrderItems' => $pendingOrders,
            'lowStockItems' => $lowStockItems,
            'inactiveProductItems' => $inactiveProductItems,
            'recentMovements' => StockMovement::query()
                ->with('product')
                ->latest('occurred_at')
                ->limit(6)
                ->get(),
            'selectedPanel' => $selectedPanel,
        ]);
    }
}
