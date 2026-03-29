<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>User Dashboard</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f1f4ef;
                --surface: #ffffff;
                --ink: #182119;
                --muted: #657064;
                --line: #d7dfd5;
                --brand: #2d6b45;
                --shadow: 0 18px 40px rgba(18, 31, 20, 0.1);
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

            .panel {
                width: min(100%, 520px);
                padding: 32px 28px;
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            h1 {
                margin: 0;
                font-size: 1.8rem;
                letter-spacing: -0.03em;
            }

            p {
                margin: 12px 0 0;
                color: var(--muted);
                line-height: 1.65;
            }

            .logout-form {
                margin-top: 24px;
            }

            button {
                min-height: 44px;
                padding: 0.8rem 1rem;
                border: 0;
                border-radius: 12px;
                background: var(--brand);
                color: #fff;
                font: inherit;
                font-weight: 700;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <main class="panel">
            <h1>User Dashboard</h1>
            <p>You are logged in as a regular user. This page is separate from the admin product management area.</p>

            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </main>
    </body>
</html>
