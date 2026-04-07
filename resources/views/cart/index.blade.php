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

    <section class="hero-panel cart-page-hero">
        <div class="hero-copy">
            <h1>Your cart</h1>
            <p>Review your selected items before checkout or continue shopping.</p>
        </div>

        <div class="hero-actions compact-actions">
            <a class="button-primary" href="{{ route('checkout.create') }}">Checkout</a>
            <a class="button-primary" href="{{ route('catalog.index') }}">Continue shopping</a>
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
                                    <h2>{{ $item['product']->name }}</h2>
                                    <p>{{ $item['product']->brand }}</p>

                                    <div style="margin: 12px 0 10px;">
                                        <div style="font-size: 1.15rem; font-weight: 700; color: var(--ink);">
                                            &euro;{{ number_format((float) ($item['product']->price_value ?? 0), 2) }} each
                                        </div>
                                        @if ($item['product']->unit_price_display)
                                            <div style="margin-top: 2px; font-size: 0.95rem; color: var(--muted);">
                                                {{ $item['product']->unit_price_display }}
                                            </div>
                                        @endif
                                        <div style="margin-top: 8px; font-size: 1rem; font-weight: 600; color: var(--ink);">
                                            Total: &euro;{{ number_format((float) $item['line_total'], 2) }}
                                        </div>
                                    </div>

                                    <div class="filter-notes">
                                        @if ($item['product']->stock <= 5)
                                            <span class="filter-note">Only {{ $item['product']->stock }} left in stock</span>
                                        @endif
                                    </div>

                                    <div class="tile-actions" style="margin-top: 18px;">
                                        <a class="button-secondary"
                                           href="{{ route('catalog.show', ['product' => $item['product'], 'from' => 'cart']) }}">View product</a>

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
            <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px;">
                <h2 style="margin: 0;">Cart summary</h2>
                <span class="muted-copy" style="white-space: nowrap;">{{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}</span>
            </div>

            <div class="stack" style="gap: 14px; margin-top: 24px;">
                <div style="display: flex; justify-content: space-between; gap: 16px;">
                    <span class="muted-copy">Products</span>
                    <strong>{{ $items->count() }}</strong>
                </div>

                <div style="display: flex; justify-content: space-between; gap: 16px;">
                    <span class="muted-copy">Subtotal</span>
                    <strong>&euro;{{ number_format($subtotal, 2) }}</strong>
                </div>

                <div style="height: 1px; background: rgba(198, 111, 0, 0.14);"></div>

                <div style="display: flex; justify-content: space-between; gap: 16px; align-items: baseline;">
                    <strong style="font-size: 1.15rem;">Total</strong>
                    <strong style="font-size: 1.35rem;">&euro;{{ number_format($subtotal, 2) }}</strong>
                </div>
            </div>

            <h2 style="display: none;">Cart summary</h2>
            <p style="display: none;">Check the number of items in your cart and the current subtotal.</p>
            <strong class="summary-figure" style="display: none;">{{ $itemCount }}</strong>

            <div class="summary-stats" style="display: none; margin-top: 20px;">
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
