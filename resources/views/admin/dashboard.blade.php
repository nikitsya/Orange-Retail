<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orange Retail | Admin Dashboard</title>
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

            <form class="search-shell" method="GET" action="{{ route('admin.orders.index') }}" data-live-search>
                <input
                    type="search"
                    name="search"
                    placeholder="Search orders"
                    aria-label="Search orders"
                >
                <span class="search-icon" aria-hidden="true"><img src="{{ asset('images/ui/search.png') }}" alt=""></span>
            </form>

            <div class="masthead-actions">
                <a class="account-pill" href="{{ route('admin.orders.index') }}">
                    <div>
                        <strong>Order Queue</strong>
                        <span>View all orders</span>
                    </div>
                </a>
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

<main class="page-shell page-main stack admin-dashboard-page">
    @if (session('status'))
        <div class="flash-message">{{ session('status') }}</div>
    @endif

    <section class="hero-panel">
        <div class="hero-copy">
            <h1>Admin Dashboard</h1>
            <p>Monitor live inventory, pending orders, inactive products, and upcoming delivery dates from one control
                surface.</p>
        </div>

        <div class="summary-stats summary-stats-wide">
            <div class="summary-stat">
                <strong>{{ $productCount }}</strong>
                <span>Total products</span>
            </div>
            <button class="summary-stat summary-stat-button @if ($selectedPanel === 'pending-orders') is-active @endif" type="button" data-dashboard-panel-trigger="pending-orders">
                <strong>{{ $pendingOrders }}</strong>
                <span>Orders waiting for action</span>
            </button>
            <button class="summary-stat summary-stat-button summary-stat-button-warning @if ($selectedPanel === 'low-stock') is-active @endif" type="button" data-dashboard-panel-trigger="low-stock">
                <strong>{{ $lowStockProducts }}</strong>
                <span>Low-stock SKUs</span>
            </button>
            <button class="summary-stat summary-stat-button summary-stat-button-danger @if ($selectedPanel === 'inactive-products') is-active @endif" type="button" data-dashboard-panel-trigger="inactive-products">
                <strong>{{ $inactiveProducts }}</strong>
                <span>Inactive products</span>
            </button>
        </div>
    </section>

    <section class="dashboard-layout">
        <section class="section-panel stack" data-dashboard-panels>
            <div class="section-actions" style="justify-content: space-between;">
                <div class="dashboard-panel-head @if ($selectedPanel === 'pending-orders') is-active @endif" data-dashboard-panel-head="pending-orders" @if ($selectedPanel !== 'pending-orders') hidden @endif>
                    <h2>Orders waiting for action</h2>
                    <p class="muted-copy">New pending orders that still need admin attention.</p>
                </div>
                <div class="dashboard-panel-head @if ($selectedPanel === 'low-stock') is-active @endif" data-dashboard-panel-head="low-stock" @if ($selectedPanel !== 'low-stock') hidden @endif>
                    <h2>Low-stock SKUs</h2>
                    <p class="muted-copy">Products that have reached or dropped below the saved minimum stock level.</p>
                </div>
                <div class="dashboard-panel-head @if ($selectedPanel === 'inactive-products') is-active @endif" data-dashboard-panel-head="inactive-products" @if ($selectedPanel !== 'inactive-products') hidden @endif>
                    <h2>Inactive products</h2>
                    <p class="muted-copy">Products that are hidden from the customer catalog right now.</p>
                </div>

                <a class="button-secondary dashboard-panel-action @if ($selectedPanel === 'pending-orders') is-active @endif" href="{{ route('admin.orders.index') }}" data-dashboard-panel-action="pending-orders" @if ($selectedPanel !== 'pending-orders') hidden @endif>
                    Open full order queue
                </a>
                <a class="button-secondary dashboard-panel-action @if ($selectedPanel === 'low-stock') is-active @endif" href="{{ route('products.index') }}" data-dashboard-panel-action="low-stock" @if ($selectedPanel !== 'low-stock') hidden @endif>
                    Open inventory
                </a>
                <a class="button-secondary dashboard-panel-action @if ($selectedPanel === 'inactive-products') is-active @endif" href="{{ route('products.index') }}" data-dashboard-panel-action="inactive-products" @if ($selectedPanel !== 'inactive-products') hidden @endif>
                    Open inventory
                </a>
            </div>

            <section class="order-list dashboard-panel-content @if ($selectedPanel === 'pending-orders') is-active @endif" data-dashboard-panel-content="pending-orders" @if ($selectedPanel !== 'pending-orders') hidden @endif>
                @forelse ($pendingOrderItems as $order)
                    <article class="summary-panel order-card">
                        <div class="order-card-head">
                            <div>
                                <span class="inventory-tag">{{ strtoupper($order->status) }}</span>
                                <h2>{{ $order->order_number }}</h2>
                                <p>{{ $order->customer_name }} | {{ $order->placed_at?->format('d M Y H:i') }}</p>
                            </div>
                            <div class="order-card-total">€{{ number_format((float) $order->total, 2) }}</div>
                        </div>
                        <div class="tile-actions">
                            <a class="button-primary" href="{{ route('orders.show', $order) }}">Open order</a>
                        </div>
                    </article>
                @empty
                    <section class="empty-panel">
                        <h2 class="section-heading">No orders waiting for action</h2>
                        <p class="muted-copy">New pending orders will appear here automatically.</p>
                    </section>
                @endforelse
            </section>

            <section class="mini-list dashboard-panel-content @if ($selectedPanel === 'low-stock') is-active @endif" data-dashboard-panel-content="low-stock" @if ($selectedPanel !== 'low-stock') hidden @endif>
                @forelse ($lowStockItems as $product)
                    <article class="mini-list-item is-warning">
                        <strong>{{ $product->name }}</strong>
                        <span>{{ $product->brand }} | {{ $product->subcategory }}</span>
                        <span>SKU: {{ $product->sku }} | Stock: {{ $product->stock }} | Min: {{ $product->minimum_stock_level }}</span>
                    </article>
                @empty
                    <section class="empty-panel">
                        <h2 class="section-heading">No low-stock SKUs</h2>
                        <p class="muted-copy">All tracked products are above their minimum stock level.</p>
                    </section>
                @endforelse
            </section>

            <section class="mini-list dashboard-panel-content @if ($selectedPanel === 'inactive-products') is-active @endif" data-dashboard-panel-content="inactive-products" @if ($selectedPanel !== 'inactive-products') hidden @endif>
                @forelse ($inactiveProductItems as $product)
                    <article class="mini-list-item is-danger dashboard-product-card">
                        <div class="dashboard-product-card-copy">
                            <strong>{{ $product->name }}</strong>
                            <span>{{ $product->brand }} | {{ $product->category }} | {{ $product->subcategory }}</span>
                            <span>SKU: {{ $product->sku }} | Pack: {{ $product->pack_size ?: ucfirst($product->unit_type) }}</span>
                        </div>

                        <form method="POST" action="{{ route('products.activate', $product) }}" data-activate-product-form>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="current_panel" value="inactive-products">
                            <button
                                class="button-primary dashboard-product-card-button"
                                type="button"
                                data-open-activate-modal
                                data-product-name="{{ $product->name }}"
                            >
                                Make active
                            </button>
                        </form>
                    </article>
                @empty
                    <section class="empty-panel">
                        <h2 class="section-heading">No inactive products</h2>
                        <p class="muted-copy">All products are currently visible in the customer catalog.</p>
                    </section>
                @endforelse
            </section>
        </section>

        <aside class="summary-panel stack">
            <div>
                <h2>Latest stock movements</h2>
                <p>Recent stock changes from manual restocks, order reservations, and cancellation returns.</p>
            </div>

            <section class="mini-list">
                @forelse ($recentMovements as $movement)
                    <article class="mini-list-item">
                        <strong>{{ $movement->product?->name ?? 'Removed product' }}</strong>
                        <span>{{ $movement->occurred_at?->format('d M Y H:i') }}</span>
                        <span>{{ strtoupper(str_replace('_', ' ', $movement->type)) }} | {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</span>
                    </article>
                @empty
                    <p class="muted-copy">No stock movements have been recorded yet.</p>
                @endforelse
            </section>

            <div class="tile-actions">
                <a class="button-primary" href="{{ route('admin.stock.index') }}">Open Stock Center</a>
            </div>
        </aside>
    </section>
</main>

<div class="modal" id="activate-product-modal" data-activate-modal aria-hidden="true">
    <div class="modal-dialog dashboard-confirm-dialog">
        <div class="modal-head dashboard-confirm-head">
            <div>
                <h2>Activate product</h2>
                <p class="muted-copy">This product will become visible in the customer catalog again.</p>
            </div>
        </div>

        <div class="stack">
            <p class="dashboard-confirm-copy">
                Are you sure you want to make
                <strong data-activate-product-name>this product</strong>
                active?
            </p>

            <div class="modal-form-actions">
                <button class="button-primary" type="button" data-confirm-activate-product>Yes, make active</button>
                <button class="button-secondary" type="button" data-close-activate-modal>Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    const dashboardPanelTriggers = document.querySelectorAll('[data-dashboard-panel-trigger]');
    const dashboardPanelHeads = document.querySelectorAll('[data-dashboard-panel-head]');
    const dashboardPanelActions = document.querySelectorAll('[data-dashboard-panel-action]');
    const dashboardPanelContents = document.querySelectorAll('[data-dashboard-panel-content]');
    const activateModal = document.querySelector('[data-activate-modal]');
    const activateModalProductName = document.querySelector('[data-activate-product-name]');
    const activateModalConfirmButton = document.querySelector('[data-confirm-activate-product]');
    let activateProductForm = null;

    const setDashboardPanel = (panelName) => {
        dashboardPanelTriggers.forEach((trigger) => {
            trigger.classList.toggle('is-active', trigger.dataset.dashboardPanelTrigger === panelName);
        });

        dashboardPanelHeads.forEach((head) => {
            const isActive = head.dataset.dashboardPanelHead === panelName;
            head.hidden = !isActive;
            head.classList.toggle('is-active', isActive);
        });

        dashboardPanelActions.forEach((action) => {
            const isActive = action.dataset.dashboardPanelAction === panelName;
            action.hidden = !isActive;
            action.classList.toggle('is-active', isActive);
        });

        dashboardPanelContents.forEach((content) => {
            const isActive = content.dataset.dashboardPanelContent === panelName;
            content.hidden = !isActive;
            content.classList.toggle('is-active', isActive);
        });
    };

    dashboardPanelTriggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            setDashboardPanel(trigger.dataset.dashboardPanelTrigger);
        });
    });

    const openActivateModal = (form, productName) => {
        if (!activateModal || !activateModalProductName) {
            return;
        }

        activateProductForm = form;
        activateModalProductName.textContent = productName;
        activateModal.classList.add('is-open');
        activateModal.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('modal-open');
        document.body.classList.add('modal-open');
    };

    const closeActivateModal = () => {
        if (!activateModal) {
            return;
        }

        activateProductForm = null;
        activateModal.classList.remove('is-open');
        activateModal.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('modal-open');
        document.body.classList.remove('modal-open');
    };

    document.querySelectorAll('[data-open-activate-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const form = button.closest('[data-activate-product-form]');

            if (!form) {
                return;
            }

            openActivateModal(form, button.dataset.productName || 'this product');
        });
    });

    document.querySelectorAll('[data-close-activate-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeActivateModal();
        });
    });

    if (activateModalConfirmButton) {
        activateModalConfirmButton.addEventListener('click', () => {
            if (!activateProductForm) {
                closeActivateModal();
                return;
            }

            activateProductForm.submit();
        });
    }

    if (activateModal) {
        activateModal.addEventListener('click', (event) => {
            if (event.target !== activateModal) {
                return;
            }

            closeActivateModal();
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !activateModal?.classList.contains('is-open')) {
            return;
        }

        closeActivateModal();
    });

    setDashboardPanel(@json($selectedPanel));
</script>

@include('partials.masthead-stick-on-scroll')
</body>
</html>
