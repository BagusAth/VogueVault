<h1 align="center">VogueVault</h1>

VogueVault is a fashion-focused e-commerce experience built with Laravel 12. The project combines a curated storefront, intuitive shopping flow, and an admin workspace for managing products, categories, and customer orders.

## Features

- Home landing page that highlights new arrivals and active categories with product counts and hero imagery.
- Advanced product search with keyword tokenization across names, descriptions, and category metadata.
- Category browsing and product detail pages with multi-image galleries, variant selectors, specifications, and "Buy Now" shortcuts.
- Persistent shopping cart with variant-aware line items, quantity editing, AJAX-friendly updates, and one-click clear.
- Checkout review page featuring address book management (add, edit, delete, set default) and payment method selection (GoPay, ShopeePay, QRIS, Virtual Account).
- Secure order creation supporting both full cart checkout and "Buy Now" flows, including inventory deduction and payment countdown timer.
- Customer dashboard to review past orders, monitor fulfillment status, and fetch live updates via JSON endpoints.
- Admin module for managing products, categories, and order status transitions, protected by `auth` + `admin` middleware stack.
- Media controller that serves stored assets with long-lived caching headers for optimal storefront performance.
- Tailored UI built with Bootstrap 5, custom CSS, and Vite-powered asset pipeline.

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.2)
- **Frontend tooling:** Vite, Tailwind CSS 4 plugins, Bootstrap 5 components
- **Database:** MySQL or compatible (configured via `.env`)
- **Authentication:** Laravel Breeze-style session auth (login/register)
- **Build tools:** Composer, NPM, Laravel Pint (formatting), PHPUnit (tests)

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 18+ with npm
- MySQL 8+ (or MariaDB equivalent)
- Git

### Clone & Install

```bash
git clone https://github.com/BagusAth/VogueVault.git
cd VogueVault

cp .env.example .env   # Windows PowerShell: copy .env.example .env

composer install
php artisan key:generate

# Update .env with your database credentials before continuing
php artisan migrate
php artisan db:seed --class=VogueVaultSeeder

php artisan storage:link

npm install
```

### Running the App

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Visit `http://127.0.0.1:8000` to explore the storefront. Vite handles hot module replacement for CSS/JS changes.

### Example Accounts

- **Admin:** `admin@voguevault.com` / `password`
- **Customer:** `customer@example.com` / `password`

Update or remove these seeded records before deploying to production.

## Project Scripts

- `composer run dev` – optional helper that launches PHP server, queue listener, pail log viewer, and `npm run dev` concurrently.
- `npm run dev` – start the Vite development server.
- `npm run build` – compile and version production assets.
- `composer run test` – clear config cache and execute the Laravel test suite.

## Testing

```bash
php artisan test
```

Add your own feature and unit tests under `tests/Feature` and `tests/Unit` to safeguard future changes.

## Contributing

1. Fork the repository and create a feature branch.
2. Make your changes with appropriate tests.
3. Run `composer run test` and `npm run build` to validate.
4. Submit a pull request describing the enhancements.
