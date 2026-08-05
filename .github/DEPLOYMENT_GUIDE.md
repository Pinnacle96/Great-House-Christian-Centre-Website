# Hostinger Production Deployment — Setup Guide

**Target:** `ghcc.pinnacletechhub.com.ng`
**Pipeline:** push-to-`main` → GitHub Actions → Hostinger (shared hosting) over SSH,
atomic zero-downtime release swaps, instant rollback via workflow dispatch.

---

## 1. Files created

| Path | Purpose |
|---|---|
| [`.github/workflows/deploy-production.yml`](file:///c:/wamp64/www/ghcc/.github/workflows/deploy-production.yml) | CI/CD: build, release, migrate, atomic swap to current, cleanup. |
| [`.github/workflows/rollback-production.yml`](file:///c:/wamp64/www/ghcc/.github/workflows/rollback-production.yml) | One-click manual rollback to any previous or previous-but-one release. |

---

## 2. Hostinger one-time server prep (run once from the Hostinger SSH terminal)

Paths assume your Hostinger account has the deployable project placed under
`/home/ghcc/` — **adjust if Hostinger uses a different user home prefix**
(for shared hosting it is commonly `/home/u123456789/` instead).

```bash
# 1. Create the directory layout
mkdir -p /home/ghcc/{releases,shared/logs,shared/assets/uploads/sermons/audio,shared/assets/logo}

# 2. Install the real .env (replace values with production secrets)
cat > /home/ghcc/shared/.env <<'EOF'
APP_ENV=production
APP_URL=https://ghcc.pinnacletechhub.com.ng
APP_NAME="Great House Christian Centre"
APP_SECRET_KEY=REPLACE_WITH_32BYTE_RANDOM_HEX

DB_HOST=localhost
DB_NAME=ghcc_db_prod
DB_USER=ghcc_prod
DB_PASS=REPLACE_WITH_STRONG_DB_PASSWORD

MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USER=noreply@ghcc.pinnacletechhub.com.ng
MAIL_PASS=REPLACE_WITH_SMTP_PASSWORD
MAIL_FROM=noreply@ghcc.pinnacletechhub.com.ng
MAIL_FROM_NAME="GHCC Events"

PAYSTACK_PUBLIC_KEY=pk_live_xxx
PAYSTACK_SECRET_KEY=sk_live_xxx
EOF

chmod 600 /home/ghcc/shared/.env

# 3. Wire the Hostinger document root for the subdomain.
#    In Hostinger hPanel, point the subdomain ghcc.pinnacletechhub.com.ng to
#    the folder:  public_html/ghcc   (or /home/ghcc/public_html).
#    The deploy workflow then symlinks that folder -> the active release.
#    (If the folder already exists and has files, remove them first).
rm -rf /home/ghcc/public_html
ln -sfn /home/ghcc/current /home/ghcc/public_html

# 4. Verify PHP >= 8.1, curl, mbstring, PDO MySQL are enabled in hPanel PHP Options.
#    (PHPMailer needs openssl + sockets; DomPDF needs gd and mbstring.)
php -v && php -m | grep -E 'pdo_mysql|mbstring|curl|gd|openssl|zip'
```

---

## 3. SSH key pair + Hostinger authorised_keys

On your local machine (or the one you'll use to run `ssh-keygen`):

```bash
ssh-keygen -t ed25519 -C "gh-actions-ghcc-deploy" -f ~/.ssh/ghcc_deploy -N ""
```

Append the **public** half to the Hostinger user's
`/home/ghcc/.ssh/authorized_keys` (create folder/file with `chmod 700` +
`chmod 600`).

Test from your machine first before committing secrets:

```bash
ssh -i ~/.ssh/ghcc_deploy <SSH_USER>@ghcc.pinnacletechhub.com.ng "echo OK"
```

Then record the known-hosts line:

```bash
ssh-keyscan -t ed25519,ecdsa-sha2-nistp256,rsa ghcc.pinnacletechhub.com.ng
```

---

## 4. GitHub Secrets (Repo → Settings → Secrets and variables → Actions)

Create these 4 Repository Secrets:

| Secret name | Value |
|---|---|
| `SSH_USER` | Hostinger SSH username. For shared hosting this is **not** `ghcc` — it is the user shown in hPanel SSH access (e.g. `u123456789`). |
| `SSH_PRIVATE_KEY` | Full contents of the **private key file** `~/.ssh/ghcc_deploy`, including the `-----BEGIN OPENSSH PRIVATE KEY-----` header and footer. |
| `SSH_KNOWN_HOSTS` | Output of `ssh-keyscan ghcc.pinnacletechhub.com.ng` (step 3). Do NOT disable host-key verification. |

Optional (recommended): create a **GitHub Environment** called `production` with
the 4 secrets scoped to it, and enable **Required reviewers** so deployments
need a human approval before running. The workflows already reference the
`production` environment.

---

## 5. What the deploy workflow does on each push to `main`

1. Checkout, `composer install --no-dev`, `npm ci && npm run build:css`.
2. Remove dev tools (node_modules, package.json, setup/seed scripts, composer
   files, `.env.*`) so they never reach the webroot.
3. Tar up a release named `YYYYMMDD-HHMMSS-<sha>` and upload it as a
   30-day retained artefact.
4. SSH to Hostinger, extract the tar into `/home/ghcc/releases/<tag>/`.
5. Symlink in the **persistent shared files:**
   - `.env`
   - `logs/`
   - `assets/uploads/`
   - `assets/logo/`
6. Run idempotent DB migrations from the *new* release (`setup_multibranch_schema.php`,
   `update_db.php`, `schema_production_settings.php` — all safe to re-run).
7. **Atomic swap:** create `current.new` → `mv` over `current`, then repoint
   `public_html` → `current`. One `rename(2)` syscall = zero downtime.
8. Smoke-check the live URL with cURL.
9. Retention: keep the newest **5** releases; delete older ones.

---

## 6. How to roll back

**No code, no rebuild, no SSH needed.**

In GitHub → Actions → **"Rollback Production (ghcc.pinnacletechhub.com.ng)"**
→ "Run workflow":

- Leave `release` **blank** to roll back to the *previous* release (the one
  immediately before what's currently live).
- Or paste an exact release tag (e.g. `20260804-101530-abcdef`) to roll back
  to that specific release.

The workflow:
1. Validates the target release directory exists on the host.
2. Swaps `current` symlink atomically to the target release.
3. Smoke-checks the URL.

Rollback only swaps code/assets. **Database rollback is separate**: restore a
DB backup taken before the bad deploy. This workflow does **not** revert DB
changes because DDL is irreversible — keep Hostinger auto-backups / scheduled
mysqldumps enabled (see below).

---

## 7. Recommended Hostinger-side operational hygiene

- **Database backups:** schedule a nightly `mysqldump` via Hostinger Cron
  (`Advanced → Cron jobs`) writing into `/home/ghcc/shared/db-backups/`.
  Example command:
  ```
  mysqldump -u ghcc_prod -p'STRONG_PASS' ghcc_db_prod \
    | gzip > /home/ghcc/shared/db-backups/ghcc-$(date +\%F-\%H\%M).sql.gz
  ```
  Retain 14 days with a daily cleanup cron.
- **Paystack webhook URL** (hPanel dashboard):
  `https://ghcc.pinnacletechhub.com.ng/give/webhook`
- **Writable paths:** Hostinger default permissions are usually fine, but if
  you get permission errors on uploads/logs run once from the deploy user:
  ```
  chmod -R u+rwX /home/ghcc/shared/logs /home/ghcc/shared/assets/uploads /home/ghcc/shared/assets/logo
  ```
- **Restrict PHP execution inside uploads:** the project `.htaccess` in
  `assets/uploads/sermons/audio/` already does this. If you add other upload
  folders, mirror it.
- **Composer on the host is not needed.** Dependencies are bundled into the
  release artefact on the runner, which matches the exact version in
  `composer.lock`.

---

## 8. First run checklist

1. Complete section 2 + 3 on Hostinger and the deploy key.
2. Add the 4 GitHub Secrets (section 4).
3. Push this branch to `main` and approve the production environment gate (if
   reviewers are enabled).
4. In Hostinger hPanel → Subdomains → confirm
   `ghcc.pinnacletechhub.com.ng` serves from the symlinked `public_html`.
5. Visit `/login` and confirm superadmin login works.
6. Admin → Global Settings → send SMTP test + Paystack test.
7. Confirm contact form → Contact Messages arrives; confirm a live donation
   (N100 test amount) flows through Paystack and appears in Finance.
