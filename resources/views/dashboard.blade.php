<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Orange Retail | Dashboard</title>
        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
    </head>
    <body>
        <div class="utility-bar">
            <div class="page-shell utility-bar-inner">
                <div class="utility-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('catalog.index') }}">Catalog</a>
                    <a href="{{ route('cart.index') }}">Cart</a>
                </div>

                <div class="utility-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Sign out</button>
                    </form>
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
                        <input type="search" name="search" placeholder="Search products" aria-label="Search products">
                        <button type="submit">Search</button>
                    </form>

                    <div class="masthead-actions">
                        <a class="account-pill" href="{{ route('cart.index') }}">
                            <div>
                                <strong>{{ auth()->user()->name }}</strong>
                                <span>Regular user</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-shell page-main stack">
            <section class="hero-grid">
                <article class="hero-panel">
                    <div class="hero-copy">
                        <span class="section-kicker">User Dashboard</span>
                        <h1>Hello {{ auth()->user()->name }}</h1>
                        <p>You are logged in as a regular user. This page is separate from the admin product management area.</p>
                        <p>From here, you can open the customer product catalog and browse the items available in the system.</p>
                    </div>

                    <div class="hero-actions">
                        <a class="button-primary" href="{{ route('catalog.index') }}">Browse Catalog</a>
                        <a class="button-secondary" href="{{ route('cart.index') }}">Open cart</a>
                    </div>

                    <div class="hero-notes">
                        <div class="hero-note">
                            <strong>Catalog</strong>
                            <span>Open the product list and use category filters.</span>
                        </div>
                        <div class="hero-note">
                            <strong>Cart</strong>
                            <span>Review the products stored in the current session.</span>
                        </div>
                        <div class="hero-note">
                            <strong>Account</strong>
                            <span>Your session is active.</span>
                        </div>
                    </div>
                </article>

                <aside class="promo-column">
                    <article class="promo-panel">
                        <span class="section-kicker">Catalog</span>
                        <h2>Open the customer catalog</h2>
                        <p class="section-copy">Search products, filter departments, and open product detail pages.</p>
                    </article>

                    <article class="promo-panel">
                        <span class="section-kicker">Cart</span>
                        <h2>Open the current cart</h2>
                        <p class="section-copy">Review saved items or remove products from the session cart.</p>
                    </article>
                </aside>
            </section>

            <section class="dashboard-layout">
                <div class="tile-grid">
                    <article class="tile-card">
                        <span class="section-kicker">Catalog</span>
                        <strong>Browse the live product range</strong>
                        <p class="tile-copy">Open the catalog, filter by category, and move through product detail pages.</p>
                        <div class="tile-actions">
                            <a class="button-primary" href="{{ route('catalog.index') }}">Open catalog</a>
                        </div>
                    </article>

                    <article class="tile-card">
                        <span class="section-kicker">Cart</span>
                        <strong>Keep your session cart in reach</strong>
                        <p class="tile-copy">Review selected products or remove them from the current session cart.</p>
                        <div class="tile-actions">
                            <a class="button-secondary" href="{{ route('cart.index') }}">Open cart</a>
                        </div>
                    </article>
                </div>

                <aside class="summary-panel">
                    <span class="section-kicker">Account status</span>
                    <h2>Account</h2>
                    <p>Use this page to move to the catalog or the cart.</p>

                    <div class="summary-stats">
                        <div class="summary-stat">
                            <strong>User</strong>
                            <span>Role</span>
                        </div>
                        <div class="summary-stat">
                            <strong>Active</strong>
                            <span>Session state</span>
                        </div>
                        <div class="summary-stat">
                            <strong>{{ auth()->user()->email }}</strong>
                            <span>Signed-in account</span>
                        </div>
                    </div>
                </aside>
            </section>
        </main>

        @include('partials.masthead-stick-on-scroll')
    </body>
</html>
