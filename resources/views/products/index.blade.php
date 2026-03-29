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

            a {
                color: inherit;
                text-decoration: none;
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
                width: min(calc(100% - 24px), 1180px);
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

            .product-form {
                display: grid;
                gap: 14px;
            }

            .field-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
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

            .checkbox-row {
                display: flex;
                align-items: center;
                gap: 10px;
                color: var(--muted);
                font-size: 0.9rem;
            }

            .checkbox-row input {
                width: auto;
            }

            .field-error {
                color: var(--danger);
                font-size: 0.8rem;
                font-weight: 400;
            }

            .product-list {
                display: grid;
                gap: 16px;
            }

            .product-item {
                padding: 18px;
                border: 1px solid var(--line);
                border-radius: 18px;
                background: var(--surface-soft);
            }

            .product-item-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-bottom: 14px;
            }

            .product-item-header h3 {
                margin: 0;
                font-size: 1.08rem;
            }

            .meta {
                color: var(--muted);
                font-size: 0.84rem;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 0.35rem 0.7rem;
                border-radius: 999px;
                font-size: 0.74rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
            }

            .badge-active {
                background: #def2e3;
                color: #1d6a38;
            }

            .badge-inactive {
                background: #eceeed;
                color: #677162;
            }

            .item-actions {
                display: flex;
                gap: 10px;
                margin-top: 12px;
            }

            .delete-form {
                margin-top: 12px;
            }

            .inline-form {
                display: grid;
                gap: 12px;
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

            @media (max-width: 640px) {
                .topbar-inner {
                    flex-direction: column;
                    align-items: flex-start;
                    justify-content: center;
                    padding: 12px 0;
                }

                .field-grid {
                    grid-template-columns: 1fr;
                }

                .product-item-header,
                .section-title {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .item-actions {
                    flex-direction: column;
                }
            }
        </style>
    </head>
    <body>
        <header class="topbar">
            <div class="topbar-inner">
                <div class="brand-block">
                    <strong>Supermarket Management</strong>
                    <span>Admin panel for full store product control</span>
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
                    Review inventory, update pricing, maintain stock values, and keep the active
                    store assortment current from one admin workspace.
                </p>

                <div class="stats">
                    <div class="stat-card">
                        <strong>{{ $products->count() }}</strong>
                        <span>Total products in the catalog</span>
                    </div>

                    <div class="stat-card">
                        <strong>{{ $products->where('is_active', true)->count() }}</strong>
                        <span>Products currently visible as active</span>
                    </div>

                    <div class="stat-card">
                        <strong>{{ $products->sum('stock') }}</strong>
                        <span>Total units tracked across all products</span>
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
                            <p>Create a new store item directly from the manager page.</p>
                        </div>
                    </div>

                    <form class="product-form" method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <div class="field-grid">
                            <label for="name">
                                Product name
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label for="sku">
                                SKU
                                <input id="sku" type="text" name="sku" value="{{ old('sku') }}" required>
                                @error('sku')
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

                            <label for="price">
                                Price
                                <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required>
                                @error('price')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <div class="field-grid">
                            <label for="stock">
                                Stock
                                <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required>
                                @error('stock')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="checkbox-row" for="is_active">
                                <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                                Mark this product as active
                            </label>
                        </div>

                        <label for="description">
                            Description
                            <textarea id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
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
                            <p>Update or remove items already stored in the catalog.</p>
                        </div>
                    </div>

                    @if ($products->isEmpty())
                        <div class="empty-state">No products are available yet. Add the first one above.</div>
                    @else
                        <div class="product-list">
                            @foreach ($products as $product)
                                <article class="product-item">
                                    <div class="product-item-header">
                                        <div>
                                            <h3>{{ $product->name }}</h3>
                                            <div class="meta">
                                                SKU: {{ $product->sku }} |
                                                Category: {{ $product->category }} |
                                                Price: ${{ number_format((float) $product->price, 2) }} |
                                                Stock: {{ $product->stock }}
                                            </div>
                                        </div>

                                        <span class="badge {{ $product->is_active ? 'badge-active' : 'badge-inactive' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>

                                    <form class="inline-form" method="POST" action="{{ route('products.update', $product) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="field-grid">
                                            <label>
                                                Product name
                                                <input type="text" name="name" value="{{ $product->name }}" required>
                                            </label>

                                            <label>
                                                SKU
                                                <input type="text" name="sku" value="{{ $product->sku }}" required>
                                            </label>
                                        </div>

                                        <div class="field-grid">
                                            <label>
                                                Category
                                                <input type="text" name="category" value="{{ $product->category }}" required>
                                            </label>

                                            <label>
                                                Price
                                                <input type="number" step="0.01" min="0" name="price" value="{{ number_format((float) $product->price, 2, '.', '') }}" required>
                                            </label>
                                        </div>

                                        <div class="field-grid">
                                            <label>
                                                Stock
                                                <input type="number" min="0" name="stock" value="{{ $product->stock }}" required>
                                            </label>

                                            <label class="checkbox-row">
                                                <input type="checkbox" name="is_active" value="1" @checked($product->is_active)>
                                                Active in catalog
                                            </label>
                                        </div>

                                        <label>
                                            Description
                                            <textarea name="description">{{ $product->description }}</textarea>
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
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </section>
        </main>
    </body>
</html>
