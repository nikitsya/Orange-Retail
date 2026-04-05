<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Orange Retail | Register</title>
        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
    </head>
    <body>
        <main class="auth-page">
            <div class="page-shell auth-layout">
                <section class="auth-aside">
                    @include('partials.brand-name', ['class' => 'brand-tag'])
                    <h2>Create an account</h2>
                    <p>Register a customer account to open the dashboard and use the cart.</p>

                    <div class="hero-notes" style="margin-top: 24px;">
                        <div class="hero-note">
                            <strong>Catalog</strong>
                            <span>Search products and browse departments.</span>
                        </div>
                        <div class="hero-note">
                            <strong>Dashboard</strong>
                            <span>Open your customer area after registration.</span>
                        </div>
                        <div class="hero-note">
                            <strong>Cart</strong>
                            <span>Keep selected items in the session cart.</span>
                        </div>
                    </div>
                </section>

                <section class="auth-panel">
                    <a class="button-secondary" href="{{ route('home') }}">Back to home</a>
                    <h1>Register</h1>
                    <p>Create a new account to access the supermarket dashboard.</p>

                    @if ($errors->any())
                        <div class="error-message">{{ $errors->first() }}</div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <label class="field-label" for="name">
                            Full name
                            <input class="field" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="field-label" for="email">
                            Email
                            <input class="field" id="email" type="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="field-label" for="password">
                            Password
                            <input class="field" id="password" type="password" name="password" required>
                            @error('password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="field-label" for="password_confirmation">
                            Confirm password
                            <input class="field" id="password_confirmation" type="password" name="password_confirmation" required>
                        </label>

                        <div class="auth-footer-actions">
                            <button class="button-primary" type="submit">Create account</button>
                            <a class="button-secondary" href="{{ route('login') }}">Login</a>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>
