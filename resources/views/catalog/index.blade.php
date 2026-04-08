<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Catalog</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body>
@php
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
        'Pets' => 'pets.png',
        'Treats & Snacks' => 'treats_snacks.png',
    ];
    $fallbackProductImage = asset('images/products/picture.png');
    $navCategories = collect([
        'Fresh Food',
        'Drinks',
        'Food Cupboard',
        'Treats & Snacks',
        'Household',
        'Pets',
        'Health & Beauty',
        'Baby & Toddler',
        'Home & Furniture',
    ])->filter(fn (string $catalogCategory) => $categories->contains($catalogCategory));
    $productCount = $products->total();
    $currentPage = $products->currentPage();
    $lastPage = $products->lastPage();
    $windowSize = 5;
    $halfWindow = intdiv($windowSize, 2);
    $pageStart = max(1, $currentPage - $halfWindow);
    $pageEnd = min($lastPage, $pageStart + $windowSize - 1);
    $pageStart = max(1, $pageEnd - $windowSize + 1);
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
                @if ($category !== '')
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                @if ($subcategory !== '')
                    <input type="hidden" name="subcategory" value="{{ $subcategory }}">
                @endif

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search groceries or brands"
                    aria-label="Search catalog"
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

<div class="catalog-nav-shell">
    <div class="page-shell">
        <div class="catalog-nav-scroll">
            <nav class="section-nav" aria-label="Catalog categories">
                <a
                    class="nav-chip @if ($category === '') is-active @endif"
                    href="{{ route('catalog.index', ['search' => $search]) }}"
                    aria-label="All departments"
                >
                            <span class="nav-chip-media">
                                <img src="{{ asset('images/departments/' . $departmentImages['']) }}"
                                     alt="All departments">
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
                                        <span
                                            class="nav-chip-fallback">{{ strtoupper(substr($catalogCategory, 0, 1)) }}</span>
                                    @endif
                                </span>
                        <span class="sr-only">{{ $catalogCategory }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        @if ($category !== '' && $subcategoryOptions->isNotEmpty())
            <div class="subcategory-filter-shell">
                <div class="subcategory-filter-row" role="navigation" aria-label="{{ $category }} subcategories">
                    <a
                        class="subcategory-chip @if ($subcategory === '') is-active @endif"
                        href="{{ route('catalog.index', array_filter(['category' => $category, 'search' => $search !== '' ? $search : null])) }}"
                    >
                        All {{ $category }}
                    </a>

                    @foreach ($subcategoryOptions as $subcategoryOption)
                        <a
                            class="subcategory-chip @if ($subcategory === $subcategoryOption) is-active @endif"
                            href="{{ route('catalog.index', array_filter(['category' => $category, 'subcategory' => $subcategoryOption, 'search' => $search !== '' ? $search : null])) }}"
                        >
                            {{ $subcategoryOption }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<section class="catalog-content">
    <main class="page-shell page-main stack">
        <h1 class="sr-only">Browse supermarket products</h1>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        @if ($products->isEmpty())
            <section class="empty-panel">
                <h2 class="section-heading">No products matched the current selection.</h2>
                <p class="muted-copy">
                    {{ $search !== '' || $category !== '' || $subcategory !== '' ? 'Try clearing one filter or choosing a different department or subcategory.' : 'The catalog is currently empty.' }}
                </p>
            </section>
        @else
            <section class="catalog-grid">
                @foreach ($products as $product)
                    <article class="product-card" id="product-card-{{ $product->id }}">
                        <div class="product-media @if (! $product->image_url) has-fallback-image @endif">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            @else
                                <img src="{{ $fallbackProductImage }}" alt="{{ $product->name }}">
                            @endif
                        </div>

                        <h3>
                            <a class="product-title-link"
                               href="{{ route('catalog.show', $product) }}">{{ $product->name }}</a>
                        </h3>
                        <div class="product-meta">{{ $product->brand }} | {{ $product->subcategory }}</div>
                        <p class="muted-copy">{{ $product->summary_text }}</p>

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
                                @auth
                                    @if (auth()->user()->role !== 'admin')
                                        @if ($product->stock > 0)
                                            <form method="POST" action="{{ route('cart.store', $product) }}">
                                                @csrf
                                                <input type="hidden" name="redirect_to" value="{{ request()->getRequestUri() }}#product-card-{{ $product->id }}">
                                                <button class="button-primary" type="submit">Add to cart</button>
                                            </form>
                                        @else
                                            <span class="button-secondary" aria-disabled="true">Out of stock</span>
                                        @endif
                                    @endif
                                @else
                                    <a class="button-primary" href="{{ route('login') }}">Add</a>
                                @endauth
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            @if ($products->hasPages())
                <nav class="pagination-nav" aria-label="Catalog pages">
                    @if ($products->onFirstPage())
                        <span class="pagination-link is-disabled" aria-hidden="true">Previous</span>
                    @else
                        <a class="pagination-link" href="{{ $products->previousPageUrl() }}">Previous</a>
                    @endif

                    <div class="pagination-pages">
                        @for ($page = $pageStart; $page <= $pageEnd; $page++)
                            <a
                                class="pagination-link @if ($page === $products->currentPage()) is-active @endif"
                                href="{{ $products->url($page) }}"
                                aria-label="Page {{ $page }}"
                                @if ($page === $products->currentPage()) aria-current="page" @endif
                            >
                                {{ $page }}
                            </a>
                        @endfor
                    </div>

                    @if ($products->hasMorePages())
                        <a class="pagination-link" href="{{ $products->nextPageUrl() }}">Next</a>
                    @else
                        <span class="pagination-link is-disabled" aria-hidden="true">Next</span>
                    @endif
                </nav>
            @endif
        @endif
    </main>
</section>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
