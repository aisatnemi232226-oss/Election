# University Online Election System

A Laravel-based election management and voting system for a university context. The project includes admin-side election management tools and public voter-facing pages for browsing candidates, registering for elections, casting votes, and viewing results.

This repository is a working codebase rather than a polished end-user product. The current source tree contains several implemented modules, but some flows still need runtime verification and cleanup before the project can be considered fully production-ready.

## Project status

This README reflects the current state of the codebase in this repository. It is intentionally more realistic than older handover documentation and does not assume all features are fully complete or verified in every environment.

### Included in the codebase
- Admin authentication and role-protected dashboard routes
- Candidate management (create, read, update, delete, restore, filters)
- Position management
- Election article management with rich text and image upload support
- Election party management
- Voter verification import/export workflow
- Academic year and course management
- Audit logs, reporting, notifications, and ticket features
- Public voter pages: home, election details, candidates, registration, vote, results
- Vote cast route and tally/result views
- QR login logic exists in the auth controller and should be validated in the browser

### Still needs verification or completion
- Public home route smoke test currently fails in this environment (`GET /` returns 404 during the default Laravel test)
- QR login must be tested against real QR payload examples
- Login error messaging should be checked in the browser
- Student-facing workflows depend on database state and seed data
- Final product polish, permission validation, and browser QA still need to be completed

## Tech stack
- PHP 8.2+
- Laravel 12
- MySQL
- Blade views
- Tailwind CSS via Vite
- Composer
- Node.js / NPM
- Intervention Image
- Maatwebsite Excel
- Mews Purifier

## Project structure

```text
ElectionSystem/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   ├── Imports/
│   ├── Exports/
│   └── Support/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   └── web.php
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
├── vite.config.js
├── db_electionsystem.sql
└── README.md
```

## Requirements

Before running the project, make sure you have:
- PHP 8.2 or newer
- Composer
- Node.js and NPM
- MySQL
- Git (optional, but recommended)
- Optional: Laragon for Windows local development

## Setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Configure environment

Copy the example environment file and update database values for your local setup:

```bash
copy .env.example .env
```

Then edit `.env` and configure your database settings, for example:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_electionsystem
DB_USERNAME=root
DB_PASSWORD=
```

The repository includes a default `.env` file as well, but you should verify it matches your own local database environment.

### 3. Generate application key

```bash
php artisan key:generate
```

### 4. Run database setup

If you are starting from a fresh database, run:

```bash
php artisan migrate
```

If the project includes seeded data you want to test with quickly, run:

```bash
php artisan migrate --seed
```

You may also use the supplied SQL dump at `db_electionsystem.sql` as a base for a local database import.

### Password reset email (OTP)

The forgot-password flow sends a six-digit code through the configured Laravel mailer. For Brevo SMTP, set these values in your local `.env` using the SMTP login and SMTP key from Brevo (not the API key), and use a sender address verified in Brevo:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-smtp-login
MAIL_PASSWORD=your-brevo-smtp-key
MAIL_FROM_ADDRESS=your-verified-sender@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

After changing mail settings, run `php artisan config:clear` and request another code. Never commit real mail credentials to the repository. If delivery fails, the OTP page reports that the email could not be sent; check `storage/logs/laravel.log` for the transport error.

### 5. Build frontend assets

```bash
npm run build
```

### 6. Start the app

For local development run the Vite watcher in one terminal and the Laravel server in another:

```bash
npm run dev
php artisan serve
```

Then open:

```text
http://localhost:8000
```

## Important routes

The following route groups are defined in the current codebase:

- Public home: `/`
- Login: `/login`
- Register: `/register`
- Election registration: `/registerElection`
- Election details: `/election-details`
- Vote page: `/vote`
- Results: `/result`
- Result tally: `/resultTally`
- Admin dashboard: `/admin/dashboard`

These routes exist in the source, but their runtime behavior should be validated through browser testing.

## Main feature areas

### Admin features
The current project includes admin management for:
- Elections
- Candidates
- Positions
- Election articles
- Election parties
- Verified voters
- Reports
- Audit logs
- Academic years
- Courses
- Notifications and tickets

### Student/voter features
The public-facing side includes:
- Candidate browsing
- Election detail browsing
- Candidate profile pages
- Election registration
- Vote-casting routes
- Result and tally pages

## Current known gaps

The source tree shows implemented logic, but these areas still need checking before calling the system fully complete:
- QR login flow validation
- login and registration error UI feedback
- route-level smoke testing
- missing or incomplete DB seed state in some environments
- permission checks across student/admin flows
- browser-level user testing for final workflow validation

## Validation commands

Useful commands for checking the project state:

```bash
php artisan route:list
php artisan test
php artisan serve
```

## Notes for developers

- The app is configured for MySQL in `.env` by default.
- The route file is the most important entry point for understanding the application flow.
- The README is intentionally conservative and focuses on the current codebase rather than a legacy handover story.
- If you are continuing development, keep the public voter flow and admin flow both tested after any change to routes, permissions, or election logic.

## Disclaimer

This project is a student/internship-era election system in active development. Some features are implemented and some remain under verification. Treat it as a working codebase with a realistic backlog rather than a finished production deployment.
