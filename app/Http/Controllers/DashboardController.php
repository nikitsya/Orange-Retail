<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);
    }

    public function admin(): View
    {
        $recentOrders = Order::query()
            ->with('user')
            ->latest('placed_at')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'productCount' => Product::query()->count(),
            'inactiveProducts' => Product::query()->where('is_active', false)->count(),
            'lowStockProducts' => Product::query()->where('stock', '<=', 5)->count(),
            'pendingOrders' => Order::query()->where('status', Order::STATUS_PENDING)->count(),
            'incomingDeliveries' => Product::query()
                ->whereNotNull('next_delivery_due_at')
                ->whereBetween('next_delivery_due_at', [now(), now()->addDays(7)])
                ->count(),
            'recentOrders' => $recentOrders,
            'recentMovements' => StockMovement::query()
                ->with('product')
                ->latest('occurred_at')
                ->limit(6)
                ->get(),
        ]);
    }
}
