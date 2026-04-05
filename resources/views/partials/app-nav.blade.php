@php
    $isAuthenticated = auth()->check();
    $isAdmin = $isAuthenticated && auth()->user()->role === 'admin';

    $navLinks = match (true) {
        $isAdmin => [
            ['label' => 'Home', 'route' => route('home')],
            ['label' => 'Catalog', 'route' => route('catalog.index')],
            ['label' => 'Admin Dashboard', 'route' => route('admin.dashboard')],
            ['label' => 'Inventory', 'route' => route('products.index')],
            ['label' => 'Stock Center', 'route' => route('admin.stock.index')],
            ['label' => 'Orders', 'route' => route('admin.orders.index')],
        ],
        $isAuthenticated => [
            ['label' => 'Home', 'route' => route('home')],
            ['label' => 'Catalog', 'route' => route('catalog.index')],
            ['label' => 'Dashboard', 'route' => route('dashboard')],
            ['label' => 'Orders', 'route' => route('orders.index')],
            ['label' => 'Cart', 'route' => route('cart.index')],
        ],
        default => [
            ['label' => 'Home', 'route' => route('home')],
            ['label' => 'Catalog', 'route' => route('catalog.index')],
            ['label' => 'Login', 'route' => route('login')],
            ['label' => 'Register', 'route' => route('register')],
        ],
    };

    $currentUrl = url()->current();
@endphp

<div class="utility-links">
    @foreach ($navLinks as $link)
        <a
            href="{{ $link['route'] }}"
            class="@if ($currentUrl === $link['route']) is-active @endif"
        >
            {{ $link['label'] }}
        </a>
    @endforeach
</div>
