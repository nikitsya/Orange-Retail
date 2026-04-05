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
            <a class="brand-lockup" href="{{ route('catalog.index') }}">
                @include('partials.brand-name', ['class' => 'brand-title'])
            </a>

            <form class="search-shell" method="GET" action="{{ route('products.index') }}">
                @if ($category !== '')
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                @if ($subcategory !== '')
                    <input type="hidden" name="subcategory" value="{{ $subcategory }}">
                @endif

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name, brand, SKU, or barcode"
                    aria-label="Search inventory"
                >
                <button class="search-image-button" type="submit" aria-label="Search">
                    <img src="{{ asset('images/ui/search.png') }}" alt="">
                    <span class="sr-only">Search</span>
                </button>
            </form>

            <div class="masthead-actions">
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

        @if ($category !== '' && $subcategoryOptions->isNotEmpty())
            <div class="subcategory-filter-shell">
                <div class="subcategory-filter-row" role="navigation" aria-label="{{ $category }} subcategories">
                    <a
                        class="subcategory-chip @if ($subcategory === '') is-active @endif"
                        href="{{ route('products.index', array_filter(['category' => $category, 'search' => $search !== '' ? $search : null])) }}"
                    >
                        All {{ $category }}
                    </a>

                    @foreach ($subcategoryOptions as $subcategoryOption)
                        <a
                            class="subcategory-chip @if ($subcategory === $subcategoryOption) is-active @endif"
                            href="{{ route('products.index', array_filter(['category' => $category, 'subcategory' => $subcategoryOption, 'search' => $search !== '' ? $search : null])) }}"
                        >
                            {{ $subcategoryOption }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
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
                        {{ $search !== '' || $category !== '' || $subcategory !== '' ? 'No products matched your current filters.' : 'No products are available yet. Add the first one above.' }}
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
                                        <h2>{{ $product->name }}</h2>
                                        <p class="muted-copy">Edit product fields or remove the record from the
                                            inventory list.</p>
                                    </div>

                                    <button class="modal-close" type="button" data-close-modal aria-label="Close modal">
                                        ×
                                    </button>
                                </div>

                                @if ($product->image_url)
                                    <div class="tile-actions">
                                        <a class="button-secondary" href="{{ $product->image_url }}" target="_blank"
                                           rel="noreferrer">Open product image</a>
                                    </div>
                                @endif

                                <form
                                    id="update-product-form-{{ $product->id }}"
                                    class="stack"
                                    method="POST"
                                    action="{{ route('products.update', $product) }}"
                                >
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="modal_product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="current_search" value="{{ $search }}">
                                    <input type="hidden" name="current_category" value="{{ $category }}">
                                    <input type="hidden" name="current_subcategory" value="{{ $subcategory }}">

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
                                            <select class="field-select" name="category" data-category-select required>
                                                @foreach ($categoryOptions as $categoryOption)
                                                    <option
                                                        value="{{ $categoryOption }}"
                                                        @selected((old('modal_product_id') == $product->id ? old('category', $product->category) : $product->category) === $categoryOption)
                                                    >
                                                        {{ $categoryOption }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>

                                        <label class="field-label">
                                            Subcategory
                                            <div class="picker-inline">
                                                <select
                                                    class="field-select"
                                                    name="subcategory"
                                                    data-subcategory-select
                                                    data-selected-subcategory="{{ old('modal_product_id') == $product->id ? old('subcategory', $product->subcategory) : $product->subcategory }}"
                                                    data-new-subcategory-value="{{ old('modal_product_id') == $product->id ? old('new_subcategory') : '' }}"
                                                ></select>
                                                <button class="button-quiet picker-inline-button" type="button" data-toggle-subcategory-input>
                                                    + New
                                                </button>
                                            </div>
                                            <input
                                                class="field subcategory-add-input"
                                                type="text"
                                                name="new_subcategory"
                                                value="{{ old('modal_product_id') == $product->id ? old('new_subcategory') : '' }}"
                                                placeholder="Add a new subcategory"
                                                data-new-subcategory
                                                hidden
                                            >
                                            @if (old('modal_product_id') == $product->id)
                                                @error('subcategory')
                                                <span class="field-error">{{ $message }}</span>
                                                @enderror
                                                @error('new_subcategory')
                                                <span class="field-error">{{ $message }}</span>
                                                @enderror
                                            @endif
                                        </label>
                                    </div>

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
                                            Unit price display
                                            <input class="field" type="text" name="unit_price_display"
                                                   value="{{ old('modal_product_id') == $product->id ? old('unit_price_display', $product->unit_price_display) : $product->unit_price_display }}">
                                        </label>
                                    </div>

                                    <div class="form-grid-2">
                                        <label class="field-label">
                                            Unit type
                                            <select class="field-select" name="unit_type" required>
                                                @foreach ($unitTypes as $unitType)
                                                    <option
                                                        value="{{ $unitType }}"
                                                        @selected((old('modal_product_id') == $product->id ? old('unit_type', $product->unit_type) : $product->unit_type) === $unitType)
                                                    >
                                                        {{ ucfirst($unitType) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>

                                        <label class="field-label">
                                            Pack size
                                            <input class="field" type="text" name="pack_size"
                                                   value="{{ old('modal_product_id') == $product->id ? old('pack_size', $product->pack_size) : $product->pack_size }}">
                                        </label>
                                    </div>

                                    <label class="field-label">
                                        Stock
                                        <input class="field" type="number" min="0" name="stock"
                                               value="{{ old('modal_product_id') == $product->id ? old('stock', $product->stock) : $product->stock }}"
                                               readonly
                                               aria-readonly="true"
                                               required>
                                        <span class="field-help">
                                            Stock is managed in <a href="{{ route('admin.stock.index') }}">Stock Center</a>.
                                        </span>
                                    </label>

                                    <label class="remember-row">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active"
                                               value="1" @checked((old('modal_product_id') == $product->id ? old('is_active', $product->is_active ? '1' : '0') : ($product->is_active ? '1' : '0')) == '1')>
                                        Visible in the customer catalog
                                    </label>

                                </form>

                                <div class="modal-form-actions">
                                    <button class="button-primary" type="submit" form="update-product-form-{{ $product->id }}">
                                        Update product
                                    </button>

                                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="current_search" value="{{ $search }}">
                                        <input type="hidden" name="current_category" value="{{ $category }}">
                                        <input type="hidden" name="current_subcategory" value="{{ $subcategory }}">
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
            <input type="hidden" name="current_subcategory" value="{{ $subcategory }}">

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
                    <select class="field-select" id="category" name="category" data-category-select required>
                        @foreach ($categoryOptions as $categoryOption)
                            <option value="{{ $categoryOption }}" @selected(old('category') === $categoryOption)>
                                {{ $categoryOption }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="field-label" for="subcategory">
                    Subcategory
                    <div class="picker-inline">
                        <select
                            class="field-select"
                            id="subcategory"
                            name="subcategory"
                            data-subcategory-select
                            data-selected-subcategory="{{ old('subcategory') }}"
                            data-new-subcategory-value="{{ old('new_subcategory') }}"
                        ></select>
                        <button class="button-quiet picker-inline-button" type="button" data-toggle-subcategory-input>
                            + New
                        </button>
                    </div>
                    <input
                        class="field subcategory-add-input"
                        type="text"
                        name="new_subcategory"
                        value="{{ old('new_subcategory') }}"
                        placeholder="Add a new subcategory"
                        data-new-subcategory
                        hidden
                    >
                    @error('subcategory')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                    @error('new_subcategory')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="field-label" for="image_url">
                Image URL
                <input class="field" id="image_url" type="url" name="image_url" value="{{ old('image_url') }}">
                @error('image_url')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </label>

            <div class="form-grid-2">
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
                    <select class="field-select" id="unit_type" name="unit_type" required>
                        @foreach ($unitTypes as $unitType)
                            <option value="{{ $unitType }}" @selected(old('unit_type') === $unitType)>
                                {{ ucfirst($unitType) }}
                            </option>
                        @endforeach
                    </select>
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
            </div>

            <label class="field-label" for="stock">
                Stock
                <input class="field" id="stock" type="number" min="0" name="stock" value="{{ old('stock', 0) }}"
                       required>
                @error('stock')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </label>

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
    const subcategoryOptionsByCategory = @json($subcategoryOptionsByCategory);
    let lockedScrollY = 0;

    const populateSubcategoryOptions = (categorySelect, subcategorySelect, preferredValue = '') => {
        const category = categorySelect.value;
        const options = [...(subcategoryOptionsByCategory[category] ?? [])];
        const nextValue = preferredValue || subcategorySelect.dataset.selectedSubcategory || '';

        if (nextValue !== '' && !options.includes(nextValue)) {
            options.push(nextValue);
        }

        subcategorySelect.innerHTML = '';

        options.forEach((optionValue) => {
            const option = document.createElement('option');
            option.value = optionValue;
            option.textContent = optionValue;
            subcategorySelect.appendChild(option);
        });

        if (options.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No saved subcategories';
            subcategorySelect.appendChild(option);
        }

        subcategorySelect.value = nextValue !== '' ? nextValue : (options[0] ?? '');
        subcategorySelect.dataset.selectedSubcategory = subcategorySelect.value;
    };

    const syncSubcategoryControls = (scope) => {
        const categorySelect = scope.querySelector('[data-category-select]');
        const subcategorySelect = scope.querySelector('[data-subcategory-select]');
        const newSubcategoryInput = scope.querySelector('[data-new-subcategory]');
        const toggleButton = scope.querySelector('[data-toggle-subcategory-input]');

        if (!categorySelect || !subcategorySelect || !newSubcategoryInput || !toggleButton) {
            return;
        }

        const updateToggleLabel = () => {
            toggleButton.textContent = newSubcategoryInput.hidden ? '+ New' : 'Cancel';
        };

        populateSubcategoryOptions(
            categorySelect,
            subcategorySelect,
            subcategorySelect.dataset.selectedSubcategory || subcategorySelect.dataset.newSubcategoryValue || '',
        );

        if ((newSubcategoryInput.value || '').trim() !== '') {
            newSubcategoryInput.hidden = false;
        }

        updateToggleLabel();

        categorySelect.addEventListener('change', () => {
            populateSubcategoryOptions(categorySelect, subcategorySelect);
        });

        subcategorySelect.addEventListener('change', () => {
            subcategorySelect.dataset.selectedSubcategory = subcategorySelect.value;
        });

        toggleButton.addEventListener('click', () => {
            const willShow = newSubcategoryInput.hidden;
            newSubcategoryInput.hidden = !willShow;

            if (willShow) {
                newSubcategoryInput.focus();
            } else {
                newSubcategoryInput.value = '';
            }

            updateToggleLabel();
        });
    };

    document.querySelectorAll('form').forEach((form) => {
        syncSubcategoryControls(form);
    });

    const lockBodyScroll = () => {
        if (document.body.classList.contains('modal-open')) {
            return;
        }

        lockedScrollY = window.scrollY;
        document.documentElement.classList.add('modal-open');
        document.body.classList.add('modal-open');
        document.body.style.position = 'fixed';
        document.body.style.top = `-${lockedScrollY}px`;
        document.body.style.left = '0';
        document.body.style.right = '0';
        document.body.style.width = '100%';
    };

    const unlockBodyScroll = () => {
        if (!document.body.classList.contains('modal-open')) {
            return;
        }

        document.documentElement.classList.remove('modal-open');
        document.body.classList.remove('modal-open');
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.left = '';
        document.body.style.right = '';
        document.body.style.width = '';
        window.scrollTo(0, lockedScrollY);
    };

    const toggleBodyScroll = () => {
        const hasOpenModal = document.querySelector('.modal.is-open') !== null;
        if (hasOpenModal) {
            lockBodyScroll();
            return;
        }

        unlockBodyScroll();
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
