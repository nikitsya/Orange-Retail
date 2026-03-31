<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('brand')
            ->orderBy('name')
            ->get();

        return view('catalog.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function show(Product $product): View
    {
        return view('catalog.show', [
            'product' => $product,
        ]);
    }

    public function cart(Request $request): View
    {
        $cart = collect($request->session()->get('cart', []));

        $items = $cart
            ->map(function (array $item, string $productId): ?array {
                $product = Product::query()->find($productId);

                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => (int) ($item['quantity'] ?? 1),
                ];
            })
            ->filter()
            ->values();

        return view('cart.index', [
            'items' => $items,
            'itemCount' => $this->cartItemCount($items),
        ]);
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        $productKey = (string) $product->id;

        $cart[$productKey] = [
            'quantity' => (int) (($cart[$productKey]['quantity'] ?? 0) + 1),
        ];

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('status', "{$product->name} was added to your cart.");
    }

    public function removeFromCart(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        unset($cart[(string) $product->id]);

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('status', "{$product->name} was removed from your cart.");
    }

    protected function cartItemCount(Collection $items): int
    {
        return $items->sum('quantity');
    }
}
