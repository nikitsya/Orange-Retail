<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Product Catalog</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f2f5ef;
                --surface: #ffffff;
                --surface-soft: #f7f9f4;
                --ink: #182119;
                --muted: #667062;
                --line: #d8e0d4;
                --brand: #2d6b45;
                --brand-strong: #1f5234;
                --shadow: 0 16px 38px rgba(18, 31, 20, 0.08);
                --radius: 22px;
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

            .topbar {
                position: sticky;
                top: 0;
                z-index: 20;
                background: rgba(242, 245, 239, 0.94);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(24, 33, 25, 0.08);
            }

            .shell {
                width: min(calc(100% - 24px), 1160px);
                margin: 0 auto;
            }

            .topbar-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                min-height: 74px;
            }

            .brand-block strong {
                display: block;
                font-size: 1rem;
            }

            .brand-block span {
                color: var(--muted);
                font-size: 0.88rem;
            }

            .topbar-actions {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .top-link,
            .logout-button,
            .search-button {
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
            .search-button {
                border-color: transparent;
                background: linear-gradient(135deg, var(--brand), var(--brand-strong));
                color: #fff;
            }

            .logout-form {
                margin: 0;
            }

            main.shell {
                padding: 28px 0 40px;
            }

            .hero,
            .search-panel,
            .empty-state,
            .product-card {
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            .hero {
                padding: 26px;
                margin-bottom: 20px;
            }

            .hero h1 {
                margin: 0;
                font-size: clamp(2rem, 4vw, 3.2rem);
                letter-spacing: -0.04em;
            }

            .hero p {
                max-width: 680px;
                margin: 14px 0 0;
                color: var(--muted);
                line-height: 1.7;
            }

            .search-panel {
                padding: 18px;
                margin-bottom: 20px;
            }

            .search-form {
                display: flex;
                gap: 12px;
            }

            .search-form input {
                flex: 1 1 auto;
                min-width: 0;
                padding: 0.9rem 1rem;
                border: 1px solid var(--line);
                border-radius: 14px;
                font: inherit;
                background: var(--surface-soft);
                color: var(--ink);
            }

            .search-note {
                margin: 0 0 18px;
                color: var(--muted);
                font-size: 0.92rem;
            }

            .product-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }

            .product-card {
                padding: 20px;
            }

            .eyebrow {
                display: inline-flex;
                padding: 0.36rem 0.62rem;
                border-radius: 999px;
                background: #edf5ee;
                color: var(--brand-strong);
                font-size: 0.76rem;
                font-weight: 700;
                letter-spacing: 0.03em;
                text-transform: uppercase;
            }

            .product-card h2 {
                margin: 14px 0 10px;
                font-size: 1.16rem;
                line-height: 1.3;
            }

            .product-card p {
                margin: 0;
                color: var(--muted);
                line-height: 1.65;
            }

            .product-meta {
                display: grid;
                gap: 10px;
                margin-top: 18px;
                padding-top: 16px;
                border-top: 1px solid var(--line);
                font-size: 0.92rem;
            }

            .product-meta strong {
                color: var(--ink);
            }

            .empty-state {
                padding: 28px;
                text-align: center;
                color: var(--muted);
            }

            @media (max-width: 900px) {
                .product-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 640px) {
                .topbar-inner,
                .topbar-actions,
                .search-form {
                    flex-direction: column;
                    align-items: stretch;
                }

                .product-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <header class="topbar">
            <div class="shell topbar-inner">
                <div class="brand-block">
                    <strong>Supermarket Catalog</strong>
                    <span>Customer browsing area for the current product range</span>
                </div>

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
            <section class="hero">
                <h1>Browse supermarket products</h1>
                <p>
                    This catalog lets regular users explore the available product data without entering the
                    admin management area. It is the start of the customer-facing side of the platform.
                </p>
            </section>

            <section class="search-panel">
                <form class="search-form" method="GET" action="{{ route('catalog.index') }}">
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search by name, description, brand, or category"
                    >
                    <button class="search-button" type="submit">Search</button>
                </form>
            </section>

            @if ($search !== '')
                <p class="search-note">Showing catalog results for "{{ $search }}".</p>
            @endif

            @if ($products->isEmpty())
                <section class="empty-state">
                    {{ $search !== '' ? 'No products matched your search.' : 'No products are available in the catalog yet.' }}
                </section>
            @else
                <section class="product-grid">
                    @foreach ($products as $product)
                        <article class="product-card">
                            <span class="eyebrow">{{ $product->category }}</span>
                            <h2>{{ $product->name }}</h2>
                            <p>{{ $product->description }}</p>

                            <div class="product-meta">
                                <div><strong>Brand:</strong> {{ $product->brand }}</div>
                                <div><strong>Subcategory:</strong> {{ $product->subcategory }}</div>
                                <div><strong>Unit:</strong> {{ $product->unit_type }}</div>
                                <div><strong>Pack size:</strong> {{ $product->pack_size ?: 'Not specified' }}</div>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endif
        </main>
    </body>
</html>
