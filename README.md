# Supermarket Store Web Platform (Laravel MVC)

## 1. Project Overview

This project is a dynamic, data-driven supermarket management and shopping web application built with the Laravel
framework using the MVC (Model-View-Controller) architecture.

The system addresses common operational issues in small-to-medium supermarkets, where product data, stock visibility,
customer orders, and user management are often handled through disconnected tools or manual workflows.

The platform provides one structured environment for both customers and staff:

- Customers can register, log in, browse products, search by category, add items to a cart, and place orders.
- Staff and administrators can securely manage products, categories, inventory, and user access through role-based
  permissions.

The core objective is to demonstrate practical server-side development skills through a realistic business scenario
while implementing reliable CRUD operations, authentication, authorization, database design, and validation.

## 2. Problem Statement

Many supermarkets need a simple but robust online platform that supports both public shopping features and internal
management operations.

Common issues include:

- No centralized product and inventory database.
- Manual stock tracking that causes out-of-stock confusion.
- Limited visibility of order history for customers and staff.
- Weak access control where all users have identical permissions.
- Poor data validation that causes inconsistent records.

This project solves these issues with a Laravel-based platform featuring clear user roles, controlled access, structured
data relationships, and validated workflows.

## 3. Target Users and Roles

### Guest

- Can view public pages and browse the product catalog.
- Cannot place orders or access account features.
- Is encouraged to register for full functionality.

### Customer

- Can register, log in, and manage profile details.
- Can browse products, use filters/search, add items to cart, and place orders.
- Can view personal order history and order status.

### Staff (Optional Role)

- Can monitor orders and update order status.
- Can view product and stock information relevant to store operations.
- Cannot perform high-risk administrative actions unless explicitly authorized.

### Admin

- Has full management access to products, categories, users, and core system content.
- Can perform CRUD actions for key entities.
- Controls role assignments and access policies.

## 4. Project Scope

### Included Scope

- Authentication (registration, login, logout, password hashing).
- Authorization through roles and middleware/policies.
- At least one fully implemented CRUD module (Products).
- Relational database design with migrations and seed data.
- Customer-facing product browsing and checkout flow.
- Validation on both client side and server side.
- Minimum of five views/pages with consistent navigation and feedback messages.

### Excluded Scope

- Real payment gateway integration.
- Delivery route optimization.
- Multi-language localization.
- External ERP/accounting integrations.

## 5. Core Functional Features

### User Account Management

- User registration with unique email constraint.
- Secure login/logout flow.
- Password hashing using Laravel defaults.
- Basic profile management.

### Role-Based Access Control

- Route protection using middleware.
- Permission boundaries between guest/customer/admin.
- Admin-only access for management pages.

### Product Catalog

- Product list page with pagination.
- Product details page with image, generated product summary, price, and stock state.
- Category-based filtering and keyword search.

### Shopping Cart and Checkout

- Add, remove, and update cart items.
- Quantity controls with stock-aware limits.
- Checkout creates an order and order items snapshot.

### Product Management (CRUD)

- Admin can create products with validated fields.
- Admin can edit product details and stock quantity.
- Admin can soft-delete or deactivate products.
- Admin can view product records in table/list format.

### Category Management (Optional but Recommended)

- Create, update, and manage categories.
- Assign products to categories for organized browsing.

### Order Management

- Customers can view their own orders and details.
- Admin/staff can review order lists and statuses.
- Status lifecycle examples: `pending`, `confirmed`, `completed`, `cancelled`.

## 6. Suggested Data Model (Relational Database)

Suggested normalized schema:

- `users`
- `roles`
- `role_user` (or equivalent role mapping table)
- `categories`
- `products`
- `carts`
- `cart_items`
- `orders`
- `order_items`

Key relationships:

- One role to many users (or many-to-many, depending on implementation).
- One category to many products.
- One user to many orders.
- One order to many order items.
- One product to many order items.

## 7. Validation and Business Rules

Validation is enforced on both frontend and backend for reliability and security.

### Server-Side Validation Examples

- Product name: required, length constraints.
- Price: required, numeric, positive.
- Stock: required, integer, non-negative.
- Email: required, unique, valid format.
- Password: required, minimum complexity.

### Business Rule Examples

- Customers cannot order a quantity above available stock.
- Only admins can access product creation and deletion routes.
- Order total is calculated from order items at checkout time.
- Deleted/inactive products are hidden from the public catalog.

## 8. Security and Authorization

Security is a core requirement:

- CSRF protection for all form submissions.
- Password hashing via Laravel authentication stack.
- Input sanitization and validation.
- Route-level protection via middleware.
- Policy/gate checks for sensitive actions.
- Session-based authentication with secure logout behavior.

## 9. User Interface and Pages

Minimum set of views/pages includes:

- Home page
- Product catalog page
- Product details page
- Cart page
- Checkout page
- Login page
- Registration page
- Admin dashboard
- Admin products management page

UX principles:

- Clear navigation and task flow.
- Action feedback (success/error alerts).
- Validation messages near relevant form fields.
- Consistent layout and component behavior.

## 10. Technical Architecture (Laravel MVC)

The project follows Laravel MVC conventions:

- Models define business entities and Eloquent relationships.
- Views render Blade templates for customer and admin interfaces.
- Controllers handle requests, validation, service logic, and responses.
- Migrations manage schema changes version by version.
- Middleware enforces access control and request filtering.
- Routes separate public and protected endpoints.

This architecture supports maintainability, testability, and clear separation of concerns.

## 11. Assumptions and Limitations

### Assumptions

- Users have a stable internet connection and modern browsers.
- Product images are stored locally or through configured storage.
- Payment is simulated (no real transaction processing).

### Limitations

- No advanced analytics dashboard in the initial release.
- No mobile app client (web only).
- No third-party logistics integration in core scope.

## 12. Future Improvements
