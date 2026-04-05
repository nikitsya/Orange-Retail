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
            <span class="section-kicker">Stock Center</span>
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
                <span>Low stock</span>
            </div>
            <div class="summary-stat">
                <strong>{{ $incomingDeliveryCount }}</strong>
                <span>Deliveries due within 7 days</span>
            </div>
        </div>

        <form class="form-grid-3" method="GET" action="{{ route('admin.stock.index') }}">
            <label class="field-label">
                Search
                <input class="field" type="search" name="search" value="{{ $search }}"
                       placeholder="Product, SKU, brand">
            </label>

            <label class="field-label">
                Category
                <select class="field-select" name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $catalogCategory)
                        <option
                            value="{{ $catalogCategory }}" @selected($catalogCategory === $category)>{{ $catalogCategory }}</option>
                    @endforeach
                </select>
            </label>

            <label class="field-label">
                Stock state
                <select class="field-select" name="stock_state">
                    <option value="">All states</option>
                    <option value="out" @selected($stockState === 'out')>Out of stock</option>
                    <option value="low" @selected($stockState === 'low')>Low stock</option>
                    <option value="healthy" @selected($stockState === 'healthy')>Healthy stock</option>
                </select>
            </label>

            <div class="tile-actions">
                <button class="button-primary" type="submit">Apply filters</button>
            </div>
        </form>

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
                            class="stock-pill {{ $product->stock === 0 ? 'is-danger' : ($product->stock <= 5 ? 'is-warning' : 'is-ok') }}">
                            {{ $product->stock }} in stock
                        </div>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-card">
                            <strong>Last restock</strong>
                            <div>{{ $product->last_restocked_at?->format('d M Y H:i') ?? 'Not recorded yet' }}</div>
                        </div>
                        <div class="detail-info-card">
                            <strong>Next delivery</strong>
                            <div>{{ $product->next_delivery_due_at?->format('d M Y H:i') ?? 'Not scheduled' }}</div>
                        </div>
                    </div>

                    <form class="stack" method="POST" action="{{ route('admin.stock.update', $product) }}">
                        @csrf
                        @method('PATCH')

                        <div class="form-grid-3">
                            <label class="field-label">
                                Current stock
                                <input class="field" type="number" min="0" name="stock"
                                       value="{{ old('stock', $product->stock) }}" required>
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

        {{ $products->links() }}
    </section>

    <aside class="summary-panel stack">
        <div>
            <span class="section-kicker">Movement log</span>
            <h2>Latest stock activity</h2>
            <p>Track the latest manual adjustments, sales, returns, and delivery planning updates.</p>
        </div>

        <section class="mini-list">
            @forelse ($recentMovements as $movement)
                <article class="mini-list-item">
                    <strong>{{ $movement->product?->name ?? 'Removed product' }}</strong>
                    <span>{{ strtoupper(str_replace('_', ' ', $movement->type)) }}</span>
                    <span>{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }} | {{ $movement->occurred_at?->format('d M Y H:i') }}</span>
                    @if ($movement->note)
                        <span>{{ $movement->note }}</span>
                    @endif
                </article>
            @empty
                <p class="muted-copy">No stock events have been recorded yet.</p>
            @endforelse
        </section>
    </aside>
</main>
</body>
</html>
