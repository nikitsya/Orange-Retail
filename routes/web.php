<?php

use App\Http\Controllers\Auth\ManagerSessionController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()?->role === 'admin'
        ? redirect()->route('products.index')
        : redirect()->route('dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [ManagerSessionController::class, 'create'])->name('login');
    Route::post('/login', [ManagerSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [ManagerSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', EnsureAdmin::class])->group(function (): void {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});
