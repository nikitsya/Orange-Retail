<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Cart</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body>
@php
    $fallbackProductImage = asset('images/products/picture.png');
@endphp

<div class="utility-bar">
    <div class="page-shell utility-bar-inner">
        @include('partials.app-nav')

        @include('partials.utility-actions')
    </div>
</div>

<header class="masthead">
    <div class="page-shell">
        <div class="masthead-main">
            <a class="brand-lockup" href="{{ route('catalog.index') }}">
                @include('partials.brand-name', ['class' => 'brand-title'])
            </a>

            <form class="search-shell" method="GET" action="{{ route('catalog.index') }}">
                <input type="search" name="search" placeholder="Search for more products" aria-label="Search products">
                <button class="search-image-button" type="submit" aria-label="Search">
                    <img src="{{ asset('images/ui/search.png') }}" alt="">
                    <span class="sr-only">Search</span>
                </button>
            </form>

            <div class="masthead-actions">
                <a class="account-pill" href="{{ route('catalog.index') }}">
                    <div>
                        <strong>{{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}</strong>
                        <span>Currently in cart</span>
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
            <h1>Your cart</h1>
            <p>This is a session-based customer cart. It stores the products selected for the current session.</p>
        </div>

        <div class="hero-actions">
            <a class="button-primary" href="{{ route('checkout.create') }}">Checkout</a>
            <a class="button-primary" href="{{ route('catalog.index') }}">Continue shopping</a>
            <a class="button-secondary" href="{{ route('catalog.index') }}">Back to catalog</a>
        </div>
    </section>

    <section class="cart-layout">
        <div>
            @if ($items->isEmpty())
                <section class="empty-panel">
                    <h2 class="section-heading">Your cart is empty</h2>
                    <p class="muted-copy">Open the catalog and add a product to the cart.</p>
                    <div class="hero-actions" style="justify-content: center;">
                        <a class="button-primary" href="{{ route('catalog.index') }}">Browse catalog</a>
                    </div>
                </section>
            @else
                <section class="cart-list">
                    @foreach ($items as $item)
                        <article class="summary-panel">
                            <div class="cart-line">
                                <div class="cart-thumb @if (! $item['product']->image_url) has-fallback-image @endif">
                                    @if ($item['product']->image_url)
                                        <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}">
                                    @else
                                        <img src="{{ $fallbackProductImage }}" alt="{{ $item['product']->name }}">
                                    @endif
                                </div>

                                <div>
                                    <span class="inventory-tag">{{ $item['product']->category }}</span>
                                    <h2>{{ $item['product']->name }}</h2>
                                    <p>{{ $item['product']->summary_text }}</p>

                                    <div class="filter-notes">
                                        <span class="filter-note">Brand: {{ $item['product']->brand }}</span>
                                        <span class="filter-note">Quantity: {{ $item['quantity'] }}</span>
                                        <span class="filter-note">{{ $item['product']->subcategory }}</span>
                                        <span class="filter-note">Stock: {{ $item['product']->stock }}</span>
                                    </div>

                                    <div class="tile-actions" style="margin-top: 18px;">
                                        <a class="button-secondary"
                                           href="{{ route('catalog.show', $item['product']) }}">View product</a>

                                        <form method="POST" action="{{ route('cart.update', $item['product']) }}">
                                            @csrf
                                            @method('PUT')
                                            <input class="field" style="min-height: 44px; width: 96px;" type="number"
                                                   min="1" max="{{ $item['product']->stock }}" name="quantity"
                                                   value="{{ $item['quantity'] }}">
                                            <button class="button-secondary" type="submit">Update</button>
                                        </form>

                                        <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button-danger" type="submit">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endif
        </div>

        <aside class="summary-panel">
            <h2>Session cart</h2>
            <p>Total quantity of items currently stored in the session cart.</p>
            <strong class="summary-figure">{{ $itemCount }}</strong>

            <div class="summary-stats" style="margin-top: 20px;">
                <div class="summary-stat">
                    <strong>{{ $items->count() }}</strong>
                    <span>Distinct lines</span>
                </div>
                <div class="summary-stat">
                    <strong>{{ $itemCount === 0 ? 'Empty' : 'Active' }}</strong>
                    <span>Status</span>
                </div>
                <div class="summary-stat">
                    <strong>€{{ number_format($subtotal, 2) }}</strong>
                    <span>Subtotal</span>
                </div>
            </div>

            @if ($itemCount > 0)
                <div class="tile-actions" style="margin-top: 20px;">
                    <a class="button-primary" href="{{ route('checkout.create') }}">Proceed to checkout</a>
                </div>
            @endif
        </aside>
    </section>
</main>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
