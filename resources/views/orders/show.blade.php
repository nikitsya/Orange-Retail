<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | {{ $order->order_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body>
<header class="masthead">
    <div class="page-shell">
        <div class="masthead-main">
            <a class="brand-lockup" href="{{ route('catalog.index') }}">
                @include('partials.brand-name', ['class' => 'brand-title'])
            </a>

            <form class="search-shell" method="GET" action="{{ $isAdmin ? route('admin.orders.index') : route('orders.index') }}" data-live-search>
                <input
                    type="search"
                    name="search"
                    placeholder="Search orders"
                    aria-label="Search orders"
                >
                <span class="search-icon" aria-hidden="true"><img src="{{ asset('images/ui/search.png') }}" alt=""></span>
            </form>

            <div class="masthead-actions">
                <a class="button-secondary" href="{{ $isAdmin ? route('admin.orders.index') : route('orders.index') }}">Back to orders</a>
            </div>
        </div>
    </div>
</header>

<div class="utility-bar">
    <div class="page-shell utility-bar-inner">
        @include('partials.app-nav')

        @include('partials.utility-actions')
    </div>
</div>

<main class="page-shell page-main detail-layout">
    <section class="detail-panel stack">
        @if (session('status'))
            <div class="flash-message">{{ session('status') }}</div>
        @endif

        <div>
            <h1 class="detail-heading">{{ $order->order_number }}</h1>
            <p class="lede">Placed on {{ $order->placed_at?->format('d M Y H:i') }} by {{ $order->customer_name }}.</p>
        </div>

        <div class="detail-info-grid">
            <div class="detail-info-card">
                <strong>Email</strong>
                <div>{{ $order->customer_email }}</div>
            </div>
            <div class="detail-info-card">
                <strong>Total</strong>
                <div>€{{ number_format((float) $order->total, 2) }}</div>
            </div>
            <div class="detail-info-card">
                <strong>Address</strong>
                <div>{{ $order->shipping_address_line_1 }}{{ $order->shipping_address_line_2 ? ', ' . $order->shipping_address_line_2 : '' }}</div>
            </div>
            <div class="detail-info-card">
                <strong>City and postal code</strong>
                <div>{{ $order->shipping_city }}{{ $order->shipping_county ? ', ' . $order->shipping_county : '' }} {{ $order->shipping_postal_code }}</div>
            </div>
        </div>

        @if ($order->notes)
            <section class="summary-panel">
                <p>{{ $order->notes }}</p>
            </section>
        @endif

        <section class="order-list">
            @foreach ($order->items as $item)
                @if (! $isAdmin)
                <article style="padding: 12px 8px; border-bottom: 1px solid rgba(198, 111, 0, 0.14);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                        <div style="min-width: 0;">
                            <div style="font-size: 1rem; font-weight: 700; line-height: 1.35; color: var(--ink);">
                                {{ $item->product_name }}
                            </div>
                            <div class="muted-copy" style="margin-top: 4px; font-size: 0.92rem;">
                                Qty {{ $item->quantity }}
                            </div>
                        </div>
                        <div style="text-align: right; white-space: nowrap; font-size: 1rem; font-weight: 700; color: var(--ink);">
                            €{{ number_format((float) $item->line_total, 2) }}
                        </div>
                    </div>
                </article>
                @else

                <article class="summary-panel order-card">
                    <div class="order-card-head">
                        <div>
                            <span class="inventory-tag">{{ $item->product_brand ?: 'Product' }}</span>
                            <h2>{{ $item->product_name }}</h2>
                            <p>SKU {{ $item->product_sku }} | Quantity {{ $item->quantity }}</p>
                        </div>
                        <div class="order-card-total">€{{ number_format((float) $item->line_total, 2) }}</div>
                    </div>
                </article>
                @endif
            @endforeach
        </section>
    </section>

    <aside class="summary-panel stack">
        <div>
            <h2>{{ ucfirst($order->status) }}</h2>
            <p>Track the current state of the order lifecycle from placement through completion.</p>
        </div>

        @if ($isAdmin)
            <form class="stack" method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PATCH')
                <label class="field-label">
                    Update status
                    <select class="field-select" name="status">
                        @foreach ($statuses as $status)
                            <option
                                value="{{ $status }}" @selected($status === $order->status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <button class="button-primary" type="submit">Save status</button>
            </form>
        @endif
    </aside>
</main>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
