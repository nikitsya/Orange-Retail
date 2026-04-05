<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $category = trim($request->string('category')->toString());

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
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('subcategory', 'like', "%{$search}%");
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
            'categories' => $categories,
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

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        $product->update($validated);

        return redirect()
            ->route('products.index', $this->indexParameters($request))
            ->with('status', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index', $this->indexParameters($request))
            ->with('status', 'Product removed successfully.');
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
            'category' => ['required', 'string', 'max:100'],
            'subcategory' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'unit_type' => ['required', 'string', 'max:50'],
            'pack_size' => ['nullable', 'string', 'max:100'],
            'weight_value' => ['nullable', 'numeric', 'min:0'],
            'weight_unit' => ['nullable', 'string', 'max:20'],
            'price_value' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'string', 'max:10'],
            'price_display' => ['nullable', 'string', 'max:30'],
            'unit_price_display' => ['nullable', 'string', 'max:40'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        foreach (['barcode', 'image_url', 'pack_size', 'weight_value', 'weight_unit', 'price_display', 'unit_price_display'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        $validated['currency'] = strtoupper($validated['currency']);
        $validated['is_active'] = $request->boolean('is_active');

        if (! $validated['price_display']) {
            $validated['price_display'] = $validated['currency'] === 'EUR'
                ? '€' . number_format((float) $validated['price_value'], 2)
                : $validated['currency'] . ' ' . number_format((float) $validated['price_value'], 2);
        }

        return $validated;
    }

    /**
     * @return array<string, string>
     */
    protected function indexParameters(Request $request): array
    {
        $search = trim($request->string('current_search')->toString());
        $category = trim($request->string('current_category')->toString());

        return array_filter([
            'search' => $search !== '' ? $search : null,
            'category' => $category !== '' ? $category : null,
        ]);
    }
}
