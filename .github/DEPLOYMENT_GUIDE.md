# Hostinger Production Deployment — Setup Guide

**Target site:** `ghcc.pinnacletechhub.com.ng`
**Hostinger SSH:** host `82.25.113.81`, **port `65002`**, user `u572560474`
**Pipeline:** push-to-`main` → GitHub Actions → Hostinger over SSH,
atomic zero-downtime release swaps, instant rollback via workflow dispatch.

---

## 1. Files created

| Path | Purpose |
|---|---|
| [`.github/workflows/deploy-production.yml`](file:///c:/wamp64/www/ghcc/.github/workflows/deploy-production.yml) | CI/CD: build, release, migrate, atomic swap to current, cleanup. |
| [`.github/workflows/rollback-production.yml`](file:///c:/wamp64/www/ghcc/.github/workflows/rollback-production.yml) | One-click manual rollback to any previous release. |

---

## 2. Deployment directory layout on Hostinger (account u572560474)

The deploy area lives **outside** the web docroot; the docroot is a symlink into it.

```
/home/u572560474/
├── deploy/ghcc/
│   ├── releases/                 # each deploy = one timestamped folder
│   │   ├── 20260805-101530-abc123/
│   │   └── 20260805-093000-def456/
│   ├── shared/                   # persists across deploys
│   │   ├── .env                  # <-- real production secrets (chmod 600)
│   │   ├── logs/
│   │   └── assets/
│   │       ├── uploads/
│   │       └── logo/
│   └── current -> releases/<active tag>     # atomic symlink
└── domains/pinnacletechhub.com.ng/public_html/
    └── __ghcc -> /home/u572560474/deploy/ghcc/current   # subdomain docroot symlink
```

### One-time server prep — run once via SSH

Connect (note the port flag `-p 65002`):

```bash
ssh -p 65002 u572560474@82.25.113.81
```

Then run:

```bash
# 1. Directory layout
mkdir -p /home/u572560474/deploy/ghcc/releases
mkdir -p /home/u572560474/deploy/ghcc/shared/logs
mkdir -p /home/u572560474/deploy/ghcc/shared/assets/uploads/sermons/audio
mkdir -p /home/u572560474/deploy/ghcc/shared/assets/logo

# 2. Real production .env (edit values!)
cat > /home/u572560474/deploy/ghcc/shared/.env <<'EOF'
APP_ENV=production
APP_URL=https://ghcc.pinnacletechhub.com.ng
APP_NAME="Great House Christian Centre"

DB_HOST=localhost
DB_NAME=REPLACE_DB_NAME
DB_USER=REPLACE_DB_USER
DB_PASS=REPLACE_DB_PASSWORD

MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USER=noreply@ghcc.pinnacletechhub.com.ng
MAIL_PASS=REPLACE_SMTP_PASSWORD
MAIL_FROM=noreply@ghcc.pinnacletechhub.com.ng
MAIL_FROM_NAME="GHCC"

PAYSTACK_PUBLIC_KEY=pk_live_xxx
PAYSTACK_SECRET_KEY=sk_live_xxx
EOF
chmod 600 /home/u572560474/deploy/ghcc/shared/.env

# 3. Verify PHP extensions the app needs
php -v && php -m | grep -Ei 'pdo_mysql|mbstring|curl|gd|openssl|zip'
```

> The **docroot symlink is created automatically** by the first successful
> deploy (it backs up any existing `__ghcc` folder to `__ghcc.bak-<tag>`).
> You do NOT need to pre-create it. Just make sure the subdomain
> `ghcc.pinnacletechhub.com.ng` exists in hPanel → Subdomains and points to
> `public_html/__ghcc` so the parent path
> `domains/pinnacletechhub.com.ng/public_html/` exists.

---

## 3. SSH deploy key

A dedicated keypair was generated for this project:

- Private key (stays local, goes into GitHub secret): [`.deploy_keys/ghcc_hostinger_deploy_ed25519`](file:///c:/wamp64/www/ghcc/.deploy_keys/ghcc_hostinger_deploy_ed25519)
- Public key (goes into Hostinger): [`.deploy_keys/ghcc_hostinger_deploy_ed25519.pub`](file:///c:/wamp64/www/ghcc/.deploy_keys/ghcc_hostinger_deploy_ed25519.pub)

**Public key line to add in Hostinger → SSH Access → Add SSH Key:**

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICbdlk4nJ7li8/UU7OcAM77SFKCn5rmtzopMqazxm/r2 gh-actions-ghcc-pinnacle96-hostinger
```

The `.deploy_keys/` folder is git-ignored so the private key is never committed.

---

## 4. GitHub Secrets (Repo → Settings → Secrets and variables → Actions)

Add these **2 required repository secrets**:

| Secret name | Value |
|---|---|
| `SSH_USER` | `u572560474` |
| `SSH_PRIVATE_KEY` | Full contents of [`.deploy_keys/ghcc_hostinger_deploy_ed25519`](file:///c:/wamp64/www/ghcc/.deploy_keys/ghcc_hostinger_deploy_ed25519), incl. the BEGIN/END lines. |

**Optional 3rd secret — `SSH_KNOWN_HOSTS`:** if you don't set it, the workflow
auto-runs `ssh-keyscan` **on the GitHub runner** (which has modern OpenSSH) to
pin the host key. Setting it explicitly is slightly more secure. To generate it,
run this on a machine with a recent OpenSSH (skip if your local `ssh-keyscan`
errors with `unsupported KEX method` — the runner will handle it):

```powershell
ssh-keyscan -p 65002 -t ed25519,ecdsa-sha2-nistp256,rsa 82.25.113.81
```

Expected output uses the bracketed host+port format:

```
[82.25.113.81]:65002 ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAA...
[82.25.113.81]:65002 ecdsa-sha2-nistp256 AAAAE2VjZHNh...
```

> Copy the private key to clipboard for pasting:
> ```powershell
> Get-Content -Raw "c:\wamp64\www\ghcc\.deploy_keys\ghcc_hostinger_deploy_ed25519" | Set-Clipboard
> ```

**Optional but recommended:** create a GitHub Environment named `production`
(Settings → Environments) with **Required reviewers**, so each deploy/rollback
pauses for your approval. Both workflows already target this environment.

---

## 5. Verify SSH works before first deploy

From your local machine:

```powershell
ssh -p 65002 -i "c:\wamp64\www\ghcc\.deploy_keys\ghcc_hostinger_deploy_ed25519" u572560474@82.25.113.81 "echo CONNECTED; php -v | head -1"
```

If it prints `CONNECTED`, the key + host + port are all correct and GitHub
Actions will authenticate the same way.

---

## 6. What each push to `main` does

1. `composer install --no-dev` + `npm ci && npm run build:css`.
2. Strip dev tools & one-off scripts (node_modules, package/composer manifests,
   `.deploy_keys`, `seed_*.php`, `fix_password.php`, etc.). Migration scripts
   (`setup_*.php`, `update_db.php`) are kept — `.htaccess` blocks web access.
3. Tar a release `YYYYMMDD-HHMMSS-<sha>`, upload as a 30-day artefact.
4. SSH to `u572560474@82.25.113.81:65002`, extract into `releases/<tag>/`.
5. Symlink shared `.env`, `logs/`, `assets/uploads/`, `assets/logo/`.
6. Run idempotent DB migrations from the new release (roles → multibranch →
   registration v1/v2 → team → update_db). All safe to re-run.
7. **Atomic swap:** `current.new` → `mv -fT` over `current`; wire
   `domains/pinnacletechhub.com.ng/public_html/__ghcc` → `current`. Zero downtime.
8. cURL smoke-check of the live URL.
9. Retention: keep newest 5 releases.

---

## 7. Rollback (no rebuild, no SSH)

GitHub → Actions → **Rollback Production** → Run workflow:

- Leave `release` blank → roll back to the immediately previous release.
- Or enter a specific tag (e.g. `20260805-093000-def456`).

It validates the target exists, atomically re-swaps `current`, and smoke-checks.
**DB is not reverted** — restore a DB backup if the bad deploy changed data.

---

## 8. Operational hygiene

- **DB backups (Hostinger → Cron jobs), nightly:**
  ```
  mysqldump -u REPLACE_DB_USER -p'REPLACE_DB_PASSWORD' REPLACE_DB_NAME | gzip > /home/u572560474/deploy/ghcc/shared/db-backups/ghcc-$(date +\%F-\%H\%M).sql.gz
  ```
  (Create `shared/db-backups/` first; add a daily cleanup keeping ~14 days.)
- **Paystack webhook URL:** `https://ghcc.pinnacletechhub.com.ng/give/webhook`
- **Upload/log permissions** (only if you hit permission errors):
  ```
  chmod -R u+rwX /home/u572560474/deploy/ghcc/shared/{logs,assets}
  ```

---

## 9. First-run checklist

1. Section 2 server prep done (dirs + real `.env`).
2. Public key added in Hostinger (Section 3); SSH test in Section 5 passes.
3. 3 GitHub secrets added (Section 4); optional `production` environment created.
4. Push `main` (or Actions → Deploy to Production → Run workflow); approve if gated.
5. Confirm `https://ghcc.pinnacletechhub.com.ng` loads and `/login` works.
6. Admin → Global Settings → SMTP test + Paystack test.
7. Test a live N100 donation → verify it appears in Finance.
