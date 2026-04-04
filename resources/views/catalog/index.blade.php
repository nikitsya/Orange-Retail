<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Orange Retail | Catalog</title>
        <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
    </head>
    <body>
        @php
            $navCategories = $categories->take(8);
            $productCount = $products->count();
            $departmentImages = [
                '' => 'all_dep.png',
                'All departments' => 'all_dep.png',
                'Baby & Toddler' => 'baby_toddler.png',
                'Drinks' => 'drinks.png',
                'Food Cupboard' => 'food_cupb.png',
                'Fresh Food' => 'fresh_food.png',
                'Health & Beauty' => 'health_beauty.png',
                'Home & Furniture' => 'home_furniture.png',
                'Household' => 'household.png',
            ];
        @endphp

        <div class="utility-bar">
            <div class="page-shell utility-bar-inner">
                <div class="utility-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('catalog.index') }}">Catalog</a>
                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('products.index') }}">Inventory</a>
                        @else
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                            <a href="{{ route('cart.index') }}">Cart</a>
                        @endif
                    @else
                        <a href="{{ route('register') }}">Create account</a>
                    @endauth
                </div>

                <div class="utility-actions">
                    @auth
                        <span>{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Sign out</button>
                        </form>
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
                        <span class="brand-tag">Orange Retail</span>
                        <span class="brand-title">Orange Retail</span>
                        <span class="brand-subtitle">Browse products, search by department, and open product details.</span>
                    </a>

                    <form class="search-shell" method="GET" action="{{ route('catalog.index') }}">
                        @if ($category !== '')
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif

                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search groceries, brands, or categories"
                            aria-label="Search catalog"
                        >
                        <button type="submit">Search</button>
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
                            @else
                                <a class="account-pill" href="{{ route('cart.index') }}">
                                    <div>
                                        <strong>{{ $productCount }} products</strong>
                                        <span>Open cart</span>
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

        <div class="catalog-nav-shell">
            <div class="page-shell">
                <nav class="section-nav" aria-label="Catalog categories">
                    <a
                        class="nav-chip @if ($category === '') is-active @endif"
                        href="{{ route('catalog.index', ['search' => $search]) }}"
                        aria-label="All departments"
                    >
                        <span class="nav-chip-media">
                            <img src="{{ asset('images/departments/' . $departmentImages['']) }}" alt="All departments">
                        </span>
                        <span class="sr-only">All departments</span>
                    </a>

                    @foreach ($navCategories as $catalogCategory)
                        <a
                            class="nav-chip @if ($category === $catalogCategory) is-active @endif"
                            href="{{ route('catalog.index', array_filter(['category' => $catalogCategory, 'search' => $search !== '' ? $search : null])) }}"
                            aria-label="{{ $catalogCategory }}"
                        >
                            <span class="nav-chip-media">
                                @if (isset($departmentImages[$catalogCategory]))
                                    <img
                                        src="{{ asset('images/departments/' . $departmentImages[$catalogCategory]) }}"
                                        alt="{{ $catalogCategory }}"
                                    >
                                @else
                                    <span class="nav-chip-fallback">{{ strtoupper(substr($catalogCategory, 0, 1)) }}</span>
                                @endif
                            </span>
                            <span class="sr-only">{{ $catalogCategory }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <main class="page-shell page-main stack">
            <h1 class="sr-only">Browse supermarket products</h1>

            <section class="section-panel">
                <div>
                    <span class="section-kicker">Filters</span>
                    <h2 class="section-heading">Search and refine</h2>
                </div>

                <form class="filter-grid" method="GET" action="{{ route('catalog.index') }}">
                    <select class="field-select" name="category" aria-label="Category filter">
                        <option value="">All categories</option>
                        @foreach ($categories as $catalogCategory)
                            <option value="{{ $catalogCategory }}" @selected($category === $catalogCategory)>
                                {{ $catalogCategory }}
                            </option>
                        @endforeach
                    </select>

                    <input
                        class="field"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search by name, description, brand, or category"
                    >

                    <div class="toolbar">
                        <button class="button-primary" type="submit">Apply filters</button>
                        @if ($search !== '' || $category !== '')
                            <a class="button-secondary" href="{{ route('catalog.index') }}">Clear</a>
                        @endif
                    </div>
                </form>

                @if ($search !== '' || $category !== '')
                    <div class="filter-notes">
                        @if ($search !== '')
                            <span class="filter-note">Search: {{ $search }}</span>
                        @endif

                        @if ($category !== '')
                            <span class="filter-note">Department: {{ $category }}</span>
                        @endif
                    </div>
                @endif
            </section>

            @if ($products->isEmpty())
                <section class="empty-panel">
                    <h2 class="section-heading">No products matched the current selection.</h2>
                    <p class="muted-copy">
                        {{ $search !== '' || $category !== '' ? 'Try clearing one filter or choosing a different department.' : 'The catalog is currently empty.' }}
                    </p>
                </section>
            @else
                <section class="catalog-grid">
                    @foreach ($products as $product)
                        <article class="product-card">
                            <a class="product-media" href="{{ route('catalog.show', $product) }}">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                @else
                                    <span class="placeholder-badge">{{ strtoupper(substr($product->category, 0, 1)) }}</span>
                                @endif
                            </a>

                            <span class="eyebrow-tag">{{ $product->category }}</span>
                            <h3>
                                <a href="{{ route('catalog.show', $product) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="product-meta">{{ $product->brand }} | {{ $product->subcategory }}</div>
                            <p class="muted-copy">{{ $product->description }}</p>

                            <div class="price-row">
                                <div class="price-block">
                                    @if ($product->price_display)
                                        <strong>{{ $product->price_display }}</strong>
                                    @else
                                        <strong>In store</strong>
                                    @endif

                                    @if ($product->unit_price_display)
                                        <span>{{ $product->unit_price_display }}</span>
                                    @else
                                        <span>{{ $product->unit_type }}{{ $product->pack_size ? ' | ' . $product->pack_size : '' }}</span>
                                    @endif
                                </div>

                                <div class="tile-actions">
                                    <a class="button-secondary" href="{{ route('catalog.show', $product) }}">View</a>

                                    @auth
                                        @if (auth()->user()->role !== 'admin')
                                            <form method="POST" action="{{ route('cart.store', $product) }}">
                                                @csrf
                                                <button class="button-primary" type="submit">Add to cart</button>
                                            </form>
                                        @endif
                                    @else
                                        <a class="button-primary" href="{{ route('login') }}">Login to add</a>
                                    @endauth
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endif
        </main>
    </body>
</html>
