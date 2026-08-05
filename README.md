# Great House Christian Centre CMS

A custom PHP church website and administration system for Great House Christian Centre.

The project is intentionally lightweight: no Laravel, no framework bootstrapping, and no build pipeline. It uses a small MVC-style structure, PDO for MySQL access, PHP views, PHPMailer for email, Dompdf for reports, and Paystack for online giving.

## What This Application Covers

- Public website pages: home, about, ministries, groups, services, sermons, events, giving, and contact.
- Admin dashboard with role-based module access.
- Member portal for role 4 users at `/member`.
- Member, family, group, ministry/team, service-planning, event, sermon, finance, registration, prayer, contact-message, and communication workflows.
- Event registration with check-in, CSV export, PDF export, reminder emails, and registration confirmation emails.
- Online giving through Paystack callback and webhook verification.
- CMS-managed page content and global settings.
- SMTP and Paystack configuration from the admin settings screen.
- CSRF protection, admin access checks, upload validation, session hardening, basic rate limiting, and audit logging for admin POST actions.

## Stack

- PHP 8.x
- MySQL / MariaDB
- Apache with `.htaccess` rewrite support
- Composer
- Node.js/npm for building CSS
- PHPMailer
- Dompdf
- Paystack API
- Tailwind CSS compiled locally to `css/style.css`

## Directory Layout

```text
app/
  Controllers/       Request handlers for public and admin routes
  Core/              Router, controller base class, database, security helpers
  Models/            Thin PDO-backed table models
  Services/          Email, communication, and Paystack integrations
  Views/             PHP templates

config/
  config.php         Environment/config bootstrap
  schema_*.sql       Database schema fragments used by update_db.php

assets/              Images, logos, uploads
css/                 Site CSS
  tailwind.css       Tailwind source file
  style.css          Compiled stylesheet served by the app
js/                  Site JavaScript
routes/web.php       Route definitions
update_db.php        CLI migration/update runner
tailwind.config.js   Tailwind build configuration
```

## Local Setup

Install dependencies:

```bash
composer install
npm install
npm run build:css
```

Create a MySQL database, then configure `.env` from the example:

```bash
cp .env.example .env
```

At minimum, set:

```env
APP_ENV=local
APP_URL=http://localhost/ghcc
APP_SECRET_KEY=use-a-strong-random-secret

DB_HOST=localhost
DB_NAME=ghcc_db
DB_USER=root
DB_PASS=
```

For a fresh database, import the base schema first, then apply the update scripts:

```bash
mysql -u root -p ghcc_db < config/schema.sql
php update_db.php
php setup_registration_schema_v2.php
php setup_team_schema.php
php add_slug_to_events.php
```

For an existing database, take a backup first, then run:

```bash
php update_db.php
```

The default seeded admin account is:

```text
Email: admin@ghcc.org
Password: password123
```

Change this password immediately after login.

## Shared Hosting Deployment

This application is compatible with typical cPanel/shared-hosting Apache environments, provided the host supports PHP 8.x, MySQL, Composer dependencies, and `.htaccess`.

Recommended deployment steps:

1. Upload the project files to the hosting account.
2. Run `composer install --no-dev --optimize-autoloader` locally before upload if the host does not allow Composer.
3. Run `npm install` and `npm run build:css` locally before upload if the host does not allow Node/npm.
4. Create the production MySQL database and database user.
5. Create a `.env` file in the project root.
6. Set the correct production `APP_URL`.
7. Import `config/schema.sql` for a fresh database.
8. Run `php update_db.php` from SSH/terminal if the host allows CLI access.
9. If there is no SSH access, run migrations locally against the production database only if your host allows remote MySQL securely. Otherwise, import the SQL files manually in phpMyAdmin.
10. Confirm `.htaccess` is active.
11. Login, change the admin password, configure SMTP and Paystack, then run both test buttons from Admin > Global Settings.

Example production `.env`:

```env
APP_ENV=production
APP_URL=https://your-domain.com
APP_NAME="Great House Christian Centre"
APP_SECRET_KEY=Qm8x7Vn4rKp2zYt9Ld6Hs3FaWc5Nj0Eu

DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_database_user
DB_PASS=your_database_password

MAIL_HOST=smtp.your-domain.com
MAIL_PORT=587
MAIL_USER=noreply@your-domain.com
MAIL_PASS=your_smtp_password
MAIL_FROM=noreply@your-domain.com
MAIL_FROM_NAME="GHCC Events"

PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxxxxxxxxxx
```

