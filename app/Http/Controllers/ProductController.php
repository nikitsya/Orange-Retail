<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $category = trim($request->string('category')->toString());
        $subcategory = trim($request->string('subcategory')->toString());
        $openProductId = $request->integer('edit');

        $categories = Product::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $subcategoryOptionsByCategory = Product::query()
            ->select('category', 'subcategory')
            ->whereNotNull('category')
            ->whereNotNull('subcategory')
            ->where('subcategory', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->orderBy('subcategory')
            ->get()
            ->groupBy('category')
            ->map(fn ($rows) => $rows->pluck('subcategory')->values()->all())
            ->all();

        foreach (Product::categories() as $categoryOption) {
            $subcategoryOptionsByCategory[$categoryOption] ??= [];
        }

        $subcategoryOptions = collect($subcategoryOptionsByCategory[$category] ?? []);

        $products = Product::query()
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
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->orderBy('category')
            ->orderBy('subcategory')
            ->orderByDesc('is_active')
            ->orderBy('stock')
            ->orderBy('brand')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'search' => $search,
            'category' => $category,
            'subcategory' => $subcategory,
            'openProductId' => $openProductId > 0 ? $openProductId : null,
            'categories' => $categories,
            'categoryOptions' => Product::categories(),
            'subcategoryOptionsByCategory' => $subcategoryOptionsByCategory,
            'subcategoryOptions' => $subcategoryOptions,
            'unitTypes' => Product::unitTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        Product::query()->create($validated);

        return redirect()
            ->route('products.index', $this->indexParameters($request))
            ->with('status', 'Product added successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('products', 'barcode')->ignore($product),
            ],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', Rule::in(Product::categories())],
            'subcategory' => ['nullable', 'string', 'max:100'],
            'new_subcategory' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'unit_type' => ['required', 'string', Rule::in(Product::unitTypes())],
            'pack_size' => ['nullable', 'string', 'max:100'],
            'price_value' => ['required', 'numeric', 'gt:0'],
            'unit_price_display' => ['nullable', 'string', 'max:40'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock_level' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        foreach (['barcode', 'image_url', 'pack_size', 'unit_price_display', 'subcategory', 'new_subcategory'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        $validated['subcategory'] = trim((string) ($validated['new_subcategory'] ?: $validated['subcategory']));

        if ($validated['subcategory'] === '') {
            throw ValidationException::withMessages([
                'subcategory' => 'Choose or add a subcategory.',
            ]);
        }

        unset($validated['new_subcategory']);
        $validated['minimum_stock_level'] = isset($validated['minimum_stock_level'])
            ? (int) $validated['minimum_stock_level']
            : ($product?->minimum_stock_level ?? 5);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    /**
     * @return array<string, string>
     */
    protected function indexParameters(Request $request): array
    {
        $search = trim($request->string('current_search')->toString());
        $category = trim($request->string('current_category')->toString());
        $subcategory = trim($request->string('current_subcategory')->toString());

        return array_filter([
            'search' => $search !== '' ? $search : null,
            'category' => $category !== '' ? $category : null,
            'subcategory' => $subcategory !== '' ? $subcategory : null,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        $product->update($validated);

        return redirect()
            ->route('products.index', $this->indexParameters($request))
            ->with('status', 'Product updated successfully.');
    }

    public function activate(Request $request, Product $product): RedirectResponse
    {
        if (! $product->is_active) {
            $product->update(['is_active' => true]);
        }

        $panel = trim($request->string('current_panel')->toString());

        return redirect()
            ->route('admin.dashboard', array_filter([
                'panel' => $panel !== '' ? $panel : 'inactive-products',
            ]))
            ->with('status', 'Product activated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index', $this->indexParameters($request))
            ->with('status', 'Product removed successfully.');
    }
}
