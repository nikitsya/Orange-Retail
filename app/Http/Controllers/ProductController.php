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

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('brand')
            ->orderBy('name')
            ->get();

        return view('products.index', [
            'products' => $products,
            'search' => $search,
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
        ]);

        foreach (['barcode', 'image_url', 'pack_size', 'weight_value', 'weight_unit'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        return $validated;
    }

    /**
     * @return array<string, string>
     */
    protected function indexParameters(Request $request): array
    {
        $search = trim($request->string('search')->toString());

        return $search === '' ? [] : ['search' => $search];
    }
}
