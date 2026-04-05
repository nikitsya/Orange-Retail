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
        <div class="utility-bar">
            <div class="page-shell utility-bar-inner">
                <div class="utility-links">
                    @if ($isAdmin)
                        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        <a href="{{ route('admin.orders.index') }}">Orders</a>
                    @else
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                        <a href="{{ route('orders.index') }}">Orders</a>
                    @endif
                </div>

                <div class="utility-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="utility-button" type="submit">Sign out</button>
                    </form>
                </div>
            </div>
        </div>

        <main class="page-shell page-main detail-layout">
            <section class="detail-panel stack">
                @if (session('status'))
                    <div class="flash-message">{{ session('status') }}</div>
                @endif

                <div>
                    <span class="section-kicker">{{ strtoupper($order->status) }}</span>
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
                        <span class="section-kicker">Notes</span>
                        <p>{{ $order->notes }}</p>
                    </section>
                @endif

                <section class="order-list">
                    @foreach ($order->items as $item)
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
                    @endforeach
                </section>
            </section>

            <aside class="summary-panel stack">
                <div>
                    <span class="section-kicker">Status</span>
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
                                    <option value="{{ $status }}" @selected($status === $order->status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </label>

                        <button class="button-primary" type="submit">Save status</button>
                    </form>
                @endif
            </aside>
        </main>
    </body>
</html>
