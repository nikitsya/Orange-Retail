<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register</title>
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

            .register-shell {
                width: min(100%, 460px);
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            .register-panel {
                padding: 32px 28px;
            }

            .eyebrow {
                display: inline-flex;
                margin-bottom: 16px;
                font-size: 0.84rem;
                font-weight: 700;
                color: var(--brand);
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
                font-family: inherit;
                font-size: 1rem;
                font-weight: 400;
                line-height: 1.5;
                color: var(--ink);
                background: #fff;
            }

            input:focus {
                outline: 2px solid rgba(45, 107, 69, 0.18);
                border-color: var(--brand);
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

            .footer-link {
                margin-top: 18px;
                color: var(--muted);
                font-size: 0.9rem;
            }

            .footer-link a {
                color: var(--brand);
                font-weight: 700;
            }

            @media (max-width: 600px) {
                body {
                    padding: 16px;
                }

                .register-panel {
                    padding: 24px 18px;
                }
            }
        </style>
    </head>
    <body>
        <main class="register-shell">
            <section class="register-panel">
                <a class="eyebrow" href="{{ route('home') }}">Back to home</a>

                <h1>Register</h1>
                <p class="lead">Create a new account to access the supermarket dashboard.</p>

                @if ($errors->any())
                    <div class="error-box">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <label for="name">
                        Full name
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="email">
                        Email
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
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

                    <label for="password_confirmation">
                        Confirm password
                        <input id="password_confirmation" type="password" name="password_confirmation" required>
                    </label>

                    <button class="button" type="submit">Create account</button>
                </form>

                <p class="footer-link">
                    Already have an account?
                    <a href="{{ route('login') }}">Login</a>
                </p>
            </section>
        </main>
    </body>
</html>
