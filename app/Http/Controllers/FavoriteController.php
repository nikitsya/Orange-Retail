<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureCustomer($request);

        $products = $request->user()
            ->favoriteProducts()
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('subcategory')
            ->orderBy('name')
            ->paginate(20);

        $cartQuantities = collect($request->session()->get('cart', []))
            ->mapWithKeys(fn (array $item, string $productId): array => [
                (int) $productId => (int) ($item['quantity'] ?? 1),
            ]);

        return view('favorites.index', [
            'products' => $products,
            'cartQuantities' => $cartQuantities,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $this->ensureCustomer($request);
        $this->ensureVisible($product);

        $request->user()->favoriteProducts()->syncWithoutDetaching([$product->id]);

        return redirect()->back()->with('status', 'Added to favourites');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->ensureCustomer($request);

        $request->user()->favoriteProducts()->detach($product->id);

        return redirect()->back()->with('status', 'Removed from favourites');
    }

    protected function ensureCustomer(Request $request): void
    {
        if ($request->user()?->role === 'admin') {
            abort(403);
        }
    }

    protected function ensureVisible(Product $product): void
    {
        if (! $product->is_active) {
            abort(404);
        }
    }
}
