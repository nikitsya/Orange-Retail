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
        <div class="utility-bar">
            <div class="page-shell utility-bar-inner">
                <div class="utility-links">
                    <a href="{{ route('catalog.index') }}">Back to catalog</a>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('catalog.index', ['category' => $product->category]) }}">{{ $product->category }}</a>
                </div>

                <div class="utility-actions">
                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('products.index') }}">Inventory</a>
                        @else
                            <a href="{{ route('cart.index') }}">Cart</a>
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}">Sign in</a>
                    @endauth
                </div>
            </div>
        </div>

        <header class="masthead">
            <div class="page-shell">
                <div class="masthead-main">
                    <a class="brand-lockup" href="{{ route('home') }}">
                        @include('partials.brand-name', ['class' => 'brand-title'])
                    </a>

                    <form class="search-shell" method="GET" action="{{ route('catalog.index') }}">
                        <input
                            type="search"
                            name="search"
                            placeholder="Search for another product"
                            aria-label="Search products"
                        >
                        <button type="submit">Search</button>
                    </form>

                    <div class="masthead-actions">
                        @auth
                            @if (auth()->user()->role !== 'admin')
                                <a class="account-pill" href="{{ route('cart.index') }}">
                                    <div>
                                        <strong>Cart</strong>
                                        <span>Open cart</span>
                                    </div>
                                </a>
                            @else
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
                    <span class="section-kicker">{{ $product->category }}</span>
                    <h1 class="detail-heading">{{ $product->name }}</h1>
                    <p class="lede">{{ $product->description }}</p>

                    <div class="detail-gallery">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @else
                            <div class="detail-gallery-empty">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
                        @endif
                    </div>

                    @if ($product->image_url)
                        <p class="muted-copy">
                            <a href="{{ $product->image_url }}" target="_blank" rel="noreferrer">Open image in a new tab</a>
                        </p>
                    @endif
                </article>

                <aside class="summary-panel">
                    <span class="section-kicker">Product details</span>
                    <h2>{{ $product->brand }}</h2>
                    <p>Use this page to review packaging, price information, and the saved product metadata.</p>

                    <div class="summary-stats">
                        <div class="summary-stat">
                            <strong>{{ $product->price_display ?: 'In store' }}</strong>
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
                                <form method="POST" action="{{ route('cart.store', $product) }}">
                                    @csrf
                                    <button class="button-primary" type="submit">Add to cart</button>
                                </form>
                            @else
                                <a class="button-primary" href="{{ route('products.index') }}">Manage inventory</a>
                            @endif
                        @else
                            <a class="button-primary" href="{{ route('login') }}">Add</a>
                        @endauth

                        <a class="button-secondary" href="{{ route('catalog.index', ['category' => $product->category]) }}">More in {{ $product->category }}</a>
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
                            <strong>Weight</strong>
                            <div>{{ $product->weight_value !== null ? trim(number_format((float) $product->weight_value, 2) . ' ' . ($product->weight_unit ?: '')) : 'Not specified' }}</div>
                        </div>
                        <div class="detail-info-card">
                            <strong>Category line</strong>
                            <div>{{ $product->category }} / {{ $product->subcategory }}</div>
                        </div>
                    </div>
                </aside>
            </section>
        </main>
    </body>
</html>
