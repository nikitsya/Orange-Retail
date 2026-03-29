<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f1f4ef;
                --surface: #ffffff;
                --ink: #182119;
                --muted: #637061;
                --line: #d7dfd5;
                --brand: #2d6b45;
                --brand-strong: #1f5234;
                --danger-bg: #fbe8e8;
                --danger-ink: #9c2f2f;
                --shadow: 0 20px 44px rgba(18, 31, 20, 0.1);
                --radius: 20px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 24px;
                font-family: Arial, Helvetica, sans-serif;
                background: var(--bg);
                color: var(--ink);
            }

            .login-shell {
                width: min(100%, 420px);
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            .login-panel {
                padding: 32px 28px;
            }

            h1 {
                margin: 0;
                font-size: 1.65rem;
                font-weight: 700;
                letter-spacing: -0.03em;
            }

            .lead {
                margin: 10px 0 0;
                color: var(--muted);
                font-size: 0.9rem;
                line-height: 1.6;
            }

            form {
                display: grid;
                gap: 16px;
                margin-top: 28px;
            }

            label {
                display: grid;
                gap: 8px;
                font-size: 0.88rem;
                font-weight: 700;
            }

            input {
                width: 100%;
                min-height: 48px;
                padding: 0.9rem 0.95rem;
                border: 1px solid var(--line);
                border-radius: 14px;
                font: inherit;
                color: var(--ink);
                background: #fff;
            }

            input:focus {
                outline: 2px solid rgba(45, 107, 69, 0.18);
                border-color: var(--brand);
            }

            .remember-row {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.9rem;
                color: var(--muted);
            }

            .remember-row input {
                width: auto;
                min-height: auto;
            }

            .button {
                min-height: 50px;
                border: 0;
                border-radius: 14px;
                background: linear-gradient(135deg, var(--brand), var(--brand-strong));
                color: #fff;
                font: inherit;
                font-weight: 700;
                cursor: pointer;
            }

            .error-box {
                padding: 12px 14px;
                border-radius: 14px;
                background: var(--danger-bg);
                color: var(--danger-ink);
                font-size: 0.9rem;
                line-height: 1.5;
            }

            .field-error {
                color: var(--danger-ink);
                font-size: 0.82rem;
                font-weight: 400;
            }

            @media (max-width: 600px) {
                body {
                    padding: 16px;
                }

                .login-panel {
                    padding: 24px 18px;
                }
            }
        </style>
    </head>
    <body>
        <main class="login-shell">
            <section class="login-panel">
                <h1>Login</h1>
                <p class="lead">Enter your email and password to continue.</p>

                @if ($errors->any())
                    <div class="error-box">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <label for="email">
                        Email
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="password">
                        Password
                        <input id="password" type="password" name="password" required>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="remember-row" for="remember">
                        <input id="remember" type="checkbox" name="remember" value="1">
                        Remember me
                    </label>

                    <button class="button" type="submit">Login</button>
                </form>
            </section>
        </main>
    </body>
</html>
