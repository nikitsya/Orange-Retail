<?php

use App\Http\Controllers\Auth\ManagerSessionController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
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
});

Route::middleware('auth')->group(function (): void {
    Route::redirect('/dashboard', '/catalog')->name('dashboard');

    Route::get('/cart', [CatalogController::class, 'cart'])->name('cart.index');
    Route::post('/cart/{product}', [CatalogController::class, 'addToCart'])->name('cart.store');
    Route::delete('/cart/{product}', [CatalogController::class, 'removeFromCart'])->name('cart.destroy');
    Route::post('/logout', [ManagerSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', EnsureAdmin::class])->group(function (): void {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});
