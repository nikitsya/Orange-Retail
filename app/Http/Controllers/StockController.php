<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $category = trim($request->string('category')->toString());
        $stockState = trim($request->string('stock_state')->toString());

        $categories = Product::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $products = Product::query()
            ->when($category !== '', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('subcategory', 'like', "%{$search}%");
                });
            })
            ->when($stockState !== '', function ($query) use ($stockState) {
                match ($stockState) {
                    'out' => $query->where('stock', 0),
                    'low' => $query->whereBetween('stock', [1, 5]),
                    'healthy' => $query->where('stock', '>', 5),
                    default => null,
                };
            })
            ->orderBy('stock')
            ->orderBy('next_delivery_due_at')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock.index', [
            'products' => $products,
            'search' => $search,
            'category' => $category,
            'categories' => $categories,
            'stockState' => $stockState,
            'recentMovements' => StockMovement::query()
                ->with('product', 'user')
                ->latest('occurred_at')
                ->limit(12)
                ->get(),
            'outOfStockCount' => Product::query()->where('stock', 0)->count(),
            'lowStockCount' => Product::query()->whereBetween('stock', [1, 5])->count(),
            'incomingDeliveryCount' => Product::query()
                ->whereNotNull('next_delivery_due_at')
                ->whereBetween('next_delivery_due_at', [now(), now()->addDays(7)])
                ->count(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
            'next_delivery_due_at' => ['nullable', 'date'],
            'last_restocked_at' => ['nullable', 'date'],
            'stock_note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $product, $validated): void {
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            $previousStock = (int)$lockedProduct->stock;
            $newStock = (int)$validated['stock'];
            $quantityChange = $newStock - $previousStock;
            $newDeliveryDate = !empty($validated['next_delivery_due_at'])
                ? Carbon::parse($validated['next_delivery_due_at'])
                : null;
            $currentDeliveryDate = $lockedProduct->next_delivery_due_at?->format('Y-m-d H:i:s');
            $scheduleChanged = $newDeliveryDate?->format('Y-m-d H:i:s') !== $currentDeliveryDate;

            $lastRestockedAt = $lockedProduct->last_restocked_at;

            if ($quantityChange > 0) {
                $lastRestockedAt = $validated['last_restocked_at']
                    ? Carbon::parse($validated['last_restocked_at'])
                    : now();
            }

            $lockedProduct->update([
                'stock' => $newStock,
                'last_restocked_at' => $lastRestockedAt,
                'next_delivery_due_at' => $newDeliveryDate,
            ]);

            if ($quantityChange !== 0 || $scheduleChanged) {
                StockMovement::query()->create([
                    'product_id' => $lockedProduct->id,
                    'user_id' => $request->user()?->id,
                    'type' => $quantityChange > 0
                        ? 'restock'
                        : ($quantityChange < 0 ? 'manual_decrease' : 'schedule_update'),
                    'quantity_change' => $quantityChange,
                    'note' => $validated['stock_note'] ?: null,
                    'occurred_at' => $quantityChange > 0 && $validated['last_restocked_at']
                        ? Carbon::parse($validated['last_restocked_at'])
                        : now(),
                ]);
            }
        });

        return redirect()
            ->route('admin.stock.index')
            ->with('status', 'Stock information updated successfully.');
    }
}
