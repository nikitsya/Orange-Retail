<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\ManagerSessionController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{product}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [ManagerSessionController::class, 'create'])->name('login');
    Route::post('/login', [ManagerSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [ManagerSessionController::class, 'createRegistration'])->name('register');
    Route::post('/register', [ManagerSessionController::class, 'storeRegistration'])->name('register.store');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{product}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::get('/cart', [CatalogController::class, 'cart'])->name('cart.index');
    Route::post('/cart/{product}', [CatalogController::class, 'addToCart'])->name('cart.store');
    Route::put('/cart/{product}', [CatalogController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{product}', [CatalogController::class, 'removeFromCart'])->name('cart.destroy');
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/logout', [ManagerSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', EnsureAdmin::class])->group(function (): void {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
    Route::patch('/admin/orders/{order}', [OrderController::class, 'updateStatus'])->name('admin.orders.update');
    Route::get('/admin/stock', [StockController::class, 'index'])->name('admin.stock.index');
    Route::patch('/admin/stock/{product}', [StockController::class, 'update'])->name('admin.stock.update');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::patch('/products/{product}/activate', [ProductController::class, 'activate'])->name('products.activate');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});
