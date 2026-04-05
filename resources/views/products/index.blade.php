<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Inventory</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body>
@php
    $departmentImages = [
        '' => 'all_dep.png',
        'All departments' => 'all_dep.png',
        'Baby & Toddler' => 'baby_toddler.png',
        'Drinks' => 'drinks.png',
        'Food Cupboard' => 'food_cupb.png',
        'Fresh Food' => 'fresh_food.png',
        'Health & Beauty' => 'health_beauty.png',
        'Home & Furniture' => 'home_furniture.png',
        'Household' => 'household.png',
        'Pets' => 'pets.png',
        'Treats & Snacks' => 'treats_snacks.png',
    ];
    $preferredCategories = collect([
        'Fresh Food',
        'Drinks',
        'Food Cupboard',
        'Treats & Snacks',
        'Household',
        'Pets',
        'Health & Beauty',
        'Baby & Toddler',
        'Home & Furniture',
    ]);
    $navCategories = $preferredCategories
        ->filter(fn (string $inventoryCategory) => $categories->contains($inventoryCategory))
        ->merge($categories->reject(fn (string $inventoryCategory) => $preferredCategories->contains($inventoryCategory)));
    $productCount = $products->total();
    $currentPage = $products->currentPage();
    $lastPage = $products->lastPage();
    $windowSize = 5;
    $halfWindow = intdiv($windowSize, 2);
    $pageStart = max(1, $currentPage - $halfWindow);
    $pageEnd = min($lastPage, $pageStart + $windowSize - 1);
    $pageStart = max(1, $pageEnd - $windowSize + 1);
@endphp

<div class="utility-bar">
    <div class="page-shell utility-bar-inner">
        @include('partials.app-nav')

        @include('partials.utility-actions')
    </div>
</div>

<header class="masthead">
    <div class="page-shell">
        <div class="masthead-main">
            <a class="brand-lockup" href="{{ route('products.index') }}">
                @include('partials.brand-name', ['class' => 'brand-title'])
            </a>

            <form class="search-shell" method="GET" action="{{ route('products.index') }}">
                @if ($category !== '')
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name or description"
                    aria-label="Search inventory"
                >
                <button class="search-image-button" type="submit" aria-label="Search">
                    <img src="{{ asset('images/ui/search.png') }}" alt="">
                    <span class="sr-only">Search</span>
                </button>
            </form>

            <div class="masthead-actions">
                <a class="button-secondary" href="{{ route('admin.stock.index') }}">Stock Center</a>
                <a class="account-pill" href="{{ route('catalog.index') }}">
                    <div>
                        <strong>{{ $productCount }} products</strong>
                        <span>Visible in the current inventory view</span>
                    </div>
                </a>

                <button class="button-primary" type="button" data-open-modal="add-product-modal">Add product</button>
            </div>
        </div>
    </div>
</header>

