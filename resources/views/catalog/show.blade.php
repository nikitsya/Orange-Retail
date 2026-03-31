<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $product->name }}</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f3f6f1;
                --surface: #ffffff;
                --surface-soft: #f7f9f5;
                --ink: #182119;
                --muted: #657062;
                --line: #d8e0d4;
                --brand: #2d6b45;
                --brand-strong: #1f5234;
                --shadow: 0 18px 42px rgba(18, 31, 20, 0.08);
                --radius: 24px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: Arial, Helvetica, sans-serif;
                background: var(--bg);
                color: var(--ink);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .shell {
                width: min(calc(100% - 24px), 1040px);
                margin: 0 auto;
            }

            .topbar {
                position: sticky;
                top: 0;
                z-index: 20;
                background: rgba(243, 246, 241, 0.94);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(24, 33, 25, 0.08);
            }

            .topbar-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                min-height: 74px;
            }

            .topbar-actions {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .top-link,
            .logout-button,
            .primary-button {
                min-height: 42px;
                padding: 0.75rem 1rem;
                border-radius: 12px;
                border: 1px solid var(--line);
                background: var(--surface);
                color: var(--ink);
                font: inherit;
                font-size: 0.9rem;
                font-weight: 700;
                cursor: pointer;
            }

            .logout-button,
            .primary-button {
                border-color: transparent;
                background: linear-gradient(135deg, var(--brand), var(--brand-strong));
                color: #fff;
            }

            .logout-form,
            .cart-form {
                margin: 0;
            }

            main.shell {
                padding: 30px 0 42px;
            }

            .product-layout {
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
                gap: 20px;
            }

            .panel {
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            .hero-panel,
            .meta-panel {
                padding: 24px;
            }

            .eyebrow {
                display: inline-flex;
                padding: 0.38rem 0.65rem;
                border-radius: 999px;
                background: #eef5ef;
                color: var(--brand-strong);
                font-size: 0.76rem;
                font-weight: 700;
                letter-spacing: 0.03em;
                text-transform: uppercase;
            }

            h1 {
                margin: 16px 0 10px;
                font-size: clamp(2rem, 4vw, 3rem);
                letter-spacing: -0.04em;
                line-height: 1.05;
            }

            .lead {
                margin: 0;
                color: var(--muted);
                line-height: 1.75;
            }

            .image-card {
                margin-top: 22px;
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 18px;
                background: var(--surface-soft);
            }

            .image-card img {
                width: 100%;
                max-height: 320px;
                object-fit: cover;
                display: block;
            }

            .image-caption,
            .image-placeholder {
                padding: 18px;
            }

            .image-caption strong,
            .image-placeholder strong {
                display: block;
                margin-bottom: 8px;
            }

            .image-caption a {
                color: var(--brand);
                font-weight: 700;
            }

            .image-placeholder {
                color: var(--muted);
                line-height: 1.65;
            }

            .price-card {
                margin-top: 22px;
                padding: 18px;
                border: 1px solid var(--line);
                border-radius: 18px;
                background: var(--surface-soft);
            }

            .price-card strong {
                display: block;
                margin-bottom: 8px;
                font-size: 0.82rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--muted);
            }

            .price-main {
                display: block;
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: -0.04em;
            }

            .price-unit {
                display: block;
                margin-top: 6px;
                color: var(--muted);
                font-size: 0.92rem;
            }

            .cta-row {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-top: 22px;
            }

            .meta-grid {
                display: grid;
                gap: 12px;
            }

            .meta-card {
                padding: 16px;
                border: 1px solid var(--line);
                border-radius: 18px;
                background: var(--surface-soft);
            }

            .meta-card strong {
                display: block;
                margin-bottom: 6px;
                font-size: 0.82rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--muted);
            }

            .meta-card span {
                line-height: 1.6;
            }

            @media (max-width: 780px) {
                .topbar-inner,
                .topbar-actions,
                .product-layout {
                    grid-template-columns: 1fr;
                    flex-direction: column;
                    align-items: stretch;
                }
            }
        </style>
    </head>
    <body>
        <header class="topbar">
            <div class="shell topbar-inner">
                <a class="top-link" href="{{ route('catalog.index') }}">Back to catalog</a>

                <div class="topbar-actions">
                    <a class="top-link" href="{{ route('dashboard') }}">Dashboard</a>

                    <form class="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Log out</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="shell">
            <section class="product-layout">
                <article class="panel hero-panel">
                    <span class="eyebrow">{{ $product->category }}</span>
                    <h1>{{ $product->name }}</h1>
                    <p class="lead">{{ $product->description }}</p>

                    <div class="image-card">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">

                            <div class="image-caption">
                                <strong>Product image</strong>
                                <a href="{{ $product->image_url }}" target="_blank" rel="noreferrer">Open image in a new tab</a>
                            </div>
                        @else
                            <div class="image-placeholder">
                                <strong>No image available</strong>
                                This product is part of the imported catalog data, but no product image URL is currently stored for it.
                            </div>
                        @endif
                    </div>

                    @if ($product->price_display || $product->unit_price_display)
                        <div class="price-card">
                            <strong>Price</strong>

                            @if ($product->price_display)
                                <span class="price-main">{{ $product->price_display }}</span>
                            @endif

                            @if ($product->unit_price_display)
                                <span class="price-unit">{{ $product->unit_price_display }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="cta-row">
                        <form class="cart-form" method="POST" action="{{ route('cart.store', $product) }}">
                            @csrf
                            <button class="primary-button" type="submit">Add to cart</button>
                        </form>

                        <a class="top-link" href="{{ route('cart.index') }}">View cart</a>
                    </div>
                </article>

                <aside class="panel meta-panel">
                    <div class="meta-grid">
                        <div class="meta-card">
                            <strong>Brand</strong>
                            <span>{{ $product->brand }}</span>
                        </div>

                        <div class="meta-card">
                            <strong>Category path</strong>
                            <span>{{ $product->category }} / {{ $product->subcategory }}</span>
                        </div>

                        <div class="meta-card">
                            <strong>SKU</strong>
                            <span>{{ $product->sku }}</span>
                        </div>

                        <div class="meta-card">
                            <strong>Barcode</strong>
                            <span>{{ $product->barcode ?: 'Not specified' }}</span>
                        </div>

                        <div class="meta-card">
                            <strong>Unit type</strong>
                            <span>{{ $product->unit_type }}</span>
                        </div>

                        <div class="meta-card">
                            <strong>Pack size</strong>
                            <span>{{ $product->pack_size ?: 'Not specified' }}</span>
                        </div>

                        <div class="meta-card">
                            <strong>Weight</strong>
                            <span>
                                {{ $product->weight_value !== null ? trim(number_format((float) $product->weight_value, 2) . ' ' . ($product->weight_unit ?: '')) : 'Not specified' }}
                            </span>
                        </div>
                    </div>
                </aside>
            </section>
        </main>
    </body>
</html>
