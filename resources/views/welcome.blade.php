<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Supermarket Management') }}</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f5efe3;
                --surface: rgba(255, 250, 242, 0.78);
                --surface-strong: #fffaf2;
                --ink: #1f2a1f;
                --muted: #5f6b5e;
                --line: rgba(31, 42, 31, 0.12);
                --accent: #2d6a4f;
                --accent-strong: #bc4749;
                --highlight: #f4a261;
                --shadow: 0 24px 60px rgba(53, 45, 31, 0.12);
                --radius-xl: 32px;
                --radius-lg: 22px;
                --radius-md: 16px;
                --content-width: 1180px;
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
                font-family: "Trebuchet MS", "Gill Sans", sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(188, 71, 73, 0.18), transparent 30%),
                    radial-gradient(circle at top right, rgba(45, 106, 79, 0.20), transparent 26%),
                    linear-gradient(180deg, #f9f5ec 0%, var(--bg) 100%);
                color: var(--ink);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .page-shell {
                position: relative;
                overflow: hidden;
            }

            .page-shell::before,
            .page-shell::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                filter: blur(8px);
                pointer-events: none;
            }

            .page-shell::before {
                top: 90px;
                left: -120px;
                width: 320px;
                height: 320px;
                background: rgba(244, 162, 97, 0.16);
            }

            .page-shell::after {
                top: 560px;
                right: -140px;
                width: 360px;
                height: 360px;
                background: rgba(45, 106, 79, 0.14);
            }

            .container {
                width: min(calc(100% - 2rem), var(--content-width));
                margin: 0 auto;
            }

            .topbar {
                position: sticky;
                top: 0;
                z-index: 10;
                backdrop-filter: blur(18px);
                background: rgba(249, 245, 236, 0.74);
                border-bottom: 1px solid rgba(31, 42, 31, 0.08);
            }

            .topbar-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 0;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                font-weight: 700;
                letter-spacing: 0.03em;
            }

            .brand-mark {
                display: grid;
                place-items: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 14px;
                background: linear-gradient(135deg, var(--accent), #6a994e);
                color: #fff;
                box-shadow: 0 12px 24px rgba(45, 106, 79, 0.22);
            }

            .nav {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                justify-content: flex-end;
            }

            .nav a {
                padding: 0.7rem 1rem;
                border-radius: 999px;
                color: var(--muted);
                transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
            }

            .nav a:hover {
                background: rgba(45, 106, 79, 0.08);
                color: var(--ink);
                transform: translateY(-1px);
            }

            .hero {
                padding: 4.5rem 0 2rem;
            }

            .hero-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
                gap: 1.5rem;
                align-items: stretch;
            }

            .hero-card,
            .panel,
            .timeline-card,
            .page-card,
            .role-card {
                position: relative;
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius-xl);
                box-shadow: var(--shadow);
                backdrop-filter: blur(12px);
            }

            .hero-card {
                padding: 2.25rem;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                padding: 0.45rem 0.9rem;
                border-radius: 999px;
                font-size: 0.85rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--accent);
                background: rgba(45, 106, 79, 0.1);
            }

            h1,
            h2,
            h3 {
                margin: 0;
                line-height: 1.02;
                font-family: Georgia, "Times New Roman", serif;
            }

            h1 {
                margin-top: 1rem;
                max-width: 11ch;
                font-size: clamp(3rem, 5vw, 5.2rem);
            }

            .lead {
                margin: 1.25rem 0 0;
                max-width: 60ch;
                font-size: 1.08rem;
                line-height: 1.75;
                color: var(--muted);
            }

            .cta-row {
                display: flex;
                flex-wrap: wrap;
                gap: 0.9rem;
                margin-top: 1.75rem;
            }

            .button,
            .ghost-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.55rem;
                padding: 0.95rem 1.2rem;
                border-radius: 999px;
                font-weight: 700;
                transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            }

            .button {
                color: #fff;
                background: linear-gradient(135deg, var(--accent), #386641);
                box-shadow: 0 18px 32px rgba(45, 106, 79, 0.2);
            }

            .ghost-button {
                border: 1px solid var(--line);
                color: var(--ink);
                background: rgba(255, 255, 255, 0.6);
            }

            .button:hover,
            .ghost-button:hover {
                transform: translateY(-2px);
            }

            .hero-stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.9rem;
                margin-top: 2rem;
            }

            .stat {
                padding: 1rem;
                border-radius: var(--radius-md);
                background: rgba(255, 255, 255, 0.62);
                border: 1px solid rgba(31, 42, 31, 0.08);
            }

            .stat strong {
                display: block;
                font-size: 1.75rem;
                font-family: Georgia, "Times New Roman", serif;
            }

            .stat span {
                display: block;
                margin-top: 0.35rem;
                color: var(--muted);
                font-size: 0.92rem;
                line-height: 1.5;
            }

            .hero-side {
                display: grid;
                gap: 1rem;
            }

            .panel {
                padding: 1.5rem;
            }

            .panel-kicker {
                margin-bottom: 0.9rem;
                color: var(--accent-strong);
                font-size: 0.8rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 700;
            }

            .panel p,
            .section-copy,
            .role-card p,
            .timeline-card p,
            .page-card p {
                margin: 0;
                color: var(--muted);
                line-height: 1.7;
            }

            .check-list,
            .tag-list,
            .page-list {
                display: grid;
                gap: 0.7rem;
                margin: 1rem 0 0;
                padding: 0;
                list-style: none;
            }

            .check-list li,
            .tag-list li,
            .page-list li {
                display: flex;
                align-items: flex-start;
                gap: 0.7rem;
                color: var(--ink);
                line-height: 1.55;
            }

            .check-list li::before,
            .page-list li::before {
                content: "";
                flex: none;
                width: 0.7rem;
                height: 0.7rem;
                margin-top: 0.45rem;
                border-radius: 999px;
                background: linear-gradient(135deg, var(--accent), var(--highlight));
            }

            .tag-list li::before {
                content: "#";
                color: var(--accent-strong);
                font-weight: 700;
            }

            .section {
                padding: 1.5rem 0 0;
            }

            .section-heading {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                max-width: 48rem;
                margin-bottom: 1.35rem;
            }

            .section-heading h2 {
                font-size: clamp(2rem, 3.4vw, 3.3rem);
            }

            .cards-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 1rem;
            }

            .role-card,
            .page-card,
            .timeline-card {
                padding: 1.4rem;
            }

            .role-card h3,
            .page-card h3,
            .timeline-card h3 {
                font-size: 1.45rem;
                margin-bottom: 0.7rem;
            }

            .role-label {
                display: inline-flex;
                margin-bottom: 0.9rem;
                padding: 0.35rem 0.7rem;
                border-radius: 999px;
                background: rgba(188, 71, 73, 0.08);
                color: var(--accent-strong);
                font-size: 0.8rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .flow-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 1rem;
            }

            .timeline-card {
                min-height: 100%;
            }

            .step-index {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 999px;
                margin-bottom: 0.9rem;
                color: #fff;
                background: linear-gradient(135deg, var(--accent-strong), #d62828);
                font-weight: 700;
            }

            .pages-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 1rem;
            }

            .page-card.highlight {
                background: linear-gradient(160deg, rgba(45, 106, 79, 0.92), rgba(56, 102, 65, 0.9));
                color: #fff;
            }

            .page-card.highlight p,
            .page-card.highlight .page-list li {
                color: rgba(255, 255, 255, 0.82);
            }

            .page-card.highlight .page-list li::before {
                background: rgba(255, 255, 255, 0.9);
            }

            .footer {
                padding: 3rem 0 4rem;
            }

            .footer-card {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
                padding: 1.35rem 1.5rem;
                border-radius: 24px;
                background: rgba(255, 250, 242, 0.72);
                border: 1px solid var(--line);
                box-shadow: var(--shadow);
            }

            .footer-card p {
                margin: 0;
                color: var(--muted);
                line-height: 1.7;
            }

            @media (max-width: 1100px) {
                .hero-grid,
                .cards-grid,
                .flow-grid,
                .pages-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 760px) {
                .topbar-inner,
                .footer-card {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .nav {
                    justify-content: flex-start;
                }

                .hero {
                    padding-top: 3rem;
                }

                .hero-grid,
                .cards-grid,
                .flow-grid,
                .pages-grid,
                .hero-stats {
                    grid-template-columns: 1fr;
                }

                .hero-card,
                .panel,
                .role-card,
                .timeline-card,
                .page-card {
                    padding: 1.2rem;
                }

                h1 {
                    max-width: none;
                }
            }
        </style>
    </head>
    <body>
        <div class="page-shell">
            <header class="topbar">
                <div class="container topbar-inner">
                    <a class="brand" href="#home">
                        <span class="brand-mark">SM</span>
                        <span>Supermarket Management</span>
                    </a>

                    <nav class="nav" aria-label="Home page navigation">
                        <a href="#overview">Overview</a>
                        <a href="#roles">Roles</a>
                        <a href="#flow">Shopping Flow</a>
                        <a href="#pages">Pages</a>
                    </nav>
                </div>
            </header>

            <main id="home">
                <section class="hero">
                    <div class="container hero-grid">
                        <article class="hero-card">
                            <span class="eyebrow">Laravel MVC platform</span>
                            <h1>One supermarket system for customers and staff.</h1>
                            <p class="lead">
                                This web platform brings product browsing, stock visibility, checkout,
                                and role-based management into one structured environment for
                                small-to-medium supermarkets.
                            </p>

                            <div class="cta-row">
                                <a class="button" href="#flow">See shopping flow</a>
                                <a class="ghost-button" href="#roles">Explore user roles</a>
                            </div>

                            <div class="hero-stats">
                                <div class="stat">
                                    <strong>3</strong>
                                    <span>Core user roles: guest, customer, and admin.</span>
                                </div>
                                <div class="stat">
                                    <strong>5+</strong>
                                    <span>Required storefront views with clear navigation.</span>
                                </div>
                                <div class="stat">
                                    <strong>1</strong>
                                    <span>Centralized platform for shopping and store operations.</span>
                                </div>
                            </div>
                        </article>

                        <div class="hero-side">
                            <article class="panel" id="overview">
                                <div class="panel-kicker">Project focus</div>
                                <p>
                                    The system solves fragmented product, stock, order, and user
                                    management by combining catalog browsing, validation, authentication,
                                    and administration in one Laravel application.
                                </p>
                                <ul class="check-list">
                                    <li>Structured catalog with product details, search, and category filtering.</li>
                                    <li>Cart and checkout flow with stock-aware quantity controls.</li>
                                    <li>Admin product management with secure access boundaries.</li>
                                </ul>
                            </article>

                            <article class="panel">
                                <div class="panel-kicker">Core principles</div>
                                <ul class="tag-list">
                                    <li>Clear navigation and task flow</li>
                                    <li>Consistent interface behavior</li>
                                    <li>Validation and action feedback</li>
                                    <li>Role-based access control</li>
                                </ul>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="section" id="roles">
                    <div class="container">
                        <div class="section-heading">
                            <span class="eyebrow">Target users</span>
                            <h2>Different roles, one connected platform.</h2>
                            <p class="section-copy">
                                The README defines a shared system where each user group sees the same
                                store through a different level of access and responsibility.
                            </p>
                        </div>

                        <div class="cards-grid">
                            <article class="role-card">
                                <span class="role-label">Guest</span>
                                <h3>Browse before registering</h3>
                                <p>
                                    Guests can view public content and explore the product catalog, but
                                    they are guided toward registration for full shopping access.
                                </p>
                                <ul class="check-list">
                                    <li>View public pages and catalog content.</li>
                                    <li>Discover products and categories.</li>
                                    <li>Move toward sign-up for account features.</li>
                                </ul>
                            </article>

                            <article class="role-card">
                                <span class="role-label">Customer</span>
                                <h3>Shop with a complete order flow</h3>
                                <p>
                                    Registered customers can search products, manage the cart, place
                                    orders, and review personal order history.
                                </p>
                                <ul class="check-list">
                                    <li>Register and log in securely.</li>
                                    <li>Add items to cart and complete checkout.</li>
                                    <li>Track order history and status.</li>
                                </ul>
                            </article>

                            <article class="role-card">
                                <span class="role-label">Admin</span>
                                <h3>Control products and permissions</h3>
                                <p>
                                    Administrators manage catalog data, stock levels, and access rules
                                    to protect high-risk actions across the system.
                                </p>
                                <ul class="check-list">
                                    <li>Create, edit, and manage products.</li>
                                    <li>Control roles and protected routes.</li>
                                    <li>Oversee store operations from one dashboard.</li>
                                </ul>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="section" id="flow">
                    <div class="container">
                        <div class="section-heading">
                            <span class="eyebrow">Customer journey</span>
                            <h2>A simple shopping flow with clear task transitions.</h2>
                            <p class="section-copy">
                                The storefront should let users move from discovery to checkout without
                                confusion, while keeping validation and stock rules visible in the right places.
                            </p>
                        </div>

                        <div class="flow-grid">
                            <article class="timeline-card">
                                <span class="step-index">1</span>
                                <h3>Discover</h3>
                                <p>
                                    Browse the catalog, search by keyword, and filter products by category.
                                </p>
                            </article>

                            <article class="timeline-card">
                                <span class="step-index">2</span>
                                <h3>Inspect</h3>
                                <p>
                                    Open product details with image, description, price, and current stock state.
                                </p>
                            </article>

                            <article class="timeline-card">
                                <span class="step-index">3</span>
                                <h3>Build cart</h3>
                                <p>
                                    Add, remove, and update quantities while respecting available stock limits.
                                </p>
                            </article>

                            <article class="timeline-card">
                                <span class="step-index">4</span>
                                <h3>Checkout</h3>
                                <p>
                                    Confirm the order and create a stable order snapshot for later review.
                                </p>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="section" id="pages">
                    <div class="container">
                        <div class="section-heading">
                            <span class="eyebrow">Required views</span>
                            <h2>The home page anchors the rest of the application.</h2>
                            <p class="section-copy">
                                According to the README, the project UI should grow around a consistent
                                set of customer and admin pages with predictable layout behavior.
                            </p>
                        </div>

                        <div class="pages-grid">
                            <article class="page-card highlight">
                                <h3>Storefront pages</h3>
                                <p>
                                    Customer-facing pages cover discovery, product review, and order completion.
                                </p>
                                <ul class="page-list">
                                    <li>Home page</li>
                                    <li>Product catalog page</li>
                                    <li>Product details page</li>
                                    <li>Cart page</li>
                                    <li>Checkout page</li>
                                </ul>
                            </article>

                            <article class="page-card">
                                <h3>Account access</h3>
                                <p>
                                    Authentication should be visible, secure, and easy to reach from the public experience.
                                </p>
                                <ul class="page-list">
                                    <li>Login page</li>
                                    <li>Registration page</li>
                                    <li>Validation feedback near fields</li>
                                    <li>Clear success and error alerts</li>
                                </ul>
                            </article>

                            <article class="page-card">
                                <h3>Administration</h3>
                                <p>
                                    Management interfaces should stay protected while keeping CRUD tasks efficient.
                                </p>
                                <ul class="page-list">
                                    <li>Admin dashboard</li>
                                    <li>Admin products management page</li>
                                    <li>Role-based route protection</li>
                                    <li>Consistent component behavior</li>
                                </ul>
                            </article>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="footer">
                <div class="container">
                    <div class="footer-card">
                        <p>
                            Supermarket Management is designed as a practical Laravel MVC project with
                            authentication, authorization, product CRUD, catalog browsing, and a checkout flow.
                        </p>
                        <a class="ghost-button" href="#home">Back to top</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
