<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Favourites</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body>
@php
    $fallbackProductImage = asset('images/products/picture.png');
@endphp

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
                    placeholder="Search groceries or brands"
                    aria-label="Search catalog"
                >
                <button class="search-image-button" type="submit" aria-label="Search">
                    <img src="{{ asset('images/ui/search.png') }}" alt="">
                    <span class="sr-only">Search</span>
                </button>
            </form>

            <div class="masthead-actions">
                <a class="button-primary" href="{{ route('catalog.index') }}">Browse catalog</a>
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

<main class="page-shell page-main stack">
    @if (session('status'))
        <div class="flash-message">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
    @endif

    <section class="hero-panel cart-page-hero">
        <div class="hero-copy">
            <h1>Your favourites</h1>
            <p>Keep a short list of products you want to revisit, compare, or add to your cart later.</p>
        </div>

        <div class="hero-actions compact-actions">
            <a class="button-primary" href="{{ route('catalog.index') }}">Browse catalog</a>
            <a class="button-secondary" href="{{ route('cart.index') }}">Open cart</a>
        </div>
    </section>

    @if ($products->isEmpty())
        <section class="empty-panel">
            <h2 class="section-heading">No favourite products yet</h2>
            <p class="muted-copy">Use the heart icon in the catalog to save products here for later.</p>
        </section>
    @else
        <section class="catalog-grid">
            @foreach ($products as $product)
                <article class="product-card">
                    <a class="product-title-link" href="{{ route('catalog.show', $product) }}">
                        <div class="product-media @if (! $product->image_url) has-fallback-image @endif">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            @else
                                <img src="{{ $fallbackProductImage }}" alt="{{ $product->name }}">
                            @endif
                        </div>
                    </a>

                    <h3>
                        <a class="product-title-link"
                           href="{{ route('catalog.show', $product) }}">{{ $product->name }}</a>
                    </h3>
                    <div class="product-meta">{{ $product->brand }} | {{ $product->subcategory }}</div>

                    @if (($cartQuantities[$product->id] ?? 0) > 0)
                        <div class="tile-actions" style="margin-top: 10px;">
                            <span class="button-secondary">In cart: {{ $cartQuantities[$product->id] }}</span>
                        </div>
                    @endif

                    <div class="price-row">
                        <div class="price-block">
                            @if ($product->formatted_price)
                                <strong>{{ $product->formatted_price }}</strong>
                            @else
                                <strong>In store</strong>
                            @endif

                            @if ($product->unit_price_display)
                                <span>{{ $product->unit_price_display }}</span>
                            @else
                                <span>{{ $product->unit_type }}{{ $product->pack_size ? ' | ' . $product->pack_size : '' }}</span>
                            @endif

                            <span>
                                @if ($product->stock > 5)
                                    In stock
                                @elseif ($product->stock > 0)
                                    Only {{ $product->stock }} left
                                @else
                                    Out of stock
                                @endif
                            </span>
                        </div>

                        <div class="tile-actions">
                            <form method="POST" action="{{ route('favorites.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button class="button-secondary" type="submit" aria-label="Remove from favourites">&#9829;</button>
                            </form>

                            @if ($product->stock > 0)
                                <form method="POST" action="{{ route('cart.store', $product) }}">
                                    @csrf
                                    <button class="button-primary" type="submit">Add to cart</button>
                                </form>
                            @else
                                <span class="button-secondary" aria-disabled="true">Out of stock</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        {{ $products->links() }}
    @endif
</main>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
