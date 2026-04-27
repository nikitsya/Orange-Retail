<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Payment</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
    <script src="https://js.stripe.com/clover/stripe.js"></script>
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
                    aria-label="Search catalogue"
                >
                <span class="search-icon" aria-hidden="true"><img src="{{ asset('images/ui/search.png') }}" alt=""></span>
            </form>

            <div class="masthead-actions">
                <a class="button-secondary" href="{{ route('orders.show', $order) }}">Order details</a>
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
        @if (session('status'))
            <div class="flash-message">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <div>
            <h1 class="page-title">Payment</h1>
            <p class="muted-copy">Complete payment for {{ $order->order_number }}.</p>
        </div>

        <form class="stack stripe-payment-form" id="stripe-payment-form">
            <div id="payment-element" class="stripe-payment-element"></div>
            <div id="payment-message" class="error-message" hidden></div>
            <button class="button-primary" id="stripe-submit-button" type="submit" disabled>
                <span id="stripe-button-text">Pay €{{ number_format((float) $order->total, 2) }}</span>
            </button>
        </form>
    </section>

    <aside class="summary-panel stack">
        <div>
            <h2>{{ $order->item_count }} item{{ $order->item_count === 1 ? '' : 's' }}</h2>
            <p>Reserved for Stripe payment.</p>
        </div>

        <section class="mini-list">
            @foreach ($order->items as $item)
                <article class="mini-list-item">
                    <strong>{{ $item->product_name }}</strong>
                    <span>{{ $item->quantity }} x €{{ number_format((float) $item->unit_price, 2) }}</span>
                    <span>€{{ number_format((float) $item->line_total, 2) }}</span>
                </article>
            @endforeach
        </section>

        <div class="summary-stat">
            <strong>€{{ number_format((float) $order->total, 2) }}</strong>
            <span>Order total</span>
        </div>
    </aside>
</main>

@include('partials.masthead-stick-on-scroll')

<script>
    const stripePublishableKey = @json($stripePublishableKey);
    const stripeClientSecret = @json($stripeClientSecret);
    const paymentForm = document.getElementById('stripe-payment-form');
    const submitButton = document.getElementById('stripe-submit-button');
    const buttonText = document.getElementById('stripe-button-text');
    const messageBox = document.getElementById('payment-message');

    let checkoutActions = null;

    function showPaymentMessage(message) {
        messageBox.textContent = message;
        messageBox.hidden = false;
    }

    function setPaymentLoading(isLoading) {
        submitButton.disabled = isLoading || !checkoutActions;
        buttonText.textContent = isLoading ? 'Processing payment...' : 'Pay €{{ number_format((float) $order->total, 2) }}';
    }

    async function initialiseStripePayment() {
        try {
            const stripe = Stripe(stripePublishableKey);
            const checkout = stripe.initCheckoutElementsSdk({
                clientSecret: stripeClientSecret,
                elementsOptions: {
                    appearance: {
                        theme: 'stripe',
                        variables: {
                            colorPrimary: '#eb6d1f',
                            colorText: '#241714',
                            borderRadius: '10px',
                        },
                    },
                },
            });

            const actionsResult = await checkout.loadActions();

            if (actionsResult.type !== 'success') {
                showPaymentMessage(actionsResult.error?.message || 'Stripe payment could not be loaded.');
                return;
            }

            checkoutActions = actionsResult.actions;
            checkout.createPaymentElement().mount('#payment-element');
            submitButton.disabled = false;
        } catch (error) {
            showPaymentMessage(error.message || 'Stripe payment could not be loaded.');
        }
    }

    paymentForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        messageBox.hidden = true;

        if (!checkoutActions) {
            showPaymentMessage('Stripe payment is still loading.');
            return;
        }

        setPaymentLoading(true);

        const { error } = await checkoutActions.confirm();

        if (error) {
            showPaymentMessage(error.message);
            setPaymentLoading(false);
        }
    });

    initialiseStripePayment();
</script>
</body>
</html>
