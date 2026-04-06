<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Order Queue</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
</head>
<body class="admin-orders-page">
<div class="utility-bar">
    <div class="page-shell utility-bar-inner">
        @include('partials.app-nav')

        @include('partials.utility-actions')
    </div>
</div>

<main class="page-shell page-main stack">
    @if (session('status'))
        <div class="flash-message">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
    @endif

    <section class="section-panel stack">
        <div class="section-actions" style="justify-content: space-between;">
            <div>
                <h1 class="page-title">Order queue</h1>
            </div>
        </div>

        <form class="form-grid-3" method="GET" action="{{ route('admin.orders.index') }}">
            <label class="field-label">
                Search
                <input class="field" type="search" name="search" value="{{ $search }}"
                       placeholder="Order number, customer, email">
            </label>

            <label class="field-label">
                Status
                <select class="field-select" name="status">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $orderStatus)
                        <option
                            value="{{ $orderStatus }}" @selected($orderStatus === $status)>{{ ucfirst($orderStatus) }}</option>
                    @endforeach
                </select>
            </label>

            <div class="tile-actions">
                <button class="button-primary" type="submit">Apply filters</button>
            </div>
        </form>

        <section class="order-list">
            @forelse ($orders as $order)
                <article class="summary-panel order-card">
                    <div class="order-card-head">
                        <div>
                            <span class="inventory-tag">{{ strtoupper($order->status) }}</span>
                            <h2>{{ $order->order_number }}</h2>
                            <p>{{ $order->customer_name }} | {{ $order->customer_email }}
                                | {{ $order->placed_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div class="order-card-total">€{{ number_format((float) $order->total, 2) }}</div>
                    </div>

                    <div class="tile-actions order-card-actions">
                        <form class="inline-form" method="POST" action="{{ route('admin.orders.update', $order) }}">
                            @csrf
                            @method('PATCH')
                            <select class="field-select" name="status">
                                @foreach ($statuses as $orderStatus)
                                    <option
                                        value="{{ $orderStatus }}" @selected($orderStatus === $order->status)>{{ ucfirst($orderStatus) }}</option>
                                @endforeach
                            </select>
                            <button class="button-primary" type="submit">Update</button>
                        </form>

                        <a class="button-secondary" href="{{ route('orders.show', $order) }}">Open details</a>
                    </div>
                </article>
            @empty
                <section class="empty-panel">
                    <h2 class="section-heading">No orders found</h2>
                    <p class="muted-copy">Try a different filter or wait for the first checkout to arrive.</p>
                </section>
            @endforelse
        </section>

        {{ $orders->links() }}
    </section>
</main>
</body>
</html>
