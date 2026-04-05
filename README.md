# Orange Retail

Orange Retail is a Laravel 12 supermarket web application prototype with two working surfaces:

- a customer-facing product catalog with search, category filtering, product details, and a session cart
- an admin inventory screen for managing products

The repository is currently in a mid-build state. This README describes what is actually implemented now, not the larger target scope from earlier drafts.

## Current Status

Implemented now:

- guest access to the catalog and product detail pages
- customer registration and login
- role-based redirects after login
- admin-only access to inventory management
- product search and category filtering
- session-based cart for authenticated non-admin users
- create, update, and delete product records from the inventory UI
- product import from `database/data/supermarket_products.json`
- feature tests for authentication, catalog browsing, cart flow, and admin inventory access

Not implemented yet:

- checkout and order placement
- order history or order management
- real payments
- stock tracking and stock-aware cart validation
- a separate staff role with its own permissions
- image upload handling

## Application Areas

### Customer Area

- browse the catalog at `/` or `/catalog`
- search by product name, description, brand, or category
- filter by department
- open a dedicated product details page
- add and remove products from a session-based cart after login

### Admin Area

- sign in as an admin and open `/products`
- browse inventory with pagination, search, and department filters
- add new products
- edit existing products in modal forms
- delete products from the inventory list

## Roles

- `guest`: can browse the catalog and product details
- `user`: can register, log in, browse the catalog, and use the cart
- `admin`: can access the full inventory management interface

The application currently uses a single `role` column on the `users` table together with `App\Http\Middleware\EnsureAdmin`.

## Data Model in Use

### Users

The `users` table is the standard Laravel authentication table with an added `role` field.

### Products

The `products` table currently stores catalog and inventory metadata such as:

- `sku`
- `barcode`
- `name`
- `brand`
- `category`
- `subcategory`
- `description`
- `image_url`
- `unit_type`
- `pack_size`
- `weight_value`
- `weight_unit`
- `price_value`
- `currency`
- `price_display`
- `unit_price_display`

Products are seeded from `database/data/supermarket_products.json`.

## Tech Stack

- PHP 8.2+
- Laravel 12
- Blade templates
- Laravel authentication with session-based auth
- Vite
- Tailwind CSS v4 tooling
- PHPUnit

## Local Setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Use any Laravel-supported database. For a quick local setup, SQLite is the simplest option.

Example:

```bash
touch database/database.sqlite
```

Then update `.env` so the database connection points to `database/database.sqlite`.

### 3. Run migrations and seed data

```bash
php artisan migrate:fresh --seed
```

This seeds:

- an admin account
- a regular user account
- the product catalog dataset

The seeded credentials are defined in [database/seeders/DatabaseSeeder.php](/Users/nikitsya/projects/Supermarket-Management/database/seeders/DatabaseSeeder.php). Review or change them before using the project outside local development.

### 4. Start the application

```bash
composer dev
```

This starts:

- the Laravel development server
- the queue listener
- Laravel Pail
- the Vite dev server

## Useful Commands

```bash
composer dev
composer test
npm run dev
npm run build
php artisan migrate:fresh --seed
```

There is also a `composer setup` script, but it still depends on a correctly configured database connection in `.env`.

## Route Summary

- `/` and `/catalog`: customer catalog
- `/catalog/{product}`: product details
- `/login`: login form
- `/register`: registration form
- `/cart`: session cart for authenticated non-admin users
- `/products`: admin-only inventory management

## Project Structure

- [app/Http/Controllers/CatalogController.php](/Users/nikitsya/projects/Supermarket-Management/app/Http/Controllers/CatalogController.php): catalog, product details, and cart actions
- [app/Http/Controllers/ProductController.php](/Users/nikitsya/projects/Supermarket-Management/app/Http/Controllers/ProductController.php): admin inventory CRUD
- [app/Http/Controllers/Auth/ManagerSessionController.php](/Users/nikitsya/projects/Supermarket-Management/app/Http/Controllers/Auth/ManagerSessionController.php): login, registration, and logout
- [app/Http/Middleware/EnsureAdmin.php](/Users/nikitsya/projects/Supermarket-Management/app/Http/Middleware/EnsureAdmin.php): admin-only route protection
- [resources/views/catalog](/Users/nikitsya/projects/Supermarket-Management/resources/views/catalog): customer catalog pages
- [resources/views/products](/Users/nikitsya/projects/Supermarket-Management/resources/views/products): admin inventory page
- [database/seeders/IrishSupermarketProductsSeeder.php](/Users/nikitsya/projects/Supermarket-Management/database/seeders/IrishSupermarketProductsSeeder.php): JSON import seeder

## Testing

Run the test suite with:

```bash
composer test
```

The existing test suite focuses on:

- authentication and registration
- admin authorization
- catalog browsing and pagination
- session cart add/remove flow
- admin product CRUD

## Known Gaps

This project already works as a catalog and inventory prototype, but it is not yet a complete supermarket commerce system. The biggest missing parts are checkout, orders, stock control, and a broader back-office workflow.
