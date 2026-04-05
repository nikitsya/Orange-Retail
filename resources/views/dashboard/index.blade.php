<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Orange Retail | User Dashboard</title>
        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
    </head>
    <body>
        <div class="utility-bar">
            <div class="page-shell utility-bar-inner">
                <div class="utility-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('catalog.index') }}">Catalog</a>
                    <a href="{{ route('orders.index') }}">Orders</a>
                    <a href="{{ route('cart.index') }}">Cart</a>
                </div>

                <div class="utility-actions">
                    <span>{{ auth()->user()->name }}</span>
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
                    <a class="brand-lockup" href="{{ route('dashboard') }}">
                        @include('partials.brand-name', ['class' => 'brand-title'])
                    </a>

                    <form class="search-shell" method="GET" action="{{ route('catalog.index') }}">
                        <input type="search" name="search" placeholder="Search groceries or brands" aria-label="Search catalog">
                        <button class="search-image-button" type="submit" aria-label="Search">
                            <img src="{{ asset('images/ui/search.png') }}" alt="">
                            <span class="sr-only">Search</span>
                        </button>
                    </form>

                    <div class="masthead-actions">
                        <a class="account-pill" href="{{ route('cart.index') }}">
                            <div>
                                <strong>{{ $cartItemCount }} cart item{{ $cartItemCount === 1 ? '' : 's' }}</strong>
                                <span>{{ $cartSubtotal > 0 ? 'Cart subtotal €' . number_format($cartSubtotal, 2) : 'Cart is currently empty' }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-shell page-main stack">
            @if (session('status'))
                <div class="flash-message">{{ session('status') }}</div>
            @endif

            <section class="hero-panel">
                <div class="hero-copy">
                    <span class="section-kicker">User Dashboard</span>
                    <h1>User Dashboard</h1>
                    <p>Use this area to continue shopping, review your cart, and track every order placed in the supermarket system.</p>
                </div>

                <div class="hero-actions">
                    <a class="button-primary" href="{{ route('catalog.index') }}">Browse catalog</a>
                    <a class="button-secondary" href="{{ route('orders.index') }}">Order history</a>
                    <a class="button-secondary" href="{{ route('cart.index') }}">Open cart</a>
                </div>

                <div class="summary-stats">
                    <div class="summary-stat">
                        <strong>{{ $orders->count() }}</strong>
                        <span>Recent orders shown</span>
                    </div>
                    <div class="summary-stat">
                        <strong>{{ $cartItemCount }}</strong>
                        <span>Items in session cart</span>
                    </div>
                    <div class="summary-stat">
                        <strong>€{{ number_format($cartSubtotal, 2) }}</strong>
                        <span>Current cart subtotal</span>
                    </div>
                </div>
            </section>

            <section class="section-panel stack">
                <div class="section-actions" style="justify-content: space-between;">
                    <div>
                        <span class="section-kicker">Recent orders</span>
                        <h2>Latest customer activity</h2>
                    </div>
                    <a class="button-secondary" href="{{ route('orders.index') }}">View all orders</a>
                </div>

                @if ($orders->isEmpty())
                    <section class="empty-panel">
                        <h2 class="section-heading">No orders yet</h2>
                        <p class="muted-copy">Once you complete checkout, your orders will appear here with their current status.</p>
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
                                    <a class="button-primary" href="{{ route('orders.show', $order) }}">Open order</a>
                                </div>
                            </article>
                        @endforeach
                    </section>
                @endif
            </section>
        </main>

        @include('partials.masthead-stick-on-scroll')
    </body>
</html>
