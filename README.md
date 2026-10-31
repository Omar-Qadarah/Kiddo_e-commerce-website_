# Kiddo's

Kiddo's is a team PHP e-commerce project built at Orange Coding Academy. The store helps customers find children's toys by category, age, gender, and budget. It includes a customer storefront and an administration panel for managing the store.

## Features

### Customer storefront

- Browse categories and view filtered product listings.
- Filter toys by category, age, gender, and price.
- Search by product name and use search suggestions.
- Sort products and navigate paginated results.
- View product images, prices, discounts, availability, and customer reviews.
- Add products to a session-based cart and update quantities.
- Check out using cash on delivery and view order history and status.
- Register, sign in, and update account details and profile photos.
- Sign in with Google when OAuth credentials are configured.
- Use the Ask Kiddo shopping assistant when a Gemini API key is configured.

### Administration

- Dashboard with store statistics, revenue summaries, recent orders, and frequently visited actions.
- Product, category, age group, user, and discount management.
- Order status management and cancellation with stock restoration.
- Review moderation, including visibility controls and deletion.
- Search, filters, and pagination across management tables.
- Shared add, edit, and delete dialogs.
- Responsive sidebar navigation and horizontally scrollable tables on smaller screens.

## Technology

| Area | Tools |
| --- | --- |
| Backend | PHP, PDO |
| Database | MySQL / MariaDB |
| Frontend | HTML, CSS, vanilla JavaScript |
| Admin components | Bootstrap 5, Bootstrap Icons |
| Authentication | PHP sessions, password hashing, optional Google OpenID Connect |
| Shopping assistant | Gemini API |
| Local development | XAMPP, Apache, phpMyAdmin |

The project uses PHP classes for data access alongside page scripts that handle requests and render HTML. It does not require a frontend build step.

## Run locally

### 1. Requirements

The project is developed using PHP 8.2 on XAMPP. Enable the following PHP extensions:

- `pdo_mysql`
- `mbstring`
- `fileinfo` for image uploads
- `curl` and `openssl` for Google sign-in and the shopping assistant

Some fonts, icons, and Bootstrap assets load from external CDNs and require an internet connection.

### 2. Place the project in XAMPP

Clone or extract the repository into:

```text
C:\xampp\htdocs\php-project\Kiddo_e-commerce-website_
```

Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 3. Import the database

1. Open [phpMyAdmin](http://localhost/phpmyadmin).
2. Create an empty database named `kiddos` using `utf8mb4`.
3. Select the database and import [`database/kiddos.sql`](database/kiddos.sql).

The SQL export includes the schema and sample records. For an existing database, export a backup first and review the files in [`database/migrations`](database/migrations) for updates that have not yet been applied. Importing a schema export over an older database does not necessarily update existing table columns.

### 4. Check configuration

Review [`config/config.php`](config/config.php) and set the database connection values for your machine:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

The current project path is:

```php
define('BASE_URL', '/php-project/Kiddo_e-commerce-website_');
```

If you change the folder location, update `BASE_URL` to match it. Do not include `C:\xampp\htdocs` in this URL value.

Apache/PHP must be able to write to the project's `uploads` directory for image uploads.

### 5. Open the website

- Storefront: [localhost/php-project/Kiddo_e-commerce-website_/](http://localhost/php-project/Kiddo_e-commerce-website_/)
- Registration: [auth/register.php](http://localhost/php-project/Kiddo_e-commerce-website_/auth/register.php)
- Administration: [admin/index.php](http://localhost/php-project/Kiddo_e-commerce-website_/admin/index.php)

Use Apache to run the application; opening PHP files directly from the filesystem will not work.

## Administrator access

The admin panel requires an active account with `role = 'admin'`. It uses the same account session as the storefront.

For local development, register an account, then update that specific account in phpMyAdmin:

```sql
UPDATE users
SET role = 'admin'
WHERE email = 'your-admin-email@example.com';
```

Replace the example email with the account you registered. Sign in through `auth/login.php`, then open `admin/index.php`. No shared admin password is documented in this repository.

## Optional integrations

### Google sign-in

Set up a Google OAuth web client and configure these values in the project-root `.env` file or the Apache environment:

```ini
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost/php-project/Kiddo_e-commerce-website_/auth/google_callback.php
```

The redirect URI must match the value registered in Google Cloud Console. See [`GOOGLE_AUTH_SETUP.md`](GOOGLE_AUTH_SETUP.md) for the project setup notes.

### Ask Kiddo

Configure a Gemini API key in the project-root `.env` file:

```ini
GEMINI_API_KEY=your-api-key
```

The assistant uses the available product catalog to help shoppers find toys. An API key and network access are needed for responses. It does not receive customer account, order, address, or payment data as catalog context.

See [`GEMINI_CHATBOT_SETUP.md`](GEMINI_CHATBOT_SETUP.md) for integration setup. Keep `.env` private and never commit real API keys, OAuth secrets, or local credentials.

## Project structure

```text
admin/        Store management pages and actions
ai/           Shopping assistant endpoint
assets/       Stylesheets, JavaScript, and static images
auth/         Registration, login, Google sign-in, and profile
cart/         Cart display and actions
checkout/     Checkout form and order placement
classes/      Data access and application classes
config/       Application, database, and integration configuration
database/     SQL export and database migrations
includes/     Shared layouts, authentication checks, and helpers
orders/       Customer order history and details
products/     Categories, listings, search, details, and reviews
uploads/      Uploaded product, category, and profile images
index.php     Store homepage
```

## Working with the team

- Pull the latest changes before starting work.
- Keep local credentials in `.env` and out of commits.
- Share database structure changes as SQL migrations, not only through local phpMyAdmin edits.
- Back up local data before importing a different SQL export.
- Share the corresponding uploaded images when transferring sample data; an SQL export does not include image files.
- Check the affected customer and admin flows before opening a pull request.

## Common setup issues

| Issue | Check |
| --- | --- |
| Apache returns Not Found | Project folder location, URL, and `BASE_URL` |
| Database connection fails | MySQL status and the database settings in `config/config.php` |
| Unknown column error | Whether the local schema matches the current SQL export and migrations |
| Admin access is denied | The signed-in account's `role` and `is_active` values |
| Product images are missing | Whether the files exist in `uploads` and match their stored paths |
| Google sign-in fails | OAuth credentials, redirect URI, cURL, and OpenSSL |
| Ask Kiddo cannot respond | API key configuration, network access, and API availability |
