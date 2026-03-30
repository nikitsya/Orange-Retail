<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Product Management</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #eff3ee;
                --surface: #ffffff;
                --surface-soft: #f6f8f5;
                --ink: #182119;
                --muted: #677162;
                --line: #d7dfd5;
                --brand: #2d6b45;
                --brand-strong: #1f5234;
                --danger: #b33b3b;
                --danger-soft: #f5e4e4;
                --success-soft: #e6f4ea;
                --success-ink: #1d6a38;
                --shadow: 0 18px 42px rgba(18, 31, 20, 0.08);
                --radius: 22px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: Arial, Helvetica, sans-serif;
                background: var(--bg);
                color: var(--ink);
            }

            .topbar {
                position: sticky;
                top: 0;
                z-index: 20;
                background: rgba(239, 243, 238, 0.92);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(24, 33, 25, 0.08);
            }

            .topbar-inner,
            .page {
                width: min(calc(100% - 24px), 1240px);
                margin: 0 auto;
            }

            .topbar-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                min-height: 68px;
            }

            .brand-block strong {
                display: block;
                font-size: 1rem;
            }

            .brand-block span {
                color: var(--muted);
                font-size: 0.86rem;
            }

            .logout-form {
                margin: 0;
            }

            .logout-button,
            .primary-button,
            .secondary-button,
            .danger-button {
                min-height: 42px;
                padding: 0.75rem 1rem;
                border-radius: 12px;
                border: 0;
                font: inherit;
                font-size: 0.86rem;
                font-weight: 700;
                cursor: pointer;
            }

            .logout-button,
            .primary-button {
                background: linear-gradient(135deg, var(--brand), var(--brand-strong));
                color: #fff;
            }

            .secondary-button {
                background: var(--surface-soft);
                border: 1px solid var(--line);
                color: var(--ink);
            }

            .danger-button {
                background: var(--danger-soft);
                color: var(--danger);
            }

            .page {
                display: grid;
                grid-template-columns: minmax(290px, 360px) minmax(0, 1fr);
                gap: 24px;
                padding: 28px 0 40px;
            }

            .panel {
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            .sidebar {
                position: sticky;
                top: 92px;
                padding: 24px;
                align-self: start;
            }

            .sidebar h1 {
                margin: 0;
                font-size: 2rem;
                line-height: 1;
                letter-spacing: -0.04em;
            }

            .sidebar p {
                margin: 14px 0 0;
                color: var(--muted);
                line-height: 1.65;
            }

            .stats {
                display: grid;
                gap: 12px;
                margin-top: 24px;
            }

            .stat-card {
                padding: 14px 16px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: var(--surface-soft);
            }

            .stat-card strong {
                display: block;
                font-size: 1.3rem;
            }

            .stat-card span {
                color: var(--muted);
                font-size: 0.84rem;
            }

            .content {
                display: grid;
                gap: 20px;
            }

            .flash,
            .error-box {
                padding: 14px 16px;
                border-radius: 16px;
                font-size: 0.92rem;
                line-height: 1.55;
            }

            .flash {
                background: var(--success-soft);
                color: var(--success-ink);
            }

            .error-box {
                background: var(--danger-soft);
                color: var(--danger);
            }

            .form-panel,
            .table-panel {
                padding: 22px;
            }

            .section-title {
                display: flex;
                align-items: end;
                justify-content: space-between;
                gap: 14px;
                margin-bottom: 18px;
            }

            .section-title h2 {
                margin: 0;
                font-size: 1.35rem;
            }

            .section-title p {
                margin: 6px 0 0;
                color: var(--muted);
                font-size: 0.9rem;
            }

            .product-form,
            .inline-form {
                display: grid;
                gap: 14px;
            }

            .field-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }

            .field-grid-wide {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
            }

            label {
                display: grid;
                gap: 8px;
                font-size: 0.85rem;
                font-weight: 700;
            }

            input,
            textarea {
                width: 100%;
                padding: 0.85rem 0.95rem;
                border: 1px solid var(--line);
                border-radius: 14px;
                font: inherit;
                color: var(--ink);
                background: #fff;
            }

            textarea {
                min-height: 96px;
                resize: vertical;
            }

            .field-error {
                color: var(--danger);
                font-size: 0.8rem;
                font-weight: 400;
            }

            .product-list {
                display: grid;
                gap: 12px;
            }

            .product-item {
                padding: 0;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: var(--surface);
            }

            .product-name-button {
                width: 100%;
                padding: 16px 18px;
                border: 0;
                border-radius: 16px;
                background: transparent;
                color: var(--ink);
                font: inherit;
                font-size: 0.98rem;
                font-weight: 700;
                text-align: left;
                cursor: pointer;
                transition: background 0.2s ease, transform 0.2s ease;
            }

            .product-name-button:hover {
                background: var(--surface-soft);
                transform: translateY(-1px);
            }

            .modal {
                position: fixed;
                inset: 0;
                z-index: 40;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 24px;
                background: rgba(24, 33, 25, 0.48);
            }

            .modal.is-open {
                display: flex;
            }

            .modal-dialog {
                width: min(100%, 860px);
                max-height: calc(100vh - 48px);
                overflow: auto;
                padding: 22px;
                border: 1px solid var(--line);
                border-radius: 22px;
                background: var(--surface);
                box-shadow: 0 24px 60px rgba(18, 31, 20, 0.24);
            }

            .modal-header {
                display: flex;
                align-items: start;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 18px;
            }

            .modal-header h3 {
                margin: 0;
                font-size: 1.35rem;
                line-height: 1.2;
            }

            .modal-header p {
                margin: 8px 0 0;
                color: var(--muted);
                font-size: 0.9rem;
            }

            .close-button {
                min-width: 42px;
                min-height: 42px;
                border: 1px solid var(--line);
                border-radius: 12px;
                background: var(--surface-soft);
                color: var(--ink);
                font: inherit;
                font-size: 1.1rem;
                font-weight: 700;
                cursor: pointer;
            }

            .modal-meta {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
                margin-bottom: 18px;
            }

            .meta-card {
                padding: 14px 16px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: var(--surface-soft);
            }

            .meta-card strong {
                display: block;
                margin-bottom: 6px;
                font-size: 0.82rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: var(--muted);
            }

            .meta {
                font-size: 0.92rem;
                line-height: 1.55;
            }

            .image-link {
                display: inline-flex;
                margin-bottom: 18px;
                color: var(--brand);
                font-size: 0.84rem;
                font-weight: 700;
                text-decoration: none;
            }

            .item-actions {
                display: flex;
                gap: 10px;
                margin-top: 12px;
            }

            .delete-form {
                margin-top: 12px;
            }

            .empty-state {
                padding: 24px;
                border: 1px dashed var(--line);
                border-radius: 18px;
                color: var(--muted);
                text-align: center;
            }

            @media (max-width: 980px) {
                .page {
                    grid-template-columns: 1fr;
                }

                .sidebar {
                    position: static;
                }
            }

            @media (max-width: 780px) {
                .field-grid,
                .field-grid-wide {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 640px) {
                .topbar-inner {
                    flex-direction: column;
                    align-items: flex-start;
                    justify-content: center;
                    padding: 12px 0;
                }

                .modal-header,
                .modal-meta,
                .section-title,
                .item-actions {
                    flex-direction: column;
                    align-items: stretch;
                }
            }
        </style>
    </head>
    <body>
        <header class="topbar">
            <div class="topbar-inner">
                <div class="brand-block">
                    <strong>Supermarket Management</strong>
                    <span>Admin panel for product catalog metadata</span>
                </div>

                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">Log out</button>
                </form>
            </div>
        </header>

        <main class="page">
            <aside class="panel sidebar">
                <h1>Products</h1>
                <p>
                    Maintain the exact catalog fields stored for each product record, including
                    barcode, brand, hierarchy, packaging, and weight metadata.
                </p>

                <div class="stats">
                    <div class="stat-card">
                        <strong>{{ $products->count() }}</strong>
                        <span>Total products in the catalog</span>
                    </div>

                    <div class="stat-card">
                        <strong>{{ $products->pluck('brand')->filter()->unique()->count() }}</strong>
                        <span>Unique brands tracked</span>
                    </div>

                    <div class="stat-card">
                        <strong>{{ $products->pluck('category')->filter()->unique()->count() }}</strong>
                        <span>Unique categories tracked</span>
                    </div>
                </div>
            </aside>

            <section class="content">
                @if (session('status'))
                    <div class="flash">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="error-box">{{ $errors->first() }}</div>
                @endif

                <section class="panel form-panel">
                    <div class="section-title">
                        <div>
                            <h2>Add product</h2>
                            <p>Create a product record with the full catalog metadata schema.</p>
                        </div>
                    </div>

                    <form class="product-form" method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <div class="field-grid">
                            <label for="sku">
                                SKU
                                <input id="sku" type="text" name="sku" value="{{ old('sku') }}" required>
                                @error('sku')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label for="barcode">
                                Barcode
                                <input id="barcode" type="text" name="barcode" value="{{ old('barcode') }}">
                                @error('barcode')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <div class="field-grid">
                            <label for="name">
                                Product name
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label for="brand">
                                Brand
                                <input id="brand" type="text" name="brand" value="{{ old('brand') }}" required>
                                @error('brand')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <div class="field-grid">
                            <label for="category">
                                Category
                                <input id="category" type="text" name="category" value="{{ old('category') }}" required>
                                @error('category')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label for="subcategory">
                                Subcategory
                                <input id="subcategory" type="text" name="subcategory" value="{{ old('subcategory') }}" required>
                                @error('subcategory')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <label for="description">
                            Description
                            <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label for="image_url">
                            Image URL
                            <input id="image_url" type="url" name="image_url" value="{{ old('image_url') }}">
                            @error('image_url')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="field-grid-wide">
                            <label for="unit_type">
                                Unit type
                                <input id="unit_type" type="text" name="unit_type" value="{{ old('unit_type') }}" required>
                                @error('unit_type')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label for="pack_size">
                                Pack size
                                <input id="pack_size" type="text" name="pack_size" value="{{ old('pack_size') }}">
                                @error('pack_size')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label for="weight_unit">
                                Weight unit
                                <input id="weight_unit" type="text" name="weight_unit" value="{{ old('weight_unit') }}">
                                @error('weight_unit')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <label for="weight_value">
                            Weight value
                            <input id="weight_value" type="number" step="0.01" min="0" name="weight_value" value="{{ old('weight_value') }}">
                            @error('weight_value')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <button class="primary-button" type="submit">Save product</button>
                    </form>
                </section>

                <section class="panel table-panel">
                    <div class="section-title">
                        <div>
                            <h2>Inventory list</h2>
                            <p>Update or remove product records stored with the new schema.</p>
                        </div>
                    </div>

                    @if ($products->isEmpty())
                        <div class="empty-state">No products are available yet. Add the first one above.</div>
                    @else
                        <div class="product-list">
                            @foreach ($products as $product)
                                <article class="product-item">
                                    <button
                                        class="product-name-button"
                                        type="button"
                                        data-open-modal="product-modal-{{ $product->id }}"
                                    >
                                        {{ $product->name }}
                                    </button>
                                </article>

                                <div
                                    class="modal @if ((string) old('modal_product_id') === (string) $product->id) is-open @endif"
                                    id="product-modal-{{ $product->id }}"
                                    data-modal
                                >
                                    <div class="modal-dialog">
                                        <div class="modal-header">
                                            <div>
                                                <h3>{{ $product->name }}</h3>
                                                <p>Full product metadata, editing, and deletion are available here.</p>
                                            </div>

                                            <button class="close-button" type="button" data-close-modal aria-label="Close modal">
                                                ×
                                            </button>
                                        </div>

                                        <div class="modal-meta">
                                            <div class="meta-card">
                                                <strong>SKU</strong>
                                                <div class="meta">{{ $product->sku }}</div>
                                            </div>

                                            <div class="meta-card">
                                                <strong>Barcode</strong>
                                                <div class="meta">{{ $product->barcode ?: 'Not available' }}</div>
                                            </div>

                                            <div class="meta-card">
                                                <strong>Brand and category</strong>
                                                <div class="meta">{{ $product->brand }} | {{ $product->category }} / {{ $product->subcategory }}</div>
                                            </div>

                                            <div class="meta-card">
                                                <strong>Packaging</strong>
                                                <div class="meta">
                                                    {{ $product->unit_type }} |
                                                    {{ $product->pack_size ?: 'No pack size' }} |
                                                    {{ $product->weight_value !== null ? trim(number_format((float) $product->weight_value, 2) . ' ' . ($product->weight_unit ?: '')) : 'No weight data' }}
                                                </div>
                                            </div>
                                        </div>

                                        @if ($product->image_url)
                                            <a class="image-link" href="{{ $product->image_url }}" target="_blank" rel="noreferrer">Open product image</a>
                                        @endif

                                        <form class="inline-form" method="POST" action="{{ route('products.update', $product) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="modal_product_id" value="{{ $product->id }}">

                                            <div class="field-grid">
                                                <label>
                                                    SKU
                                                    <input type="text" name="sku" value="{{ old('modal_product_id') == $product->id ? old('sku', $product->sku) : $product->sku }}" required>
                                                </label>

                                                <label>
                                                    Barcode
                                                    <input type="text" name="barcode" value="{{ old('modal_product_id') == $product->id ? old('barcode', $product->barcode) : $product->barcode }}">
                                                </label>
                                            </div>

                                            <div class="field-grid">
                                                <label>
                                                    Product name
                                                    <input type="text" name="name" value="{{ old('modal_product_id') == $product->id ? old('name', $product->name) : $product->name }}" required>
                                                </label>

                                                <label>
                                                    Brand
                                                    <input type="text" name="brand" value="{{ old('modal_product_id') == $product->id ? old('brand', $product->brand) : $product->brand }}" required>
                                                </label>
                                            </div>

                                            <div class="field-grid">
                                                <label>
                                                    Category
                                                    <input type="text" name="category" value="{{ old('modal_product_id') == $product->id ? old('category', $product->category) : $product->category }}" required>
                                                </label>

                                                <label>
                                                    Subcategory
                                                    <input type="text" name="subcategory" value="{{ old('modal_product_id') == $product->id ? old('subcategory', $product->subcategory) : $product->subcategory }}" required>
                                                </label>
                                            </div>

                                            <label>
                                                Description
                                                <textarea name="description" required>{{ old('modal_product_id') == $product->id ? old('description', $product->description) : $product->description }}</textarea>
                                            </label>

                                            <label>
                                                Image URL
                                                <input type="url" name="image_url" value="{{ old('modal_product_id') == $product->id ? old('image_url', $product->image_url) : $product->image_url }}">
                                            </label>

                                            <div class="field-grid-wide">
                                                <label>
                                                    Unit type
                                                    <input type="text" name="unit_type" value="{{ old('modal_product_id') == $product->id ? old('unit_type', $product->unit_type) : $product->unit_type }}" required>
                                                </label>

                                                <label>
                                                    Pack size
                                                    <input type="text" name="pack_size" value="{{ old('modal_product_id') == $product->id ? old('pack_size', $product->pack_size) : $product->pack_size }}">
                                                </label>

                                                <label>
                                                    Weight unit
                                                    <input type="text" name="weight_unit" value="{{ old('modal_product_id') == $product->id ? old('weight_unit', $product->weight_unit) : $product->weight_unit }}">
                                                </label>
                                            </div>

                                            <label>
                                                Weight value
                                                <input type="number" step="0.01" min="0" name="weight_value" value="{{ old('modal_product_id') == $product->id ? old('weight_value', $product->weight_value !== null ? number_format((float) $product->weight_value, 2, '.', '') : '') : ($product->weight_value !== null ? number_format((float) $product->weight_value, 2, '.', '') : '') }}">
                                            </label>

                                            <div class="item-actions">
                                                <button class="secondary-button" type="submit">Update product</button>
                                            </div>
                                        </form>

                                        <form class="delete-form" method="POST" action="{{ route('products.destroy', $product) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="danger-button" type="submit">Delete product</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </section>
        </main>

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
    </body>
</html>
