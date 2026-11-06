# Walleys — Personal Finance & Expense Tracker

Walleys is a Laravel application for managing income, expenses, categories, and receipts. It pairs a Blade dashboard with user-scoped database access, Google OAuth identity checks, automated feature tests, and GitHub Actions CI.

[Features](#key-features) · [Engineering](#technical-highlights) · [Setup](#installation-and-setup) · [Google OAuth](#google-oauth) · [Testing and CI](#testing-and-ci) · [Status](#project-status-and-known-limitations)

## Key features

- Income/expense transactions with receipt uploads, date/type/category filters, and pagination.
- System categories and user-owned categories, with deletion protection for categories already used by transactions.
- Monthly income, expenses, and net balance in Indonesian Rupiah; six-month charts and ranked category insights with links to transaction history.
- Email/password registration, password reset, logout, profile management, and Google sign-in.
- Responsive navigation, form feedback, custom error pages, and confirmation dialogs for destructive actions.

## Technology stack

- **Backend:** Laravel 12, PHP 8.3, Eloquent ORM, Laravel Socialite.
- **Frontend:** Blade with Laravel Breeze scaffolding, Tailwind CSS 3, Alpine.js, Chart.js, Vite.
- **Database:** MySQL for application data; in-memory SQLite for automated tests.
- **Quality checks:** Pest, GitHub Actions, Composer and NPM dependency audits.

## Technical highlights

- **Ownership at the server boundary:** [transaction queries](app/Http/Controllers/TransactionController.php) start from the authenticated user's relationship. [Request authorization and validation](app/Http/Requests/TransactionRequest.php) reject other users' transactions and categories, preventing access through manually changed resource IDs.
- **Explicit identity checks:** [Google authentication](app/Http/Controllers/Auth/GoogleAuthController.php) validates OAuth state and verified provider identity, matches the stable provider ID, regenerates the session, and refuses to automatically link an existing account solely because its email matches. OAuth tokens are not retained because no further Google API access is needed.
- **Receipt lifecycle:** uploaded images are validated; replacement/deletion removes old files, and a failed transaction update cleans up its newly uploaded file.
- **Consistent financial data:** amounts are integer Rupiah, bounded to the existing MySQL column. Dashboard analytics use transaction dates and category types, with queries scoped to the signed-in user.
- **Regression coverage:** [transaction tests](tests/Feature/TransactionTest.php), [category tests](tests/Feature/CategoryTest.php), and [Google authentication tests](tests/Feature/Auth/GoogleAuthenticationTest.php) exercise ownership, validation, file handling, and provider failure cases.

## Installation and setup

Use PHP **8.3** to match CI, Composer 2, Node.js **24**, NPM, MySQL, and PHP extensions including mbstring, DOM, fileinfo, PDO MySQL, and PDO SQLite. Development test dependencies require PHP 8.3 even though the root Composer PHP constraint is broader.

```bash
git clone https://github.com/jomet06/Walleys---Expense-Tracker.git
cd Walleys---Expense-Tracker
composer install
npm ci
```

For a fresh clone, copy `.env.example` to `.env` (`cp .env.example .env`, or `Copy-Item .env.example .env` in PowerShell). Never overwrite an existing environment file. Create an empty local MySQL database and configure:

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

```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
```

Run these in separate terminals:

```bash
php artisan serve
```

```bash
npm run dev
```

Open the host configured in `APP_URL`. `composer run dev` is an alternative that also starts the queue listener and log viewer.

For an existing installation, back up the database and apply only pending migrations. Do not use `migrate:fresh` against data you want to keep. Seeders contain optional demonstration accounts; inspect them before using them on an isolated local database.

## Google OAuth

1. Configure a Google Cloud OAuth consent screen and a **Web application** client. Add your account as a test user if the consent screen is in Testing.
2. Register the exact callback URI for your chosen origin, such as `http://localhost:8000/auth/google/callback`. A Laragon hostname needs its own callback registration.
3. Set your private environment values:

```dotenv
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

4. Run `php artisan config:clear` and use the same hostname throughout sign-in so session-backed state can be validated.
5. Manually verify new/returning users, cancellation, logout, and an email collision with an existing password account.

Without provider configuration, the Google button returns a helpful error while email/password sign-in remains available. Automated OAuth tests mock Socialite; they do not verify a deployed consent screen or callback configuration.

<details>
<summary>Account linking and password behavior</summary>

Google accounts are matched by provider ID. An existing email account is never automatically linked: sign in with its existing method instead. The stored Google email is read-only and provider identity cannot be changed through profile input.

A Google-only user initially has no password. **Set a password via email** sends a reset link to the stored account email and signs the user out to open the guest-only reset form; **Forgot password** also works when signed out. Set a password before using password confirmation for password changes or account deletion.

Configure a real mail transport for email flows. With `MAIL_MAILER=log`, development reset links are written to `storage/logs/laravel.log`; keep these logs private. Production requires HTTPS and `APP_DEBUG=false`.

</details>

Provider references: [Laravel Socialite](https://laravel.com/docs/12.x/socialite) and [Google web-server OAuth](https://developers.google.com/identity/protocols/oauth2/web-server).

## Testing and CI

```bash
php artisan test
npm run build
composer audit
npm audit
npm audit --omit=dev
```

[`phpunit.xml`](phpunit.xml) forces in-memory SQLite; the test bootstrap in [`tests/TestCase.php`](tests/TestCase.php) refuses another database configuration. Google tests use a mocked provider and need no real credentials or network calls.

The [CI workflow](.github/workflows/ci.yml) runs PHP 8.3 and Node.js 24, installs locked dependencies, builds Vite assets, runs the Pest suite, and audits dependencies, including a critical-advisory gate for frontend packages. A [successful run for commit `fc8c627`](https://github.com/jomet06/Walleys---Expense-Tracker/actions/runs/37805728604) is available; this is evidence for that commit, not a guarantee for later changes.

## Project status and known limitations

- Portfolio application with implemented features and automated checks; live Google login and browser acceptance testing remain separate from mocked tests.
- Receipts use the public storage disk. Treat their URLs as public files and do not upload sensitive documents; account deletion also removes the user's receipt files.
- Changing a category's type changes how its existing transactions are classified.
- The existing documentation records development-only Tailwind 3 dependency advisories. Audit output can change; rerun checks rather than assuming a past successful CI run means there are no current advisories. Do not use `npm audit fix --force` without reviewing the resulting framework/build changes.
- The tracked images are logos, not application screenshots. No screenshot or verified public demo is advertised.
