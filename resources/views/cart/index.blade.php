<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shopping Cart</title>
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
                --danger: #b23f3f;
                --danger-soft: #f8e7e7;
                --success-soft: #e8f4eb;
                --success-ink: #1e6a39;
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
            .remove-button {
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

            .logout-button {
                border-color: transparent;
                background: linear-gradient(135deg, var(--brand), var(--brand-strong));
                color: #fff;
            }

            .remove-button {
                background: var(--danger-soft);
                color: var(--danger);
            }

            .logout-form,
            .remove-form {
                margin: 0;
            }

            main.shell {
                padding: 30px 0 42px;
            }

            .panel,
            .cart-item,
            .empty-state,
            .flash {
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            .hero,
            .summary,
            .cart-item {
                padding: 24px;
            }

            .flash {
                margin-bottom: 18px;
                padding: 14px 16px;
                background: var(--success-soft);
                color: var(--success-ink);
                font-size: 0.94rem;
            }

            .hero {
                margin-bottom: 20px;
            }

            .hero h1 {
                margin: 0;
                font-size: clamp(2rem, 4vw, 2.8rem);
                letter-spacing: -0.04em;
            }

            .hero p,
            .summary p,
            .empty-state p {
                color: var(--muted);
                line-height: 1.7;
            }

            .layout {
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
                gap: 20px;
            }

            .items {
                display: grid;
                gap: 16px;
            }

            .cart-item h2 {
                margin: 0 0 10px;
                font-size: 1.2rem;
            }

            .cart-item p {
                margin: 0;
                color: var(--muted);
                line-height: 1.65;
            }

            .item-meta {
                display: grid;
                gap: 8px;
                margin-top: 16px;
                font-size: 0.92rem;
            }

            .item-meta strong {
                color: var(--ink);
            }

            .item-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-top: 18px;
                padding-top: 16px;
                border-top: 1px solid var(--line);
            }

            .summary h2 {
                margin: 0 0 10px;
                font-size: 1.3rem;
            }

            .summary strong {
                display: block;
                margin-top: 16px;
                font-size: 2rem;
                letter-spacing: -0.04em;
            }

            .empty-state {
                padding: 28px;
                text-align: center;
            }

            @media (max-width: 780px) {
                .topbar-inner,
                .topbar-actions,
                .layout,
                .item-actions {
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
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif

            <section class="panel hero">
                <h1>Your cart</h1>
                <p>This is a session-based customer cart. It lets the user collect products before checkout is added.</p>
            </section>

            <section class="layout">
                <div>
                    @if ($items->isEmpty())
                        <section class="empty-state">
                            <h2>Your cart is empty</h2>
                            <p>Browse the catalog and add a product to start the customer shopping flow.</p>
                        </section>
                    @else
                        <section class="items">
                            @foreach ($items as $item)
                                <article class="cart-item">
                                    <h2>{{ $item['product']->name }}</h2>
                                    <p>{{ $item['product']->description }}</p>

                                    <div class="item-meta">
                                        <div><strong>Brand:</strong> {{ $item['product']->brand }}</div>
                                        <div><strong>Category:</strong> {{ $item['product']->category }} / {{ $item['product']->subcategory }}</div>
                                        <div><strong>Quantity:</strong> {{ $item['quantity'] }}</div>
                                    </div>

                                    <div class="item-actions">
                                        <a class="top-link" href="{{ route('catalog.show', $item['product']) }}">View product</a>

                                        <form class="remove-form" method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="remove-button" type="submit">Remove</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </section>
                    @endif
                </div>

                <aside class="panel summary">
                    <h2>Cart summary</h2>
                    <p>Total quantity of items currently stored in the session cart.</p>
                    <strong>{{ $itemCount }}</strong>
                </aside>
            </section>
        </main>
    </body>
</html>
