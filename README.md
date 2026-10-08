# Walleys

Walleys is a personal finance portfolio application built with Laravel 12, PHP 8.3, Breeze (Blade), Tailwind CSS, Alpine.js, Vite, Chart.js, and MySQL. Pest tests use SQLite in memory.

## Implemented features

- Email/password registration, login, password reset, session logout, and profile/account management.
- Google sign-in with Laravel Socialite, verified provider identities, state validation, cancellation/error handling, and protection against email-based account takeover.
- System and user-owned categories, including ownership checks and deletion protection for categories in use.
- Income/expense transactions, JPG/PNG/WebP receipts, receipt replacement/deletion, filtering, and pagination.
- Current-month income, expenses, net balance in Indonesian Rupiah, six-month expense charts, and recent transactions. The monthly breakdown switches between cashflow (combined income and expense movement), income categories, and expense categories.
- Monthly category insights with ranked amounts, transaction counts, shares, and links to filtered transaction history. Category management remains available below the insights.
- Responsive app navigation, category icon cards with capitalized display names, semantic income/expense colors, inline form feedback, custom error pages, and confirmation dialogs for logout and destructive actions.
- Automated feature tests and GitHub Actions for dependency installation, tests, frontend build, and security audits.

Google OAuth implementation is covered by mocked-provider tests. A live Google login still requires your Google Cloud configuration and manual verification; automated tests do not prove that your OAuth consent screen or callback URL is configured correctly.

## Local installation

Requirements: PHP 8.3 or later, Composer 2, Node.js 24, NPM, MySQL, and PHP extensions including mbstring, fileinfo, PDO MySQL and PDO SQLite. Use PHP 8.3 for the same runtime as CI.

```bash
git clone https://github.com/jomet06/Walleys---Expense-Tracker.git
cd Walleys---Expense-Tracker
composer install
npm ci
cp .env.example .env
php artisan key:generate
```

PowerShell users can use `Copy-Item .env.example .env` instead of `cp`. Never overwrite an existing `.env`.

Create an empty MySQL database named `walleys`. Configure your private `.env`:

```dotenv
APP_NAME=Walleys
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=walleys
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Then apply pending migrations and expose uploaded receipts:

```bash
php artisan migrate
php artisan storage:link
```

For an existing installation, back up the database first and run only `php artisan migrate`. Do not run `migrate:fresh` against development or production data. The Google migration adds a unique nullable provider ID and allows a null password for Google-only users.

Seeders are optional demo data. Inspect them before running: they contain sample accounts and are not intended for production.

## Google OAuth configuration

1. In Google Cloud, configure an OAuth consent screen and create an OAuth client of type **Web application**. If the consent screen is in Testing, add your Google account as a test user.
2. Add the exact authorized redirect URI for the origin you use. For the commands above it is `http://localhost:8000/auth/google/callback`. For Laragon, use your actual hostname, for example `http://walleys.test/auth/google/callback`. Different hostnames, ports, schemes, or paths must be registered separately.
3. Set these only in your private `.env`:

```dotenv
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

4. Run `php artisan config:clear`. Use the same hostname throughout login so the session cookie and OAuth state survive the redirect.
5. Verify live flows: a new Google user, a returning Google user, cancellation, logout, and an email collision with an existing password account.

Without configuration, the Google button returns a friendly error and email/password authentication remains available. Do not send credentials through chat or commit them. Production must use HTTPS and `APP_DEBUG=false`.

Accounts are matched by the stable Google provider ID. An existing account with the same email is **never automatically linked**. Sign in using that account's existing method. Google identities cannot be changed through profile fields; the stored Google-account email is read-only. A Google-only user has no password and can use **Set a password via email** on the profile page. This sends a reset link to the stored account email and signs the user out to open the guest-only Breeze reset form. The **Forgot password** page also works when signed out. Set a password before using password confirmation for account deletion or password changes. Configure a real mail transport for those flows. With `MAIL_MAILER=log` during local development, reset links are written to `storage/logs/laravel.log`.

OAuth access/refresh tokens are not stored because Walleys does not call Google APIs after authentication.

Reference: [Laravel Socialite](https://laravel.com/docs/12.x/socialite) and [Google web-server OAuth setup](https://developers.google.com/identity/protocols/oauth2/web-server).

## Development

Run in two terminals:

```bash
php artisan serve
```

```bash
npm run dev
```

Open the host configured in `APP_URL`. Alternatively, `composer run dev` starts the server, queue listener, log viewer, and Vite together.

## Tests and production build

```bash
php artisan test
npm run build
composer audit
npm audit
npm audit --omit=dev
```

`phpunit.xml` forces `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:`. The test bootstrap refuses to run database tests with another database configuration. Google tests mock Socialite and require no real credentials or network calls.

The CI workflow runs PHP 8.3 and Node 24, installs locked dependencies, generates a temporary application key, builds Vite assets, and runs the complete Pest suite. It also audits Composer, production NPM dependencies, and rejects critical frontend advisories.

Some development-only Tailwind 3 dependencies have high/moderate audit advisories without a compatible upstream fix. They are reported by `npm audit`; they are not suppressed. Do not run `npm audit fix --force` blindly, as it replaces Tailwind 3 with Tailwind 4 and requires a CSS/build migration. The critical shell-quote issue is addressed by a patched compatible override.

## Storage and data behavior

Amounts are integer Rupiah, with no fractional currency values. Transaction input is bounded to the existing MySQL integer column. Analytics use `transaction_date` and `categories.type`, and all queries are scoped to the authenticated user. Category type changes affect how existing transactions are classified. Uploaded receipts use the public disk: treat receipt URLs as public files and avoid uploading sensitive documents. Account deletion also removes that user's receipt files.

Browser smoke testing and live Google verification must be recorded separately from automated tests before the final release is approved.