If the app is deployed in a subfolder, include the folder in `APP_URL`:

```env
APP_URL=https://your-domain.com/ghcc
```

## Paystack Configuration

Set these in Paystack after deployment:

```text
Callback URL:
https://your-domain.com/give/callback

Webhook URL:
https://your-domain.com/give/webhook
```

For subfolder deployments:

```text
Callback URL:
https://your-domain.com/ghcc/give/callback

Webhook URL:
https://your-domain.com/ghcc/give/webhook
```

The webhook endpoint verifies the `x-paystack-signature` header using the configured Paystack secret key.

## SMTP Configuration

SMTP can be configured in Admin > Global Settings.

After saving SMTP values, use the **Send SMTP Test Email** button. The application sends the test to the configured Site Email.

If the test fails, check:

- SMTP host and port
- Encryption mode: TLS or SSL
- Username and password
- Whether the hosting provider blocks outbound SMTP
- `logs/communication.log`
- Server/PHP error logs

## Roles

The seeded roles are:

- Admin
- Pastor
- Department Leader
- Member
- Registration Manager
- Registration Team

Current backend access is role-gated by controller. The `Member` role uses the separate member portal instead of the admin dashboard.

Member users are redirected to `/member` after login. The member portal links the user account to the member profile by email address and shows profile details, groups, event registrations, and giving history.

## Demo Role Accounts

For staging/demo use, seed one sample account per role:

```bash
php seed_sample_role_accounts.php
```

The script creates or updates these accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@ghcc.org` | `password123` |
| Pastor | `pastor@ghcc.org` | `password123` |
| Department Leader | `leader@ghcc.org` | `password123` |
| Member | `member@ghcc.org` | `password123` |
| Registration Manager | `registration.manager@ghcc.org` | `password123` |
| Registration Team | `registration.team@ghcc.org` | `password123` |

The member demo account is also linked to a sample member profile. Do not leave these credentials unchanged on a public production deployment.

## Security Notes

The application includes:

- CSRF protection for browser POST requests.
- CSRF exemption only for the Paystack webhook endpoint.
- Session cookie hardening.
- Session ID regeneration on login.
- Basic rate limiting for login and public submission flows.
- Controller-level admin role checks.
- MIME and size validation for image uploads.
- Admin audit logging for authenticated admin POST actions.
- `.htaccess` blocks for `app`, `config`, `logs`, `vendor`, `.env`, Composer files, setup scripts, and seed scripts.

For production, also confirm the server is configured correctly:

- HTTPS is enabled and forced.
- Directory listing is disabled.
- PHP execution is disabled inside upload directories if the host allows that control.
- Database backups are scheduled.
- The default admin password has been changed.
- `APP_ENV=production`.
- `APP_SECRET_KEY` is set before saving secrets in the admin settings screen.

## Operational Files

- `logs/communication.log`: email/SMS delivery attempts and errors.
- `audit_logs` table: admin POST activity.
- `PRODUCTION_CHECKLIST.md`: deployment and launch checklist.

## CSS Build

Tailwind is compiled before deployment. The application serves only `css/style.css`; it does not load Tailwind from the browser CDN.

Build once:

```bash
npm run build:css
```

Watch during UI development:

```bash
npm run watch:css
```

For shared hosting, upload the compiled `css/style.css`. `node_modules` does not need to be uploaded.

## Production Verification

Before launch, verify:

```text
/                  returns 200
/contact           returns 200
/give              returns 200
/login             returns 200
/admin             redirects to login when signed out
/app               forbidden or not found
/config            forbidden or not found
/vendor            forbidden or not found
/.env              forbidden or not found
```

Then test these flows manually:

- Admin login
- Change admin password
- Save global settings
- Send SMTP test email
- Test Paystack connection
- Submit contact message
- Submit prayer request
- Create event
- Register for event
- Check in registration
- Export registrations
- Create manual finance entry
- Create Paystack test donation

## Maintenance

- Keep PHP and Composer dependencies patched.
- Rotate SMTP and Paystack credentials when staff access changes.
- Review audit logs periodically.
- Back up the database before running migrations.
- Keep `APP_SECRET_KEY` stable after launch. Changing it will prevent previously encrypted DB secrets from being decrypted.
