<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Supermarket Management') }}</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f3f5f0;
                --surface: #ffffff;
                --ink: #182119;
                --line: #d7dfd5;
                --brand: #2d6b45;
                --brand-strong: #1f5234;
                --shadow: 0 18px 36px rgba(18, 31, 20, 0.08);
                --content-width: 1120px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: Arial, Helvetica, sans-serif;
                background: var(--bg);
                color: var(--ink);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .wrap {
                width: min(calc(100% - 32px), var(--content-width));
                margin: 0 auto;
            }

            .site-header {
                position: sticky;
                top: 0;
                background: rgba(243, 245, 240, 0.95);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--line);
            }

            .site-header-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                min-height: 76px;
            }

            .brand {
                font-size: 1rem;
                font-weight: 700;
                letter-spacing: -0.02em;
            }

            .auth-links {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .auth-links a {
                padding: 0.7rem 1rem;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: var(--surface);
                box-shadow: var(--shadow);
                font-size: 0.92rem;
                font-weight: 700;
                transition: transform 0.2s ease, border-color 0.2s ease;
            }

            .auth-links a:hover {
                transform: translateY(-1px);
                border-color: rgba(45, 107, 69, 0.35);
            }

            .auth-links .register-link {
                border-color: transparent;
                background: linear-gradient(135deg, var(--brand), var(--brand-strong));
                color: #ffffff;
            }

            main {
                min-height: calc(100vh - 77px);
            }

            @media (max-width: 640px) {
                .site-header-inner {
                    min-height: 68px;
                }

                .brand {
                    font-size: 0.92rem;
                }

                .auth-links {
                    gap: 8px;
                }

                .auth-links a {
                    padding: 0.62rem 0.86rem;
                    font-size: 0.84rem;
                }
            }
        </style>
    </head>
    <body>
        <header class="site-header">
            <div class="wrap site-header-inner">
                <a class="brand" href="{{ route('home') }}">{{ config('app.name', 'Supermarket Management') }}</a>

                <nav class="auth-links" aria-label="Authentication links">
                    <a href="{{ route('login') }}">Login</a>
                    <a class="register-link" href="{{ route('register') }}">Register</a>
                </nav>
            </div>
        </header>

        <main></main>
    </body>
</html>
