<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * @var list<string>
     */
    protected const CATEGORY_DISPLAY_ORDER = [
        'Fresh Food',
        'Drinks',
        'Food Cupboard',
        'Treats & Snacks',
        'Household',
        'Pets',
        'Health & Beauty',
        'Baby & Toddler',
        'Home & Furniture',
    ];

    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $category = trim($request->string('category')->toString());
        $subcategory = trim($request->string('subcategory')->toString());

        $categories = Product::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $subcategoryOptions = Product::query()
            ->where('is_active', true)
            ->when($category !== '', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->select('subcategory')
            ->whereNotNull('subcategory')
            ->where('subcategory', '!=', '')
            ->distinct()
            ->orderBy('subcategory')
            ->pluck('subcategory');

        $products = Product::query()
            ->where('is_active', true)
            ->when($category !== '', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($subcategory !== '', function ($query) use ($subcategory) {
                $query->where('subcategory', $subcategory);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->orderByRaw(
                'case ' .
                collect(self::CATEGORY_DISPLAY_ORDER)
                    ->values()
                    ->map(fn (string $categoryName, int $index): string => "when category = ? then {$index}")
                    ->implode(' ') .
                ' else ' . count(self::CATEGORY_DISPLAY_ORDER) . ' end',
                self::CATEGORY_DISPLAY_ORDER,
            )
            ->orderBy('subcategory')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $cartQuantities = collect($request->session()->get('cart', []))
            ->mapWithKeys(fn (array $item, string $productId): array => [
                (int) $productId => (int) ($item['quantity'] ?? 1),
            ]);

        $favoriteProductIds = collect();

        if ($request->user()?->role === 'user') {
            $favoriteProductIds = $request->user()
                ->favoriteProducts()
                ->pluck('products.id');
        }

        return view('catalog.index', [
            'products' => $products,
            'search' => $search,
            'category' => $category,
            'subcategory' => $subcategory,
            'categories' => $categories,
            'subcategoryOptions' => $subcategoryOptions,
            'cartQuantities' => $cartQuantities,
            'favoriteProductIds' => $favoriteProductIds,
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        if (!$product->is_active && $request->user()?->role !== 'admin') {
            abort(404);
        }

        return view('catalog.show', [
            'product' => $product,
            'isFavorite' => $request->user()?->role === 'user'
                ? $request->user()->favoriteProducts()->whereKey($product->id)->exists()
                : false,
        ]);
    }

    public function cart(Request $request): View
    {
        $this->ensureCustomer($request);

        $items = Cart::items($request->session()->get('cart', []));

        return view('cart.index', [
            'items' => $items,
            'itemCount' => Cart::itemCount($items),
            'subtotal' => Cart::subtotal($items),
        ]);
    }

    protected function ensureCustomer(Request $request): void
    {
        if ($request->user()?->role === 'admin') {
            abort(403);
        }
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $this->ensureCustomer($request);
        $this->ensurePurchasable($product);

        $cart = $request->session()->get('cart', []);
        $productKey = (string)$product->id;
        $currentQuantity = (int)($cart[$productKey]['quantity'] ?? 0);

        if ($currentQuantity >= $product->stock) {
            return redirect()
                ->back()
                ->withErrors(['cart' => "{$product->name} has reached the available stock limit."]);
        }

        $cart[$productKey] = [
            'quantity' => $currentQuantity + 1,
        ];

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('status', 'Added to cart');
    }

    protected function ensurePurchasable(Product $product): void
    {
        if (!$product->is_active) {
            abort(404);
        }

        if ($product->stock < 1) {
            throw ValidationException::withMessages([
                'cart' => "{$product->name} is currently out of stock.",
            ]);
        }
    }

    public function updateCart(Request $request, Product $product): RedirectResponse
    {
        $this->ensureCustomer($request);
        $this->ensurePurchasable($product);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = (int)$validated['quantity'];

        if ($quantity > $product->stock) {
            return redirect()
                ->route('cart.index')
                ->withErrors(['cart' => "{$product->name} only has {$product->stock} item(s) available."]);
        }

        $cart = $request->session()->get('cart', []);
        $cart[(string)$product->id] = [
            'quantity' => $quantity,
        ];

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('status', 'Cart quantity updated successfully.');
    }

    public function removeFromCart(Request $request, Product $product): RedirectResponse
    {
        $this->ensureCustomer($request);

        $cart = $request->session()->get('cart', []);

        unset($cart[(string)$product->id]);

        $request->session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('status', 'The item was removed from your cart.');
    }

}
