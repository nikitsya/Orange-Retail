<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Orange Retail | Login</title>
        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="stylesheet" href="{{ asset('css/orange-market.css') }}">
    </head>
    <body>
        <main class="auth-page">
            <div class="page-shell auth-layout">
                <section class="auth-panel">
                    <a class="button-secondary" href="{{ route('home') }}">Back to home</a>
                    <h1>Login</h1>
                    <p>Enter your email and password to continue.</p>

                    @if ($errors->any())
                        <div class="error-message">{{ $errors->first() }}</div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <label class="field-label" for="email">
                            Email
                            <input class="field" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
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

                        <label class="remember-row" for="remember">
                            <input id="remember" type="checkbox" name="remember" value="1">
                            Remember me
                        </label>

                        <div class="auth-footer-actions">
                            <button class="button-primary" type="submit">Login</button>
                            <a class="button-secondary" href="{{ route('register') }}">Register</a>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>