<div class="catalog-nav-shell">
    <div class="page-shell">
        <div class="catalog-nav-scroll">
            <nav class="section-nav" aria-label="Inventory departments">
                <a
                    class="nav-chip @if ($category === '') is-active @endif"
                    href="{{ route('products.index', ['search' => $search]) }}"
                    aria-label="All departments"
                >
                            <span class="nav-chip-media">
                                <img src="{{ asset('images/departments/' . $departmentImages['']) }}"
                                     alt="All departments">
                            </span>
                    <span class="sr-only">All departments</span>
                </a>

                @foreach ($navCategories as $inventoryCategory)
                    <a
                        class="nav-chip @if ($category === $inventoryCategory) is-active @endif"
                        href="{{ route('products.index', array_filter(['category' => $inventoryCategory, 'search' => $search !== '' ? $search : null])) }}"
                        aria-label="{{ $inventoryCategory }}"
                    >
                                <span class="nav-chip-media">
                                    @if (isset($departmentImages[$inventoryCategory]))
                                        <img
                                            src="{{ asset('images/departments/' . $departmentImages[$inventoryCategory]) }}"
                                            alt="{{ $inventoryCategory }}"
                                        >
                                    @else
                                        <span
                                            class="nav-chip-fallback">{{ strtoupper(substr($inventoryCategory, 0, 1)) }}</span>
                                    @endif
                                </span>
                        <span class="sr-only">{{ $inventoryCategory }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</div>

<section class="catalog-content">
    <main class="page-shell page-main admin-grid">
        <h1 class="sr-only">Inventory list</h1>

        @if (session('status'))
            <div class="flash-message">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <section class="admin-surface stack">
            @if ($products->isEmpty())
                <section class="empty-panel">
                    <h2 class="section-heading">No products found</h2>
                    <p class="muted-copy">
                        {{ $search !== '' || $category !== '' ? 'No products matched your current filters.' : 'No products are available yet. Add the first one above.' }}
                    </p>
                </section>
            @else
                <section class="inventory-list">
                    @foreach ($products as $product)
                        <article>
                            <button
                                class="inventory-row"
                                type="button"
                                data-open-modal="product-modal-{{ $product->id }}"
                            >
                                <span class="inventory-tag">{{ $product->category }}</span>
                                <strong>{{ $product->name }}</strong>
                                <span>{{ $product->brand }} | {{ $product->subcategory }} | SKU: {{ $product->sku }} | Stock: {{ $product->stock }} | {{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                            </button>
                        </article>

                        <div
                            class="modal @if ((string) old('modal_product_id') === (string) $product->id) is-open @endif"
                            id="product-modal-{{ $product->id }}"
                            data-modal
                        >
                            <div class="modal-dialog">
                                <div class="modal-head">
                                    <div>
                                        <span class="section-kicker">Product</span>
                                        <h2>{{ $product->name }}</h2>
                                        <p class="muted-copy">Edit product fields or remove the record from the
                                            inventory list.</p>
                                    </div>

                                    <button class="modal-close" type="button" data-close-modal aria-label="Close modal">
                                        ×
                                    </button>
                                </div>

                                <div class="meta-grid">
                                    <div class="meta-card">
                                        <strong>SKU</strong>
                                        <div>{{ $product->sku }}</div>
                                    </div>

                                    <div class="meta-card">
                                        <strong>Barcode</strong>
                                        <div>{{ $product->barcode ?: 'Not available' }}</div>
                                    </div>

                                    <div class="meta-card">
                                        <strong>Brand and category</strong>
                                        <div>{{ $product->brand }} | {{ $product->category }}
                                            / {{ $product->subcategory }}</div>
                                    </div>

                                    <div class="meta-card">
                                        <strong>Packaging</strong>
                                        <div>
                                            {{ $product->unit_type }} |
                                            {{ $product->pack_size ?: 'No pack size' }} |
                                            {{ $product->weight_value !== null ? trim(number_format((float) $product->weight_value, 2) . ' ' . ($product->weight_unit ?: '')) : 'No weight data' }}
                                        </div>
                                    </div>

                                    <div class="meta-card">
                                        <strong>Stock and status</strong>
                                        <div>{{ $product->stock }} units
                                            | {{ $product->is_active ? 'Active in catalog' : 'Hidden from catalog' }}</div>
                                    </div>

                                    <div class="meta-card">
                                        <strong>Price</strong>
                                        <div>{{ $product->price_display ?: 'No display price' }}
                                            | {{ $product->unit_price_display ?: 'No unit price' }}</div>
                                    </div>
                                </div>

                                @if ($product->image_url)
                                    <div class="tile-actions">
                                        <a class="button-secondary" href="{{ $product->image_url }}" target="_blank"
                                           rel="noreferrer">Open product image</a>
                                    </div>
                                @endif

                                <form class="stack" method="POST" action="{{ route('products.update', $product) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="modal_product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="current_search" value="{{ $search }}">
                                    <input type="hidden" name="current_category" value="{{ $category }}">

                                    <div class="form-grid-2">
                                        <label class="field-label">
                                            SKU
                                            <input class="field" type="text" name="sku"
                                                   value="{{ old('modal_product_id') == $product->id ? old('sku', $product->sku) : $product->sku }}"
                                                   required>
                                        </label>

                                        <label class="field-label">
                                            Barcode
                                            <input class="field" type="text" name="barcode"
                                                   value="{{ old('modal_product_id') == $product->id ? old('barcode', $product->barcode) : $product->barcode }}">
                                        </label>
                                    </div>

                                    <div class="form-grid-2">
                                        <label class="field-label">
                                            Product name
                                            <input class="field" type="text" name="name"
                                                   value="{{ old('modal_product_id') == $product->id ? old('name', $product->name) : $product->name }}"
                                                   required>
                                        </label>

                                        <label class="field-label">
                                            Brand
                                            <input class="field" type="text" name="brand"
                                                   value="{{ old('modal_product_id') == $product->id ? old('brand', $product->brand) : $product->brand }}"
                                                   required>
                                        </label>
                                    </div>

                                    <div class="form-grid-2">
                                        <label class="field-label">
                                            Category
                                            <input class="field" type="text" name="category"
                                                   value="{{ old('modal_product_id') == $product->id ? old('category', $product->category) : $product->category }}"
                                                   required>
                                        </label>

                                        <label class="field-label">
                                            Subcategory
                                            <input class="field" type="text" name="subcategory"
                                                   value="{{ old('modal_product_id') == $product->id ? old('subcategory', $product->subcategory) : $product->subcategory }}"
                                                   required>
                                        </label>
                                    </div>

                                    <label class="field-label">
                                        Description
                                        <textarea class="field-area" name="description"
                                                  required>{{ old('modal_product_id') == $product->id ? old('description', $product->description) : $product->description }}</textarea>
                                    </label>

                                    <label class="field-label">
                                        Image URL
                                        <input class="field" type="url" name="image_url"
                                               value="{{ old('modal_product_id') == $product->id ? old('image_url', $product->image_url) : $product->image_url }}">
                                    </label>

                                    <div class="form-grid-3">
                                        <label class="field-label">
                                            Price value
                                            <input class="field" type="number" step="0.01" min="0.01" name="price_value"
                                                   value="{{ old('modal_product_id') == $product->id ? old('price_value', $product->price_value !== null ? number_format((float) $product->price_value, 2, '.', '') : '') : ($product->price_value !== null ? number_format((float) $product->price_value, 2, '.', '') : '') }}"
                                                   required>
                                        </label>

                                        <label class="field-label">
                                            Currency
                                            <input class="field" type="text" name="currency"
                                                   value="{{ old('modal_product_id') == $product->id ? old('currency', $product->currency ?: 'EUR') : ($product->currency ?: 'EUR') }}"
                                                   required>
                                        </label>

                                        <label class="field-label">
                                            Unit price display
                                            <input class="field" type="text" name="unit_price_display"
                                                   value="{{ old('modal_product_id') == $product->id ? old('unit_price_display', $product->unit_price_display) : $product->unit_price_display }}">
                                        </label>
                                    </div>

                                    <div class="form-grid-3">
                                        <label class="field-label">
                                            Unit type
                                            <input class="field" type="text" name="unit_type"
                                                   value="{{ old('modal_product_id') == $product->id ? old('unit_type', $product->unit_type) : $product->unit_type }}"
                                                   required>
                                        </label>

                                        <label class="field-label">
                                            Pack size
                                            <input class="field" type="text" name="pack_size"
                                                   value="{{ old('modal_product_id') == $product->id ? old('pack_size', $product->pack_size) : $product->pack_size }}">
                                        </label>

                                        <label class="field-label">
                                            Weight unit
                                            <input class="field" type="text" name="weight_unit"
                                                   value="{{ old('modal_product_id') == $product->id ? old('weight_unit', $product->weight_unit) : $product->weight_unit }}">
                                        </label>
                                    </div>

                                    <label class="field-label">
                                        Weight value
                                        <input class="field" type="number" step="0.01" min="0" name="weight_value"
                                               value="{{ old('modal_product_id') == $product->id ? old('weight_value', $product->weight_value !== null ? number_format((float) $product->weight_value, 2, '.', '') : '') : ($product->weight_value !== null ? number_format((float) $product->weight_value, 2, '.', '') : '') }}">
                                    </label>

                                    <div class="form-grid-2">
                                        <label class="field-label">
                                            Stock
                                            <input class="field" type="number" min="0" name="stock"
                                                   value="{{ old('modal_product_id') == $product->id ? old('stock', $product->stock) : $product->stock }}"
                                                   required>
                                        </label>

                                        <label class="field-label">
                                            Price display
                                            <input class="field" type="text" name="price_display"
                                                   value="{{ old('modal_product_id') == $product->id ? old('price_display', $product->price_display) : $product->price_display }}">
                                        </label>
                                    </div>

                                    <label class="remember-row">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active"
                                               value="1" @checked((old('modal_product_id') == $product->id ? old('is_active', $product->is_active ? '1' : '0') : ($product->is_active ? '1' : '0')) == '1')>
                                        Visible in the customer catalog
                                    </label>

                                    <div class="tile-actions">
                                        <button class="button-primary" type="submit">Update product</button>
                                    </div>
                                </form>

                                <div class="tile-actions" style="margin-top: 16px;">
                                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="current_search" value="{{ $search }}">
                                        <input type="hidden" name="current_category" value="{{ $category }}">
                                        <button class="button-danger" type="submit">Delete product</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </section>

                @if ($products->hasPages())
                    <nav class="pagination-nav" aria-label="Inventory pages">
                        @if ($products->onFirstPage())
                            <span class="pagination-link is-disabled" aria-hidden="true">Previous</span>
                        @else
                            <a class="pagination-link" href="{{ $products->previousPageUrl() }}">Previous</a>
                        @endif

                        <div class="pagination-pages">
                            @for ($page = $pageStart; $page <= $pageEnd; $page++)
                                <a
                                    class="pagination-link @if ($page === $products->currentPage()) is-active @endif"
                                    href="{{ $products->url($page) }}"
                                    aria-label="Page {{ $page }}"
                                    @if ($page === $products->currentPage()) aria-current="page" @endif
                                >
                                    {{ $page }}
                                </a>
                            @endfor
                        </div>

                        @if ($products->hasMorePages())
                            <a class="pagination-link" href="{{ $products->nextPageUrl() }}">Next</a>
                        @else
                            <span class="pagination-link is-disabled" aria-hidden="true">Next</span>
                        @endif
                    </nav>
                @endif
            @endif
        </section>
    </main>
</section>

<div
    class="modal @if (old('modal_context') === 'create') is-open @endif"
    id="add-product-modal"
    data-modal
>
    <div class="modal-dialog">
        <div class="modal-head">
            <div>
                <span class="section-kicker">New product</span>
                <h2>Add product</h2>
                <p class="muted-copy">Create a product record with the current catalog metadata fields.</p>
            </div>

            <button class="modal-close" type="button" data-close-modal aria-label="Close modal">×</button>
        </div>

        <form class="stack" method="POST" action="{{ route('products.store') }}">
            @csrf
            <input type="hidden" name="modal_context" value="create">
            <input type="hidden" name="current_search" value="{{ $search }}">
            <input type="hidden" name="current_category" value="{{ $category }}">

            <div class="form-grid-2">
                <label class="field-label" for="sku">
                    SKU
                    <input class="field" id="sku" type="text" name="sku" value="{{ old('sku') }}" required>
                    @error('sku')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="barcode">
                    Barcode
                    <input class="field" id="barcode" type="text" name="barcode" value="{{ old('barcode') }}">
                    @error('barcode')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="form-grid-2">
                <label class="field-label" for="price_value">
                    Price value
                    <input class="field" id="price_value" type="number" step="0.01" min="0.01" name="price_value"
                           value="{{ old('price_value') }}" required>
                    @error('price_value')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="currency">
                    Currency
                    <input class="field" id="currency" type="text" name="currency" value="{{ old('currency', 'EUR') }}"
                           required>
                    @error('currency')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="form-grid-2">
                <label class="field-label" for="name">
                    Product name
                    <input class="field" id="name" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="brand">
                    Brand
                    <input class="field" id="brand" type="text" name="brand" value="{{ old('brand') }}" required>
                    @error('brand')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="form-grid-2">
                <label class="field-label" for="category">
                    Category
                    <input class="field" id="category" type="text" name="category" value="{{ old('category') }}"
                           required>
                    @error('category')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="subcategory">
                    Subcategory
                    <input class="field" id="subcategory" type="text" name="subcategory"
                           value="{{ old('subcategory') }}" required>
                    @error('subcategory')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="field-label" for="description">
                Description
                <textarea class="field-area" id="description" name="description"
                          required>{{ old('description') }}</textarea>
                @error('description')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="field-label" for="image_url">
                Image URL
                <input class="field" id="image_url" type="url" name="image_url" value="{{ old('image_url') }}">
                @error('image_url')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </label>

            <div class="form-grid-3">
                <label class="field-label" for="unit_price_display">
                    Unit price display
                    <input class="field" id="unit_price_display" type="text" name="unit_price_display"
                           value="{{ old('unit_price_display') }}">
                    @error('unit_price_display')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="unit_type">
                    Unit type
                    <input class="field" id="unit_type" type="text" name="unit_type" value="{{ old('unit_type') }}"
                           required>
                    @error('unit_type')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="pack_size">
                    Pack size
                    <input class="field" id="pack_size" type="text" name="pack_size" value="{{ old('pack_size') }}">
                    @error('pack_size')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="weight_unit">
                    Weight unit
                    <input class="field" id="weight_unit" type="text" name="weight_unit"
                           value="{{ old('weight_unit') }}">
                    @error('weight_unit')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="field-label" for="weight_value">
                Weight value
                <input class="field" id="weight_value" type="number" step="0.01" min="0" name="weight_value"
                       value="{{ old('weight_value') }}">
                @error('weight_value')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </label>

            <div class="form-grid-2">
                <label class="field-label" for="stock">
                    Stock
                    <input class="field" id="stock" type="number" min="0" name="stock" value="{{ old('stock', 0) }}"
                           required>
                    @error('stock')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="price_display">
                    Price display
                    <input class="field" id="price_display" type="text" name="price_display"
                           value="{{ old('price_display') }}">
                    @error('price_display')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="remember-row" for="is_active">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" type="checkbox" name="is_active"
                       value="1" @checked(old('is_active', '1') === '1')>
                Visible in the customer catalog
            </label>

            <div class="tile-actions">
                <button class="button-primary" type="submit">Save product</button>
                <button class="button-secondary" type="button" data-close-modal>Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    const toggleBodyScroll = () => {
        const hasOpenModal = document.querySelector('.modal.is-open') !== null;
        document.body.style.overflow = hasOpenModal ? 'hidden' : '';
    };

    document.querySelectorAll('[data-open-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.getElementById(button.dataset.openModal);

            if (!modal) {
                return;
            }

            modal.classList.add('is-open');
            toggleBodyScroll();
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = button.closest('[data-modal]');

            if (!modal) {
                return;
            }

            modal.classList.remove('is-open');
            toggleBodyScroll();
        });
    });

    document.querySelectorAll('[data-modal]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target !== modal) {
                return;
            }

            modal.classList.remove('is-open');
            toggleBodyScroll();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        document.querySelectorAll('.modal.is-open').forEach((modal) => {
            modal.classList.remove('is-open');
        });

        toggleBodyScroll();
    });

    toggleBodyScroll();
</script>
@include('partials.masthead-stick-on-scroll')
</body>
</html>
