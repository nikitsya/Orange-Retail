<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Checkout</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body>
<header class="masthead">
    <div class="page-shell">
        <div class="masthead-main">
            <a class="brand-lockup" href="{{ route('catalog.index') }}">
                @include('partials.brand-name', ['class' => 'brand-title'])
            </a>

            <form class="search-shell" method="GET" action="{{ route('catalog.index') }}" data-live-search>
                <input
                    type="search"
                    name="search"
                    placeholder="Search groceries or brands"
                    aria-label="Search catalog"
                >
                <span class="search-icon" aria-hidden="true"><img src="{{ asset('images/ui/search.png') }}" alt=""></span>
            </form>

            <div class="masthead-actions">
                <a class="button-secondary" href="{{ route('cart.index') }}">Back to cart</a>
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

<main class="page-shell page-main checkout-layout">
    <section class="section-panel stack">
        <div>
            <h1 class="page-title">Review delivery details</h1>
            <p class="muted-copy">Review your delivery information before continuing to payment.</p>
        </div>

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form class="stack" method="POST" action="{{ route('checkout.store') }}">
            @csrf

            <div class="form-grid-2">
                <label class="field-label">
                    Full name
                    <input class="field" type="text" name="customer_name"
                           value="{{ old('customer_name', auth()->user()->name) }}"
                           placeholder="Enter the full name for delivery"
                           required>
                </label>

                <label class="field-label">
                    Email
                    <input class="field" type="email" name="customer_email"
                           value="{{ old('customer_email', auth()->user()->email) }}"
                           placeholder="Enter the email for order updates"
                           required>
                </label>
            </div>

            <label class="field-label">
                Address line 1
                <input class="field" type="text" name="shipping_address_line_1"
                       value="{{ old('shipping_address_line_1') }}"
                       placeholder="Street address, house number, or apartment"
                       required>
            </label>

            <label class="field-label">
                Address line 2 <span class="muted-copy">(optional)</span>
                <input class="field" type="text" name="shipping_address_line_2"
                       value="{{ old('shipping_address_line_2') }}"
                       placeholder="Apartment, suite, building, or floor">
            </label>

            <div class="form-grid-3">
                <label class="field-label">
                    City
                    <input class="field" type="text" name="shipping_city"
                           value="{{ old('shipping_city') }}"
                           placeholder="Enter the city"
                           required>
                </label>

                <label class="field-label">
                    <span style="white-space: nowrap;">County <span class="muted-copy">(optional)</span></span>
                    <input class="field" type="text" name="shipping_county"
                           value="{{ old('shipping_county') }}"
                           placeholder="Enter the county">
                </label>

                <label class="field-label">
                    Postal code
                    <input class="field" type="text" name="shipping_postal_code"
                           value="{{ old('shipping_postal_code') }}"
                           placeholder="Enter the postal code"
                           required>
                </label>
            </div>

            <label class="field-label">
                Delivery notes <span class="muted-copy">(optional)</span>
                <textarea class="field-area" name="notes" placeholder="Add any delivery instructions or notes for this order">{{ old('notes') }}</textarea>
            </label>

            <div class="tile-actions">
                <button class="button-primary" type="submit">Continue to payment</button>
                <a class="button-secondary" href="{{ route('cart.index') }}">Back to cart</a>
            </div>
        </form>
    </section>

    <aside class="summary-panel stack">
        <div>
            <h2>{{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}</h2>
            <p>Review the products and quantities included in this order.</p>
        </div>

        <section class="mini-list">
            @foreach ($items as $item)
                <article class="mini-list-item">
                    <strong>{{ $item['product']->name }}</strong>
                    <span>{{ $item['quantity'] }} x {{ $item['product']->formatted_price ?: 'In store' }}</span>
                    <span>€{{ number_format($item['line_total'], 2) }}</span>
                </article>
            @endforeach
        </section>

        <div class="summary-stat">
            <strong>€{{ number_format($subtotal, 2) }}</strong>
            <span>Order total</span>
        </div>
    </aside>
</main>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
