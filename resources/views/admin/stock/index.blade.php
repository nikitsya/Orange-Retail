<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Stock Center</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body>
@php
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

<main class="page-shell page-main stock-layout">
    <section class="section-panel stack">
        @if (session('status'))
            <div class="flash-message">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <div>
            <h1 class="page-title">Warehouse and delivery planning</h1>
            <p class="muted-copy">Review live stock, last restock dates, next expected deliveries, and record manual
                warehouse updates.</p>
        </div>

        <div class="summary-stats summary-stats-wide">
            <div class="summary-stat">
                <strong>{{ $outOfStockCount }}</strong>
                <span>Out of stock</span>
            </div>
            <div class="summary-stat">
                <strong>{{ $lowStockCount }}</strong>
                <span>At or below minimum</span>
            </div>
            <div class="summary-stat">
                <strong>{{ $incomingDeliveryCount }}</strong>
                <span>Deliveries due within 7 days</span>
            </div>
        </div>

        <form class="form-grid-3" method="GET" action="{{ route('admin.stock.index') }}" data-auto-filter-form>
            <label class="field-label">
                Search
                <input class="field" type="search" name="search" value="{{ $search }}"
                       placeholder="Product, SKU, brand" data-auto-filter-search>
            </label>

            <label class="field-label">
                Category
                <select class="field-select" name="category" data-auto-filter-select>
                    <option value="">All categories</option>
                    @foreach ($categories as $catalogCategory)
                        <option
                            value="{{ $catalogCategory }}" @selected($catalogCategory === $category)>{{ $catalogCategory }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field-label">
                Stock state
                <select class="field-select" name="stock_state" data-auto-filter-select>
                    <option value="">All states</option>
                    <option value="out" @selected($stockState === 'out')>Out of stock</option>
                    <option value="low" @selected($stockState === 'low')>At or below minimum</option>
                    <option value="healthy" @selected($stockState === 'healthy')>Healthy stock</option>
                </select>
            </label>
        </form>

        @if ($products->isEmpty())
            <section class="empty-panel">
                <h2 class="section-heading">No products found</h2>
                <p class="muted-copy">No products matched the current Stock Center filters.</p>
            </section>
        @else
            <section class="stock-card-list">
                @foreach ($products as $product)
                    <article class="summary-panel stock-card">
                        <div class="order-card-head">
                            <div>
                                <span class="inventory-tag">{{ $product->category }}</span>
                                <h2>{{ $product->name }}</h2>
                                <p>{{ $product->brand }} | SKU {{ $product->sku }}</p>
                            </div>
                            <div
                                class="stock-pill {{ $product->stock === 0 ? 'is-danger' : ($product->stock <= $product->minimum_stock_level ? 'is-warning' : 'is-ok') }}">
                                {{ $product->stock }} in stock
                            </div>
                        </div>

                        <form class="stack" method="POST" action="{{ route('admin.stock.update', $product) }}">
                            @csrf
                            @method('PATCH')

                            <div class="form-grid-2">
                                <label class="field-label">
                                    Current stock
                                    <input class="field" type="number" min="0" name="stock"
                                           value="{{ old('stock', $product->stock) }}" required>
                                </label>

                                <label class="field-label">
                                    Minimum stock level
                                    <input class="field" type="number" min="0" name="minimum_stock_level"
                                           value="{{ old('minimum_stock_level', $product->minimum_stock_level) }}" required>
                                </label>

                                <label class="field-label">
                                    Last restocked at
                                    <input class="field" type="datetime-local" name="last_restocked_at"
                                           value="{{ old('last_restocked_at', $product->last_restocked_at?->format('Y-m-d\\TH:i')) }}">
                                </label>

                                <label class="field-label">
                                    Next delivery due
                                    <input class="field" type="datetime-local" name="next_delivery_due_at"
                                           value="{{ old('next_delivery_due_at', $product->next_delivery_due_at?->format('Y-m-d\\TH:i')) }}">
                                </label>
                            </div>

                            <label class="field-label">
                                Stock note
                                <textarea class="field-area field-area-compact"
                                          name="stock_note">{{ old('stock_note') }}</textarea>
                            </label>

                            <div class="tile-actions">
                                <button class="button-primary" type="submit">Save stock update</button>
                            </div>
                        </form>
                    </article>
                @endforeach
            </section>

            @if ($products->hasPages())
                <nav class="pagination-nav" aria-label="Stock Center pages">
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

    <aside class="summary-panel stack">
        <div>
            <h2>Products at minimum stock</h2>
            <p>Review the items that have reached their minimum stock level or already dropped below it.</p>
        </div>

        <section class="mini-list">
            @forelse ($lowStockProducts as $product)
                <article class="mini-list-item {{ $product->stock === 0 ? 'is-danger' : 'is-warning' }}">
                    <strong>{{ $product->name }}</strong>
                    <span>{{ $product->brand }} | SKU {{ $product->sku }}</span>
                    <span>Stock {{ $product->stock }} | Minimum {{ $product->minimum_stock_level }}</span>
                    @if ($product->next_delivery_due_at)
                        <span>Next delivery {{ $product->next_delivery_due_at->format('d M Y H:i') }}</span>
                    @else
                        <span>No delivery scheduled</span>
                    @endif
                </article>
            @empty
                <p class="muted-copy">No products are currently at or below their minimum stock level.</p>
            @endforelse
        </section>
    </aside>
</main>
<script>
    (() => {
        const filterForm = document.querySelector('[data-auto-filter-form]');

        if (!filterForm) {
            return;
        }

        const searchInput = filterForm.querySelector('[data-auto-filter-search]');
        const selects = filterForm.querySelectorAll('[data-auto-filter-select]');
        let searchDebounceTimer = null;

        const submitFilters = () => {
            if (searchDebounceTimer) {
                clearTimeout(searchDebounceTimer);
            }

            filterForm.requestSubmit();
        };

        selects.forEach((select) => {
            select.addEventListener('change', submitFilters);
        });

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                if (searchDebounceTimer) {
                    clearTimeout(searchDebounceTimer);
                }

                searchDebounceTimer = setTimeout(() => {
                    filterForm.requestSubmit();
                }, 350);
            });
        }
    })();
</script>
</body>
</html>
