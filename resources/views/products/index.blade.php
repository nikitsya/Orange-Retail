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
        <div class="utility-bar">
            <div class="page-shell utility-bar-inner">
                <div class="utility-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('catalog.index') }}">Catalog</a>
                    <a href="{{ route('products.index') }}">Inventory</a>
                </div>

                <div class="utility-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="utility-button" type="submit">Sign out</button>
                    </form>
                </div>
            </div>
        </div>

        <header class="masthead">
            <div class="page-shell">
                <div class="masthead-main">
                    <a class="brand-lockup" href="{{ route('products.index') }}">
                        @include('partials.brand-name', ['class' => 'brand-title'])
                    </a>

                    <form class="search-shell" method="GET" action="{{ route('products.index') }}">
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
                        <a class="account-pill" href="{{ route('catalog.index') }}">
                            <div>
                                <strong>{{ $products->count() }} products</strong>
                                <span>Visible in the current inventory view</span>
                            </div>
                        </a>

                        <button class="button-primary" type="button" data-open-modal="add-product-modal">Add product</button>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-shell page-main admin-grid">
            <section class="admin-hero stack">
                <div>
                    <span class="section-kicker">Admin</span>
                    <h1>Inventory list</h1>
                    <p class="muted-copy">Update or remove product records stored with the current schema.</p>
                </div>

                <div class="hero-notes">
                    <div class="hero-note">
                        <strong>{{ $products->count() }}</strong>
                        <span>Products in the current result</span>
                    </div>
                    <div class="hero-note">
                        <strong>{{ $search !== '' ? 'Filtered' : 'All records' }}</strong>
                        <span>{{ $search !== '' ? 'Search is active for the inventory list.' : 'No search filter is active.' }}</span>
                    </div>
                    <div class="hero-note">
                        <strong>Products</strong>
                        <span>Create, edit, and delete catalog items.</span>
                    </div>
                </div>
            </section>

            @if (session('status'))
                <div class="flash-message">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-message">{{ $errors->first() }}</div>
            @endif

            <section class="admin-surface stack">
                <div class="admin-toolbar">
                    <form class="toolbar-search" method="GET" action="{{ route('products.index') }}">
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search by name or description"
                        >
                        <button class="search-image-button" type="submit" aria-label="Search">
                            <img src="{{ asset('images/ui/search.png') }}" alt="">
                            <span class="sr-only">Search</span>
                        </button>
                    </form>

                    <div class="toolbar">
                        @if ($search !== '')
                            <a class="button-secondary" href="{{ route('products.index') }}">Clear</a>
                        @endif

                        <button class="button-primary" type="button" data-open-modal="add-product-modal">Add product</button>
                    </div>
                </div>

                @if ($search !== '')
                    <p class="muted-copy">Showing results for "{{ $search }}".</p>
                @endif

                @if ($products->isEmpty())
                    <section class="empty-panel">
                        <h2 class="section-heading">No products found</h2>
                        <p class="muted-copy">
                            {{ $search !== '' ? 'No products matched your search.' : 'No products are available yet. Add the first one above.' }}
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
                                    <span>{{ $product->brand }} | {{ $product->subcategory }} | SKU: {{ $product->sku }}</span>
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
                                            <p class="muted-copy">Edit product fields or remove the record from the inventory list.</p>
                                        </div>

                                        <button class="modal-close" type="button" data-close-modal aria-label="Close modal">×</button>
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
                                            <div>{{ $product->brand }} | {{ $product->category }} / {{ $product->subcategory }}</div>
                                        </div>

                                        <div class="meta-card">
                                            <strong>Packaging</strong>
                                            <div>
                                                {{ $product->unit_type }} |
                                                {{ $product->pack_size ?: 'No pack size' }} |
                                                {{ $product->weight_value !== null ? trim(number_format((float) $product->weight_value, 2) . ' ' . ($product->weight_unit ?: '')) : 'No weight data' }}
                                            </div>
                                        </div>
                                    </div>

                                    @if ($product->image_url)
                                        <div class="tile-actions">
                                            <a class="button-secondary" href="{{ $product->image_url }}" target="_blank" rel="noreferrer">Open product image</a>
                                        </div>
                                    @endif

                                    <form class="stack" method="POST" action="{{ route('products.update', $product) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="modal_product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="search" value="{{ $search }}">

                                        <div class="form-grid-2">
                                            <label class="field-label">
                                                SKU
                                                <input class="field" type="text" name="sku" value="{{ old('modal_product_id') == $product->id ? old('sku', $product->sku) : $product->sku }}" required>
                                            </label>

                                            <label class="field-label">
                                                Barcode
                                                <input class="field" type="text" name="barcode" value="{{ old('modal_product_id') == $product->id ? old('barcode', $product->barcode) : $product->barcode }}">
                                            </label>
                                        </div>

                                        <div class="form-grid-2">
                                            <label class="field-label">
                                                Product name
                                                <input class="field" type="text" name="name" value="{{ old('modal_product_id') == $product->id ? old('name', $product->name) : $product->name }}" required>
                                            </label>

                                            <label class="field-label">
                                                Brand
                                                <input class="field" type="text" name="brand" value="{{ old('modal_product_id') == $product->id ? old('brand', $product->brand) : $product->brand }}" required>
                                            </label>
                                        </div>

                                        <div class="form-grid-2">
                                            <label class="field-label">
                                                Category
                                                <input class="field" type="text" name="category" value="{{ old('modal_product_id') == $product->id ? old('category', $product->category) : $product->category }}" required>
                                            </label>

                                            <label class="field-label">
                                                Subcategory
                                                <input class="field" type="text" name="subcategory" value="{{ old('modal_product_id') == $product->id ? old('subcategory', $product->subcategory) : $product->subcategory }}" required>
                                            </label>
                                        </div>

                                        <label class="field-label">
                                            Description
                                            <textarea class="field-area" name="description" required>{{ old('modal_product_id') == $product->id ? old('description', $product->description) : $product->description }}</textarea>
                                        </label>

                                        <label class="field-label">
                                            Image URL
                                            <input class="field" type="url" name="image_url" value="{{ old('modal_product_id') == $product->id ? old('image_url', $product->image_url) : $product->image_url }}">
                                        </label>

                                        <div class="form-grid-3">
                                            <label class="field-label">
                                                Unit type
                                                <input class="field" type="text" name="unit_type" value="{{ old('modal_product_id') == $product->id ? old('unit_type', $product->unit_type) : $product->unit_type }}" required>
                                            </label>

                                            <label class="field-label">
                                                Pack size
                                                <input class="field" type="text" name="pack_size" value="{{ old('modal_product_id') == $product->id ? old('pack_size', $product->pack_size) : $product->pack_size }}">
                                            </label>

                                            <label class="field-label">
                                                Weight unit
                                                <input class="field" type="text" name="weight_unit" value="{{ old('modal_product_id') == $product->id ? old('weight_unit', $product->weight_unit) : $product->weight_unit }}">
                                            </label>
                                        </div>

                                        <label class="field-label">
                                            Weight value
                                            <input class="field" type="number" step="0.01" min="0" name="weight_value" value="{{ old('modal_product_id') == $product->id ? old('weight_value', $product->weight_value !== null ? number_format((float) $product->weight_value, 2, '.', '') : '') : ($product->weight_value !== null ? number_format((float) $product->weight_value, 2, '.', '') : '') }}">
                                        </label>

                                        <div class="tile-actions">
                                            <button class="button-primary" type="submit">Update product</button>
                                        </div>
                                    </form>

                                    <div class="tile-actions" style="margin-top: 16px;">
                                        <form method="POST" action="{{ route('products.destroy', $product) }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="search" value="{{ $search }}">
                                            <button class="button-danger" type="submit">Delete product</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif
            </section>
        </main>

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
                    <input type="hidden" name="search" value="{{ $search }}">

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
                            <input class="field" id="category" type="text" name="category" value="{{ old('category') }}" required>
                            @error('category')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="field-label" for="subcategory">
                            Subcategory
                            <input class="field" id="subcategory" type="text" name="subcategory" value="{{ old('subcategory') }}" required>
                            @error('subcategory')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <label class="field-label" for="description">
                        Description
                        <textarea class="field-area" id="description" name="description" required>{{ old('description') }}</textarea>
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
                        <label class="field-label" for="unit_type">
                            Unit type
                            <input class="field" id="unit_type" type="text" name="unit_type" value="{{ old('unit_type') }}" required>
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
                            <input class="field" id="weight_unit" type="text" name="weight_unit" value="{{ old('weight_unit') }}">
                            @error('weight_unit')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <label class="field-label" for="weight_value">
                        Weight value
                        <input class="field" id="weight_value" type="number" step="0.01" min="0" name="weight_value" value="{{ old('weight_value') }}">
                        @error('weight_value')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
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

                    if (! modal) {
                        return;
                    }

                    modal.classList.add('is-open');
                    toggleBodyScroll();
                });
            });

            document.querySelectorAll('[data-close-modal]').forEach((button) => {
                button.addEventListener('click', () => {
                    const modal = button.closest('[data-modal]');

                    if (! modal) {
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
