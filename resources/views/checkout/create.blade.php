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
        <div class="utility-bar">
            <div class="page-shell utility-bar-inner">
                @include('partials.app-nav')

                <div class="utility-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="utility-button" type="submit">Sign out</button>
                    </form>
                </div>
            </div>
        </div>

        <main class="page-shell page-main checkout-layout">
            <section class="section-panel stack">
                <div>
                    <span class="section-kicker">Checkout</span>
                    <h1 class="page-title">Review delivery details</h1>
                    <p class="muted-copy">Checkout creates an order snapshot and reserves the requested stock immediately.</p>
                </div>

                @if ($errors->any())
                    <div class="error-message">{{ $errors->first() }}</div>
                @endif

                <form class="stack" method="POST" action="{{ route('checkout.store') }}">
                    @csrf

                    <div class="form-grid-2">
                        <label class="field-label">
                            Full name
                            <input class="field" type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required>
                        </label>

                        <label class="field-label">
                            Email
                            <input class="field" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email) }}" required>
                        </label>
                    </div>

                    <label class="field-label">
                        Address line 1
                        <input class="field" type="text" name="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}" required>
                    </label>

                    <label class="field-label">
                        Address line 2
                        <input class="field" type="text" name="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}">
                    </label>

                    <div class="form-grid-3">
                        <label class="field-label">
                            City
                            <input class="field" type="text" name="shipping_city" value="{{ old('shipping_city') }}" required>
                        </label>

                        <label class="field-label">
                            County
                            <input class="field" type="text" name="shipping_county" value="{{ old('shipping_county') }}">
                        </label>

                        <label class="field-label">
                            Postal code
                            <input class="field" type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required>
                        </label>
                    </div>

                    <label class="field-label">
                        Delivery notes
                        <textarea class="field-area" name="notes">{{ old('notes') }}</textarea>
                    </label>

                    <div class="tile-actions">
                        <button class="button-primary" type="submit">Place order</button>
                        <a class="button-secondary" href="{{ route('cart.index') }}">Back to cart</a>
                    </div>
                </form>
            </section>

            <aside class="summary-panel stack">
                <div>
                    <span class="section-kicker">Order summary</span>
                    <h2>{{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}</h2>
                    <p>Review the exact products and quantities that will be captured in the order snapshot.</p>
                </div>

                <section class="mini-list">
                    @foreach ($items as $item)
                        <article class="mini-list-item">
                            <strong>{{ $item['product']->name }}</strong>
                            <span>{{ $item['quantity'] }} x {{ $item['product']->price_display ?: 'In store' }}</span>
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
    </body>
</html>
