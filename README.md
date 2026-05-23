# Orange Retail

Orange Retail is a Laravel MVC supermarket web application with customer-facing shopping features and an administrator
management area. The project supports product browsing, account access, favourites, cart management, Stripe test
payments, order tracking, stock control, and administrator product management.

## Overview

The overview below shows the main Orange Retail shopping and management experience.

![Orange Retail overview](docs/media/orange-retail-overview.gif)

## Deployment Platform

Orange Retail runs on Microsoft Azure App Service with Azure Database for MySQL. The deployed application uses the
production `APP_URL` value configured in the Azure environment.

Public Azure URL: [Open the live site](https://lavarel-supermarket-management-abgbdxf3cchfcdbu.polandcentral-01.azurewebsites.net)

## CA3 Project Summary

This version of Orange Retail was completed for CA3: Laravel Deployment, Feedback Implementation and Professional
Engagement. The work improves the previous Laravel project, supports cloud hosting, connects it to an online database,
and documents the testing completed around the main application workflows.

The submitted version demonstrates:

- implemented feedback from the previous assessment
- a stable Laravel application configured for Azure deployment
- Microsoft Azure hosting through Azure App Service
- a cloud database connection using MySQL
- evidence of testing on desktop and mobile screens
- clear reflection on the deployment process and technical decisions

## Feedback Implementation

The following changes were made in response to tutor feedback and further project review:

- The page header was redesigned so that the Orange Retail logo is positioned at the top of the interface.
- The previous thin top navigation area was moved below the main header and improved for clearer visual hierarchy.
- Catalogue, stock, and order search controls were refined to reduce unnecessary confirmation steps.
- Search fields now update results automatically while typing, using debounced live search behaviour.
- Cart quantity controls now update automatically when the quantity changes, making the shopping flow faster.
- The responsive layout was reviewed so that the same core features are available on desktop and mobile screens.
- Product and department imagery was added to improve the catalogue presentation.

## Additional CA3 Improvements

In addition to the feedback changes, the project was improved with:

- an online MySQL database for deployment
- Stripe Checkout Sessions with the Stripe Payment Element for test payments
- Stripe webhook handling for payment status updates
- Google OAuth sign-in for customer accounts
- improved customer and administrator navigation
- Git branch usage to manage development work more professionally
- post-deployment testing of the main user workflows

## Core Features

### Customer Area

- public product catalogue
- department, category, and subcategory browsing
- keyword product search
- product details pages
- customer registration and login
- Google sign-in for customer accounts
- favourites / saved products
- shopping cart
- stock-aware quantity updates
- checkout delivery form validation
- Stripe test payment flow
- order history and order details

### Administrator Area

- protected administrator dashboard
- role-based administrator access
- product management
- product create, read, update, and delete operations
- stock management
- stock alert information
- order queue review
- order status updates

### Data and Validation

- Laravel migrations for database structure
- seeded users and supermarket products
- server-side validation for important forms
- browser-level validation where suitable
- CSRF protection on form submissions
- protected routes for authenticated and administrator-only areas
- stock checks during cart and checkout actions
- payment status tracking for Stripe orders

## User Roles

### Guest

- can browse the product catalogue
- can search for products
- can view product details
- must register or log in before placing an order

### Customer

- can manage a customer session
- can save favourite products
- can add products to the cart
- can complete checkout with Stripe test payments
- can view order history and order details

### Administrator

- can access the administrator dashboard
- can manage product records
- can update stock information
- can view customer orders
- can update order statuses

## Main Database Entities

The application uses the following main database entities:

- `users`
- `products`
- `orders`
- `order_items`
- `stock_movements`
- `favorite_products`

## Technology Stack

- Laravel 12
- PHP 8.2+
- Blade templates
- Eloquent ORM
- MySQL for cloud deployment
- SQLite or MySQL for local development
- Vite
- HTML, CSS, and JavaScript
- Stripe PHP SDK
- Google OAuth
- Microsoft Azure App Service
- Azure Database for MySQL

## Local Setup

1. Clone the repository.
2. Install PHP dependencies.
3. Install frontend dependencies.
4. Copy `.env.example` to `.env`.
5. Generate the Laravel application key.
6. Configure the database connection. The default `.env.example` uses SQLite; use the MySQL example below when testing a
   MySQL connection locally or in Azure.
7. Configure Stripe and Google credentials for payment and OAuth testing.
8. Run migrations and seeders.
9. Build or run the frontend assets.
10. Start the Laravel development server.

Example commands:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Start Vite and Laravel in separate terminals:

```bash
npm run dev
php artisan serve
```

For production assets, run:

```bash
npm run build
```

Alternatively, the combined development workflow can be started with:

```bash
composer run dev
```

For Windows PowerShell, use:

```powershell
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Environment Configuration

The project uses environment variables for database, Stripe, Google OAuth, and deployment settings.

Default local SQLite example:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
```

Local MySQL example:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supermarket
DB_USERNAME=root
DB_PASSWORD=
```

Azure production example:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-production-domain.azurewebsites.net

DB_CONNECTION=mysql
DB_HOST=your-azure-mysql-host.mysql.database.azure.com
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Secrets must be configured in Azure App Service environment variables, not committed to the repository.

## Stripe Test Payments

The checkout flow uses Stripe Checkout Sessions with `ui_mode=elements` and renders the Stripe Payment Element inside the
Laravel checkout experience.

Add Stripe test keys to the local `.env` file or to Azure App Service environment variables:

```dotenv
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_CURRENCY=eur
```

For local webhook testing, install the Stripe CLI and forward events to Laravel:

```bash
stripe listen --forward-to http://127.0.0.1:8000/stripe/webhook
```

Use the webhook signing secret printed by the Stripe CLI as `STRIPE_WEBHOOK_SECRET`. In Stripe test mode, the standard
test card `4242 4242 4242 4242` can be used with any future expiry date, any CVC, and any postcode.

## Google Sign-In

Google OAuth is available for customer accounts and uses these environment values:

```dotenv
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

The Google OAuth redirect URI must match the deployed Azure URL when the application is running in production.

## Azure Deployment

The CA3 deployment target is Microsoft Azure. The project uses the following deployment structure:

- Azure App Service for the Laravel web application
- Azure Database for MySQL Flexible Server for the cloud database
- Azure App Service environment variables for production secrets
- GitHub repository deployment or Azure Deployment Centre for publishing the application

The deployed environment uses these production settings:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` set to the public Azure website URL
- MySQL connection values stored in Azure App Service configuration
- Stripe and Google OAuth secrets stored in Azure App Service configuration

Database migrations and seeders are run in the Azure environment with:

```bash
php artisan migrate --force
php artisan db:seed --force
```

The included Azure notes in [docs/azure-deploy-checklist.txt](docs/azure-deploy-checklist.txt) record the deployment
configuration checks used for the project.

## Demo Accounts

### Administrator

- Email: `nikita@gmail.com`
- Password: `smiichyk123`

### Customer

- Email: `user@supermarket.test`
- Password: `user123`

## Testing

Run the automated Laravel test suite with:

```bash
composer test
```

or:

```bash
php artisan test
```

The deployed Azure application was also checked manually across the main user workflows:

- homepage and catalogue load successfully
- product search works
- product details open correctly
- registration and login work
- Google sign-in is configured or fails with a controlled message
- favourites can be added and removed
- cart quantity updates work
- checkout can create a Stripe test payment session
- customer order history loads
- administrator login works
- administrator product management works
- stock updates are saved
- order statuses can be updated
- desktop and mobile layouts remain usable

## Video Demonstration

YouTube screencast: https://youtu.be/JYDVXrErAWk

## Project Artefacts

- [Project planning document](docs/Project-planning.pdf)
- [Final project summary](docs/Final%20Project%20Summary.pdf)
- [Azure deployment checklist](docs/azure-deploy-checklist.txt)

## Project Development and Engagement

The project was developed collaboratively. Git branches were used to separate and organise development work before
changes were merged into the main project version. The CA3 work included reviewing tutor feedback, improving the user
interface, preparing deployment configuration, connecting the application to an online database, and testing the hosted
application.

This collaborative approach reflects the assignment aim of demonstrating consistent engagement, professional
development practice, and the ability to respond constructively to feedback.

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

Nikita focused mainly on the administrator-facing side of the application and the core management features. Her work
included:

- admin dashboard
- product and inventory management
- product CRUD functionality
- Stock Centre and stock alert workflows
- admin order queue and order status management
- shared admin layout and styling improvements
- initial repository setup and project documentation work

## Reflection Notes

During the deployment process, the main learning points were:

- how Laravel environment variables change between local development and production
- how a cloud-hosted Laravel application connects to an online MySQL database
- why application secrets should be stored in Azure configuration rather than committed to GitHub
- how payment and authentication services depend on correct production callback URLs
- how mobile testing can reveal layout and navigation issues that are less visible on desktop

The main risks in the project are incorrect environment configuration, missing database migrations, incorrect OAuth
redirect URLs, and Stripe webhook configuration differences between local and production environments.

## Known Limitations

- Stripe is configured for test payments rather than live payments.
- Product images depend on the available seeded dataset and may use placeholders where no specific image is available.
- Delivery route optimisation is outside the scope of this version.

## MVC Architecture

The application follows the Laravel MVC pattern:

- **Models** define the business entities and Eloquent relationships.
- **Views** render Blade templates for customer and administrator interfaces.
- **Controllers** handle requests, validation, business logic, and responses.

This structure keeps the project organised, maintainable, and easier to explain during the project defence.

## Service Layer

The project uses focused Laravel service classes for integration and payment workflow logic:

- `StripeCheckoutService` creates and retrieves Stripe Checkout Sessions, normalises Stripe responses, and validates
  webhook events.
- `OrderPaymentService` updates Stripe-related order states and restores stock when an order or payment session is
  cancelled.

Keeping this logic outside controllers makes the checkout flow easier to test and keeps controller methods focused on
HTTP requests, validation, and responses.
