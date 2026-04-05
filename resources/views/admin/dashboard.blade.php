<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Admin Dashboard</title>
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

<main class="page-shell page-main stack admin-dashboard-page">
    @if (session('status'))
        <div class="flash-message">{{ session('status') }}</div>
    @endif

    <section class="hero-panel">
        <div class="hero-copy">
            <h1>Admin Dashboard</h1>
            <p>Monitor live inventory, pending orders, inactive products, and upcoming delivery dates from one control
                surface.</p>
        </div>

        <div class="summary-stats summary-stats-wide">
            <div class="summary-stat">
                <strong>{{ $productCount }}</strong>
                <span>Total products</span>
            </div>
            <div class="summary-stat">
                <strong>{{ $pendingOrders }}</strong>
                <span>Orders waiting for action</span>
            </div>
            <div class="summary-stat">
                <strong>{{ $lowStockProducts }}</strong>
                <span>Low-stock SKUs</span>
            </div>
            <div class="summary-stat">
                <strong>{{ $inactiveProducts }}</strong>
                <span>Inactive products</span>
            </div>
        </div>
    </section>

    <section class="dashboard-layout">
        <section class="section-panel stack">
            <div class="section-actions" style="justify-content: space-between;">
                <div>
                    <h2>Recent orders</h2>
                </div>
                <a class="button-secondary" href="{{ route('admin.orders.index') }}">Open full order queue</a>
            </div>

            <section class="order-list">
                @forelse ($recentOrders as $order)
                    <article class="summary-panel order-card">
                        <div class="order-card-head">
                            <div>
                                <span class="inventory-tag">{{ strtoupper($order->status) }}</span>
                                <h2>{{ $order->order_number }}</h2>
                                <p>{{ $order->customer_name }} | {{ $order->placed_at?->format('d M Y H:i') }}</p>
                            </div>
                            <div class="order-card-total">€{{ number_format((float) $order->total, 2) }}</div>
                        </div>
                        <div class="tile-actions">
                            <a class="button-primary" href="{{ route('orders.show', $order) }}">Open order</a>
                        </div>
                    </article>
                @empty
                    <section class="empty-panel">
                        <h2 class="section-heading">No orders yet</h2>
                        <p class="muted-copy">Completed checkouts will appear here for admin review.</p>
                    </section>
                @endforelse
            </section>
        </section>

        <aside class="summary-panel stack">
            <div>
                <h2>Latest stock movements</h2>
                <p>Recent stock changes from manual restocks, order reservations, and cancellation returns.</p>
            </div>

            <section class="mini-list">
                @forelse ($recentMovements as $movement)
                    <article class="mini-list-item">
                        <strong>{{ $movement->product?->name ?? 'Removed product' }}</strong>
                        <span>{{ $movement->occurred_at?->format('d M Y H:i') }}</span>
                        <span>{{ strtoupper(str_replace('_', ' ', $movement->type)) }} | {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</span>
                    </article>
                @empty
                    <p class="muted-copy">No stock movements have been recorded yet.</p>
                @endforelse
            </section>

            <div class="tile-actions">
                <a class="button-primary" href="{{ route('admin.stock.index') }}">Open Stock Center</a>
            </div>
        </aside>
    </section>
</main>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
