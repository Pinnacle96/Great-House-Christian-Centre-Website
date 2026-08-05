# GHCC Production Checklist

## Required Server Configuration

- Use HTTPS only.
- Point the web server document root to this project directory and keep `.htaccess` enabled.
- Set `APP_ENV=production`.
- Set a strong `APP_SECRET_KEY`; this is required for encrypted admin-stored secrets.
- Use a dedicated database user with only the permissions this app needs.
- Disable public PHP error display and route errors to server logs.
- Ensure `logs/`, `assets/uploads/`, and `assets/logo/` are writable by PHP but not executable.

## Required Application Setup

- Run `composer install --no-dev --optimize-autoloader`.
- Run `npm install` and `npm run build:css` before uploading/deploying frontend assets.
- Run `php update_db.php` from CLI after deployment.
- Change the default admin password immediately.
- Configure SMTP in Admin > Global Settings and send a test email.
- Configure Paystack in Admin > Global Settings and test the connection.
- In Paystack dashboard, set the webhook URL to:
  `https://your-domain.example/give/webhook`

## Security Checks

- Confirm `/app`, `/config`, `/logs`, `/vendor`, setup scripts, seed scripts, and composer files return forbidden/not found in the browser.
- Confirm all admin POST actions require login and CSRF.
- Confirm user roles have only the expected module access.
- Confirm uploads reject non-image files and large files.
- Confirm audit logs are being written for admin POST actions.

## Operational Checks

- Schedule database backups.
- Monitor PHP/server error logs.
- Monitor `logs/communication.log`.
- Keep PHP, Composer packages, WAMP/Apache, and MySQL patched.
- Rotate SMTP and Paystack keys if staff access changes.
