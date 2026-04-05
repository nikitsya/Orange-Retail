<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Orange Retail | Orders</title>
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

        <main class="page-shell page-main stack">
            @if (session('status'))
                <div class="flash-message">{{ session('status') }}</div>
            @endif

            <section class="section-panel stack">
                <div class="section-actions" style="justify-content: space-between;">
                    <div>
                        <span class="section-kicker">Orders</span>
                        <h1 class="page-title">Order history</h1>
                    </div>
                    <a class="button-primary" href="{{ route('catalog.index') }}">Continue shopping</a>
                </div>

                @if ($orders->isEmpty())
                    <section class="empty-panel">
                        <h2 class="section-heading">No orders yet</h2>
                        <p class="muted-copy">Your checkout history will appear here after the first completed order.</p>
                    </section>
                @else
                    <section class="order-list">
                        @foreach ($orders as $order)
                            <article class="summary-panel order-card">
                                <div class="order-card-head">
                                    <div>
                                        <span class="inventory-tag">{{ strtoupper($order->status) }}</span>
                                        <h2>{{ $order->order_number }}</h2>
                                        <p>{{ $order->placed_at?->format('d M Y H:i') }} | {{ $order->items_count }} line item{{ $order->items_count === 1 ? '' : 's' }}</p>
                                    </div>
                                    <div class="order-card-total">€{{ number_format((float) $order->total, 2) }}</div>
                                </div>

                                <div class="tile-actions">
                                    <a class="button-primary" href="{{ route('orders.show', $order) }}">View details</a>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    {{ $orders->links() }}
                @endif
            </section>
        </main>
    </body>
</html>
