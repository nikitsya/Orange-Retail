<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Supermarket Management') }}</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #eef1eb;
                --surface: #ffffff;
                --surface-soft: #f6f7f4;
                --ink: #172118;
                --muted: #667165;
                --line: #d9dfd6;
                --brand: #2d6b45;
                --brand-strong: #1f5133;
                --accent: #d9ab3d;
                --shadow: 0 16px 36px rgba(18, 31, 20, 0.1);
                --radius: 18px;
                --content-width: 1080px;
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
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

            img {
                display: block;
                max-width: 100%;
            }

            .wrap {
                width: min(calc(100% - 24px), var(--content-width));
                margin: 0 auto;
            }

            .topbar {
                background: var(--brand);
                color: #fff;
                border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            }

            .topbar-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                min-height: 52px;
            }

            .brand {
                font-size: 0.92rem;
                font-weight: 700;
                letter-spacing: 0.01em;
            }

            .nav {
                display: flex;
                align-items: center;
                gap: 16px;
                font-size: 0.76rem;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .nav-links a {
                color: rgba(255, 255, 255, 0.9);
                transition: opacity 0.2s ease;
            }

            .nav-links a:hover,
            .auth-links a:hover {
                opacity: 0.72;
            }

            .auth-links {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .auth-links a {
                color: #fff;
                transition: opacity 0.2s ease;
            }

            .auth-links .register-link {
                padding: 0.4rem 0.8rem;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.14);
            }

            .main-content {
                padding: 14px 0 36px;
            }

            .hero {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(240px, 360px);
                gap: 28px;
                align-items: center;
                padding: 30px 12px 16px;
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: 0 0 var(--radius) var(--radius);
                box-shadow: var(--shadow);
                animation: hero-enter 0.55s ease-out;
            }

            .hero-copy {
                max-width: 420px;
            }

            .hero-copy h1 {
                margin: 0;
                font-size: clamp(2rem, 4vw, 2.75rem);
                line-height: 1.05;
                letter-spacing: -0.03em;
            }

            .hero-copy p {
                margin: 14px 0 0;
                color: var(--muted);
                font-size: 0.95rem;
                line-height: 1.6;
            }

            .hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 18px;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 38px;
                padding: 0.7rem 1rem;
                border-radius: 10px;
                font-size: 0.82rem;
                font-weight: 700;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .button:hover {
                transform: translateY(-1px);
            }

            .button-primary {
                background: var(--brand);
                color: #fff;
                box-shadow: 0 10px 20px rgba(45, 107, 69, 0.2);
            }

            .button-secondary {
                border: 1px solid var(--line);
                background: var(--surface-soft);
                color: var(--ink);
            }

            .hero-visual {
                position: relative;
                overflow: hidden;
                min-height: 230px;
                border-radius: 12px;
                background:
                    linear-gradient(180deg, rgba(34, 42, 29, 0.15), rgba(34, 42, 29, 0)),
                    linear-gradient(145deg, #5f3718, #8f5b2c 38%, #6d421e 100%);
                box-shadow: inset 0 0 0 1px rgba(24, 35, 25, 0.18);
            }

            .hero-visual::before,
            .hero-visual::after {
                content: "";
                position: absolute;
                inset: 0;
                pointer-events: none;
            }

            .hero-visual::before {
                background:
                    linear-gradient(90deg, transparent 8%, rgba(255, 255, 255, 0.18) 8%, rgba(255, 255, 255, 0.18) 10%, transparent 10%),
                    linear-gradient(90deg, transparent 48%, rgba(255, 255, 255, 0.18) 48%, rgba(255, 255, 255, 0.18) 50%, transparent 50%),
                    linear-gradient(90deg, transparent 84%, rgba(255, 255, 255, 0.18) 84%, rgba(255, 255, 255, 0.18) 86%, transparent 86%),
                    linear-gradient(180deg, transparent 20%, rgba(255, 255, 255, 0.2) 20%, rgba(255, 255, 255, 0.2) 22%, transparent 22%),
                    linear-gradient(180deg, transparent 48%, rgba(255, 255, 255, 0.2) 48%, rgba(255, 255, 255, 0.2) 50%, transparent 50%),
                    linear-gradient(180deg, transparent 76%, rgba(255, 255, 255, 0.2) 76%, rgba(255, 255, 255, 0.2) 78%, transparent 78%);
            }

            .hero-visual::after {
                background:
                    radial-gradient(circle at 16% 18%, #86b43e 0 10%, transparent 11%),
                    radial-gradient(circle at 28% 18%, #d7bb2d 0 10%, transparent 11%),
                    radial-gradient(circle at 39% 18%, #f17a2a 0 10%, transparent 11%),
                    radial-gradient(circle at 61% 18%, #6cab3f 0 10%, transparent 11%),
                    radial-gradient(circle at 73% 18%, #d64b30 0 10%, transparent 11%),
                    radial-gradient(circle at 84% 18%, #ead24d 0 10%, transparent 11%),
                    radial-gradient(circle at 16% 46%, #5b8f35 0 10%, transparent 11%),
                    radial-gradient(circle at 28% 46%, #c95a2a 0 10%, transparent 11%),
                    radial-gradient(circle at 39% 46%, #82a83a 0 10%, transparent 11%),
                    radial-gradient(circle at 61% 46%, #f1c84c 0 10%, transparent 11%),
                    radial-gradient(circle at 73% 46%, #79a23d 0 10%, transparent 11%),
                    radial-gradient(circle at 84% 46%, #d84c39 0 10%, transparent 11%),
                    radial-gradient(circle at 16% 74%, #b6cb47 0 10%, transparent 11%),
                    radial-gradient(circle at 28% 74%, #8fae3d 0 10%, transparent 11%),
                    radial-gradient(circle at 39% 74%, #e7d65e 0 10%, transparent 11%),
                    radial-gradient(circle at 61% 74%, #688f39 0 10%, transparent 11%),
                    radial-gradient(circle at 73% 74%, #d2a63e 0 10%, transparent 11%),
                    radial-gradient(circle at 84% 74%, #88b443 0 10%, transparent 11%);
                opacity: 0.98;
            }

            .section {
                margin-top: 18px;
                padding: 0 12px;
            }

            .section-header {
                display: flex;
                align-items: end;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 12px;
            }

            .section-header h2 {
                margin: 0;
                font-size: 1.2rem;
                letter-spacing: -0.02em;
            }

            .section-header p {
                margin: 0;
                color: var(--muted);
                font-size: 0.86rem;
            }

            .categories {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 16px;
            }

            .category-card {
                overflow: hidden;
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: 14px;
                box-shadow: 0 12px 24px rgba(18, 31, 20, 0.08);
                transition: transform 0.22s ease, box-shadow 0.22s ease;
            }

            .category-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 18px 34px rgba(18, 31, 20, 0.12);
            }

            .category-art {
                height: 138px;
                background-color: #bcc4b5;
            }

            .category-card h3 {
                margin: 0;
                font-size: 1rem;
            }

            .category-body {
                padding: 14px;
            }

            .category-body p {
                margin: 8px 0 0;
                color: var(--muted);
                font-size: 0.86rem;
                line-height: 1.55;
            }

            .produce-art {
                background:
                    radial-gradient(circle at 16% 30%, #f6af2f 0 12%, transparent 13%),
                    radial-gradient(circle at 34% 28%, #cf4f2d 0 14%, transparent 15%),
                    radial-gradient(circle at 54% 28%, #7cb33d 0 13%, transparent 14%),
                    radial-gradient(circle at 76% 24%, #edc52b 0 13%, transparent 14%),
                    radial-gradient(circle at 24% 68%, #72a73d 0 13%, transparent 14%),
                    radial-gradient(circle at 48% 70%, #ee7c23 0 13%, transparent 14%),
                    radial-gradient(circle at 72% 70%, #d8432e 0 13%, transparent 14%),
                    linear-gradient(135deg, #2b4a24 0%, #4e6f35 100%);
            }

            .bakery-art {
                background:
                    radial-gradient(circle at 20% 62%, #8c5c33 0 16%, transparent 17%),
                    radial-gradient(circle at 43% 38%, #6a4326 0 20%, transparent 21%),
                    radial-gradient(circle at 76% 50%, #a87341 0 18%, transparent 19%),
                    linear-gradient(135deg, #d8c7aa 0%, #b39b79 100%);
            }

            .dairy-art {
                background:
                    linear-gradient(90deg, transparent 30%, rgba(255, 255, 255, 0.42) 30%, rgba(255, 255, 255, 0.42) 55%, transparent 55%),
                    radial-gradient(circle at 72% 72%, #1f2a2f 0 18%, transparent 19%),
                    radial-gradient(circle at 44% 62%, #ffffff 0 15%, transparent 16%),
                    linear-gradient(135deg, #29323a 0%, #75808a 100%);
            }

            .info-strip {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 16px;
                margin-top: 18px;
                padding: 0 12px;
            }

            .info-item {
                padding: 14px 16px;
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid var(--line);
                border-radius: 14px;
            }

            .info-item strong {
                display: block;
                margin-bottom: 4px;
                font-size: 0.86rem;
            }

            .info-item span {
                color: var(--muted);
                font-size: 0.82rem;
                line-height: 1.45;
            }

            @keyframes hero-enter {
                from {
                    opacity: 0;
                    transform: translateY(14px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 840px) {
                .hero,
                .categories,
                .info-strip {
                    grid-template-columns: 1fr;
                }

                .hero {
                    padding-top: 22px;
                }

                .hero-copy {
                    max-width: none;
                }
            }

            @media (max-width: 620px) {
                .topbar-inner {
                    flex-direction: column;
                    align-items: flex-start;
                    justify-content: center;
                    padding: 10px 0;
                }

                .nav {
                    width: 100%;
                    flex-wrap: wrap;
                    justify-content: space-between;
                    gap: 10px;
                }

                .nav-links,
                .auth-links {
                    flex-wrap: wrap;
                }

                .main-content {
                    padding-top: 10px;
                }

                .hero-copy h1 {
                    font-size: 1.85rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="page-shell">
            <header class="topbar">
                <div class="wrap topbar-inner">
                    <a class="brand" href="#home">Supermarket Store</a>

                    <nav class="nav" aria-label="Main navigation">
                        <div class="nav-links">
                            <a href="#home">Home</a>
                            <a href="#categories">Products</a>
                            <a href="#categories">Cart</a>
                        </div>

                        <div class="auth-links">
                            <a href="/login">Login</a>
                            <a class="register-link" href="/register">Register</a>
                        </div>
                    </nav>
                </div>
            </header>

            <main class="main-content" id="home">
                <div class="wrap">
                    <section class="hero" aria-labelledby="hero-title">
                        <div class="hero-copy">
                            <h1 id="hero-title">Fresh groceries delivered simply</h1>
                            <p>
                                Browse produce, add items to your cart, and place orders through
                                a clean supermarket experience built for everyday shopping.
                            </p>

                            <div class="hero-actions">
                                <a class="button button-primary" href="#categories">Shop now</a>
                                <a class="button button-secondary" href="/register">Create account</a>
                            </div>
                        </div>

                        <div class="hero-visual" aria-hidden="true"></div>
                    </section>

                    <section class="section" id="categories" aria-labelledby="categories-title">
                        <div class="section-header">
                            <div>
                                <h2 id="categories-title">Popular Categories</h2>
                                <p>Start with the essentials most customers look for first.</p>
                            </div>
                        </div>

                        <div class="categories">
                            <article class="category-card">
                                <div class="category-art produce-art" aria-hidden="true"></div>
                                <div class="category-body">
                                    <h3>Fresh Produce</h3>
                                    <p>Seasonal fruits and vegetables picked for fast daily orders.</p>
                                </div>
                            </article>

                            <article class="category-card">
                                <div class="category-art bakery-art" aria-hidden="true"></div>
                                <div class="category-body">
                                    <h3>Bakery</h3>
                                    <p>Fresh bread, breakfast pastries, and baked goods for every cart.</p>
                                </div>
                            </article>

                            <article class="category-card">
                                <div class="category-art dairy-art" aria-hidden="true"></div>
                                <div class="category-body">
                                    <h3>Dairy</h3>
                                    <p>Milk, cheese, and chilled basics kept visible and easy to reach.</p>
                                </div>
                            </article>
                        </div>
                    </section>

                    <section class="info-strip" aria-label="Store benefits">
                        <div class="info-item">
                            <strong>Fast ordering</strong>
                            <span>Move from browsing to checkout with a straightforward flow.</span>
                        </div>

                        <div class="info-item">
                            <strong>Clear categories</strong>
                            <span>Popular product groups stay visible right on the home page.</span>
                        </div>

                        <div class="info-item">
                            <strong>Simple access</strong>
                            <span>Login or register quickly before managing your cart and orders.</span>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
