# Orange Retail

## Project Overview

Orange Retail is a Laravel MVC supermarket web application with separate customer and administrator areas.

The project was designed to solve a common problem in small and medium supermarkets: product data, stock tracking, and
customer orders are often managed in disconnected tools or manual workflows. This system brings those tasks together in
one structured web platform.

The application includes:

- a customer-facing shopping experience
- an administrator-facing management area
- role-based access control
- relational database storage using Laravel migrations
- server-side and client-side validation

## Problem Statement

Many supermarkets need a simple but reliable platform that supports both customer shopping and internal store
management.

Typical problems include:

- no central product and inventory database
- weak stock visibility
- limited order tracking for customers
- poor separation between customer and admin access
- inconsistent data caused by weak validation

Orange Retail addresses these problems by using a Laravel MVC structure, validated forms, role-based access, and
connected product, stock, and order workflows.

## User Roles

### Guest

- can browse the public catalogue
- can search for products
- must register or log in to place orders

### Customer

- can register and log in
- can browse the catalogue
- can search and filter products by category and subcategory
- can save favourite products
- can add products to the cart
- can complete checkout
- can view order history and order details

### Admin

- can access the admin dashboard
- can manage products through CRUD operations
- can manage stock in the Stock Center
- can review customer orders
- can update order statuses

## Implemented Features

### Authentication and Authorisation

- user registration and login
- secure password hashing
- session-based authentication
- role-based redirection after login
- protected admin-only routes

### Customer Features

- customer dashboard
- product catalogue
- category and subcategory navigation
- keyword search
- product details page
- favourites / saved products
- shopping cart
- checkout with delivery form validation
- order history
- customer order details

### Admin Features

- admin dashboard
- product management
- full product CRUD
- stock management and stock alerts
- admin order queue
- order status updates

### Data and Validation

- relational database with Laravel migrations
- seeded users and products
- server-side validation
- client-side validation through browser form constraints
- stock-aware cart and checkout rules

## Main Database Entities

The final application uses the following main entities:

- `users`
- `products`
- `orders`
- `order_items`
- `stock_movements`
- `favorite_products`

## Tech Stack

- Laravel
- PHP 8.4
- Blade templates
- Eloquent ORM
- SQLite
- Vite
- HTML / CSS / JavaScript

## Setup and Installation

1. Clone the repository.
2. Install PHP dependencies.
3. Install frontend dependencies.
4. Copy `.env.example` to `.env`.
5. Configure the database connection.
6. Run migrations and seeders.
7. Start the Laravel development server.
8. Start Vite.

Example commands:

```bash
composer install
npm install
copy .env.example .env
C:\php84\php.exe artisan key:generate
C:\php84\php.exe artisan migrate --seed
C:\php84\php.exe artisan serve
npm run dev
```

## Demo Accounts

### Admin

- Email: `nikita@gmail.com`
- Password: `smiichyk123`

### Customer

- Email: `user@supermarket.test`
- Password: `user123`

## Video Demonstration

YouTube screencast: `ADD_YOUR_VIDEO_LINK_HERE`

## Validation and Security

The project includes both usability-focused validation and backend protection.

Examples include:

- required fields for registration, checkout, and product management
- email format validation
- numeric and non-negative stock validation
- positive price validation
- stock checks during cart updates and checkout
- CSRF protection on forms
- route protection based on user role

## Assumptions, Limitations, and Known Issues

### Assumptions

- the project is run in a local development environment
- users access the system through a modern web browser
- payment is simulated and not connected to a real payment provider

### Limitations

- the project does not include a real payment gateway
- the project does not include delivery route optimisation
- the imported dataset does not include real product images for all products
- placeholder images are used where no image is available

### Known Issues

- some local Windows environments may cause temporary Blade cache file locking during automated tests
- product images depend on the available seeded dataset and may use placeholders instead of real item photos

## Collaboration and Individual Contributions

This project was completed as a pair project, with responsibilities divided between the customer-facing shopping
experience and the administrator management system.

### Hanna Bokariuk

Hanna focused mainly on the customer-facing side of the application. Her work included:

- customer product catalogue and product details flow
- shopping cart and checkout experience
- checkout validation improvements
- customer order history and customer order details
- favourites / saved products functionality
- customer dashboard improvements
- customer-facing UX refinements across catalogue, cart, checkout, and order views

### Nikita Smiichyk

Nikita focused mainly on the administrator-facing side of the application and the core management features. His work
included:

- admin dashboard
- product and inventory management
- product CRUD functionality
- Stock Center and stock alert workflows
- admin order queue and order status management
- shared admin layout and styling improvements
- initial repository setup and project documentation work

## MVC Architecture

The application follows the Laravel MVC pattern:

- **Models** define the business entities and Eloquent relationships
- **Views** render Blade templates for the customer and admin interfaces
- **Controllers** handle requests, validation, business logic, and responses

This structure helps keep the project organised, maintainable, and easier to explain during the project defence.
