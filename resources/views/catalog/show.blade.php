<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | {{ $product->name }}</title>
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
                <input
                    type="search"
                    name="search"
                    placeholder="Search for another product"
                    aria-label="Search products"
                >
                <button class="search-image-button" type="submit" aria-label="Search">
                    <img src="{{ asset('images/ui/search.png') }}" alt="">
                    <span class="sr-only">Search</span>
                </button>
            </form>

            <div class="masthead-actions">
                @auth
                    @if (auth()->user()->role === 'admin')
                        <a class="account-pill" href="{{ route('products.index') }}">
                            <div>
                                <strong>Inventory</strong>
                                <span>Open product management</span>
                            </div>
                        </a>
                    @endif
                @else
                    <a class="button-secondary" href="{{ route('login') }}">Login</a>
                    <a class="button-primary" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </div>
</header>

<main class="page-shell page-main stack">
    <section class="detail-layout">
        <article class="detail-panel">
            <h1 class="detail-heading">{{ $product->name }}</h1>
            <p class="lede">{{ $product->summary_text }}</p>

            <div class="detail-gallery @if (! $product->image_url) has-fallback-image @endif">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @else
                    <img src="{{ $fallbackProductImage }}" alt="{{ $product->name }}">
                @endif
            </div>

            @if ($product->image_url)
                <p class="muted-copy">
                    <a href="{{ $product->image_url }}" target="_blank" rel="noreferrer">Open image in a new tab</a>
                </p>
            @endif
        </article>

        <aside class="summary-panel">
            <h2>{{ $product->brand }}</h2>
            <p>Use this page to review packaging, price information, and the saved product metadata.</p>

            <div class="summary-stats">
                <div class="summary-stat">
                    <strong>{{ $product->formatted_price ?: 'In store' }}</strong>
                    <span>Price</span>
                </div>
                <div class="summary-stat">
                    <strong>{{ $product->unit_price_display ?: ($product->pack_size ?: $product->unit_type) }}</strong>
                    <span>Unit</span>
                </div>
                <div class="summary-stat">
                    <strong>{{ $product->subcategory }}</strong>
                    <span>Subcategory</span>
                </div>
            </div>

            <div class="detail-actions" style="margin-top: 18px;">
                @auth
                    @if (auth()->user()->role !== 'admin')
                        @if ($product->stock > 0)
                            <form method="POST" action="{{ route('cart.store', $product) }}">
                                @csrf
                                <button class="button-primary" type="submit">Add to cart</button>
                            </form>
                        @else
                            <span class="button-secondary" aria-disabled="true">Out of stock</span>
                        @endif
                    @else
                        <a class="button-primary" href="{{ route('admin.stock.index') }}">Manage stock</a>
                    @endif
                @else
                    <a class="button-primary" href="{{ route('login') }}">Add</a>
                @endauth

                <a class="button-secondary" href="{{ route('catalog.index', ['category' => $product->category]) }}">More
                    in {{ $product->category }}</a>
            </div>

            <div class="detail-info-grid">
                <div class="detail-info-card">
                    <strong>SKU</strong>
                    <div>{{ $product->sku }}</div>
                </div>
                <div class="detail-info-card">
                    <strong>Barcode</strong>
                    <div>{{ $product->barcode ?: 'Not specified' }}</div>
                </div>
                <div class="detail-info-card">
                    <strong>Unit type</strong>
                    <div>{{ $product->unit_type }}</div>
                </div>
                <div class="detail-info-card">
                    <strong>Pack size</strong>
                    <div>{{ $product->pack_size ?: 'Not specified' }}</div>
                </div>
                <div class="detail-info-card">
                    <strong>Category line</strong>
                    <div>{{ $product->category }} / {{ $product->subcategory }}</div>
                </div>
                <div class="detail-info-card">
                    <strong>Stock</strong>
                    <div>{{ $product->stock > 0 ? $product->stock . ' available' : 'Out of stock' }}</div>
                </div>
            </div>
        </aside>
    </section>
</main>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
