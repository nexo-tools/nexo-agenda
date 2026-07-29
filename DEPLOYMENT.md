# Deploying Nexo Agenda

This guide deploys Nexo Agenda to **shared hosting (Hostinger)** on the subdomain
`nexoagenda.alvarocdev.com`. Nothing here is Hostinger-specific at the code level —
it's a standard Laravel 13 app and moves to a VPS without changes. Where a step is
specific to shared hosting it's called out.

> Convention used below: the app lives in `~/nexo-agenda` and the subdomain's
> document root points at `~/nexo-agenda/public`. Adjust paths to your account.

## Running it locally

Before deploying anywhere, this is how to get Nexo Agenda up on your own machine. The README
points here on purpose: keeping the steps in one place is why they stopped drifting.

### Option A — everything in Docker (recommended if you just want it running)

`compose.yaml` in this repo runs the **app only**: the author's machine keeps a single
MySQL/Mailpit shared by every Nexo tool, so shipping another database per repo would be
waste. `compose.selfhost.yaml` is the overlay that fills the gap for everyone else.

```sh
cp .env.example .env
# in .env: DB_HOST=mysql  DB_PORT=3306  MAIL_HOST=mailpit  MAIL_PORT=1025
docker compose -f compose.yaml -f compose.selfhost.yaml up -d
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
npm install && npm run build
```

The app answers on **http://localhost:8101** and outgoing mail lands in Mailpit at
http://localhost:8025.

### Option B — your own MySQL

Keep `compose.yaml` alone (or no Docker at all) and point `.env` at your database:
`DB_HOST` / `DB_PORT` / `DB_DATABASE` (`nexoagenda`) / `DB_USERNAME` / `DB_PASSWORD`. Everything
else is a stock Laravel app: `composer install`, `php artisan key:generate`,
`php artisan migrate`, `npm run build`, `php artisan serve`.

> The values committed in `.env.example` target the author's shared local stack
> (`host.docker.internal:3307`). Override them — they are a default, not a requirement.

Run the suite with `vendor/bin/pest` (SQLite in memory — it never touches your database).

---

## 0. Requirements on the server

- PHP **8.3+** with the usual Laravel extensions (mbstring, intl, pdo_mysql,
  openssl, tokenizer, xml, ctype, json, bcmath, fileinfo)
- Composer (available over SSH on Hostinger)
- A MySQL database + user
- SSH access (hPanel → *Advanced → SSH Access*)
- Node is **not** required on the server if you build assets in CI / locally (see §5)

## 1. Create the subdomain and database (hPanel)

1. **Domains → Subdomains** → create `nexoagenda` under `alvarocdev.com`.
   Set its **document root to `nexo-agenda/public`** (not the default
   `public_html`). Hostinger lets you set this at creation; if not, edit it after.
2. **Databases → MySQL Databases** → create a database and a user, grant all
   privileges. Note the DB name, user, password and host (usually `localhost`).
3. **SSL** → issue a free Let's Encrypt certificate for the subdomain and enable
   **Force HTTPS**.

## 2. Get the code onto the server

Over SSH:

```bash
cd ~
git clone https://github.com/nexo-tools/nexo-agenda.git
cd nexo-agenda
```

Updates later are just `git pull` (see §9).

## 3. Install PHP dependencies (no dev, no scripts)

```bash
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
```

`--no-scripts` skips the post-install package-discovery hook, which can fail
before `.env` exists. We run discovery manually after the env is in place (§6).

## 4. Configure `.env` for production

Copy the example and edit it **on the server** (never commit a real `.env`):

```bash
cp .env.example .env
nano .env
```

Set at least:

```dotenv
APP_NAME="Nexo Agenda"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://nexoagenda.alvarocdev.com

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_AR

# Database (from step 1)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Sessions over HTTPS
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# Cache & queue on shared hosting (no Redis needed)
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail — Hostinger SMTP (create the mailbox in hPanel first)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=nexoagenda@alvarocdev.com
MAIL_PASSWORD=your_mailbox_password
MAIL_FROM_ADDRESS="nexoagenda@alvarocdev.com"
MAIL_FROM_NAME="${APP_NAME}"

# Optional "powered by" attribution footer (the LABEL is the whole phrase —
# the footer prepends nothing to it)
NEXO_ATTRIBUTION_URL=https://alvarocdev.com
NEXO_ATTRIBUTION_LABEL="powered by alvarocdev.com"

# Who operates this instance, named on /privacidad and /terminos. Set them: this
# deployment stores the name and contact of people who never signed up here (the
# clients booking with each business), so its controller has to be identifiable.
NEXO_LEGAL_OPERATOR="Alvaro C."
NEXO_LEGAL_CONTACT=nexoagenda@alvarocdev.com
```

> `MAIL_PORT=465` uses implicit TLS (`ssl`). If you prefer STARTTLS use
> `587` + `MAIL_ENCRYPTION=tls`.

Generate the app key:

```bash
php artisan key:generate
```

## 5. Front-end assets

The server has no Node in this setup, so build assets where Node is available and
ship the compiled `public/build/` directory.

**Locally (Node 20+):**

```bash
npm ci
npm run build
```

Then upload `public/build/` to the server, e.g.:

```bash
scp -r public/build USER@HOST:~/nexo-agenda/public/
```

CI already builds assets on every push — you can also download the built
`public/build` artifact from the GitHub Actions run instead of building locally.

> If your plan **does** have Node over SSH, just run `npm ci && npm run build`
> on the server and skip the upload.

## 6. Finalize package discovery, migrate, and cache

```bash
php artisan package:discover
php artisan migrate --force
php artisan storage:link

# Production caches (rebuild these on every deploy)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`--force` is required because `APP_ENV=production` makes migrations prompt
otherwise.

Optionally seed reference data — **do not** run the demo seeder in production
(it's disabled outside local, but don't invoke it).

## 7. The reminder scheduler (cron)

Nexo Agenda sends the 24h reminder via `nexo:send-reminders`, dispatched by the
Laravel scheduler (`routes/console.php`, hourly). On shared hosting, run the
scheduler every minute with a single cron entry.

hPanel → **Advanced → Cron Jobs** → add:

```
* * * * * cd ~/nexo-agenda && php artisan schedule:run >> /dev/null 2>&1
```

Laravel decides internally when the hourly command actually fires; the cron just
ticks every minute.

## 8. Verify

- Visit `https://nexoagenda.alvarocdev.com` — the landing page loads over HTTPS.
- `https://nexoagenda.alvarocdev.com/explorar` — directory renders.
- Register a business, create a service, and book a slot end-to-end.
- Confirm the confirmation email arrives (check SMTP settings if not).
- Error pages are branded (they only show when `APP_DEBUG=false`, which is the
  production setting).
- Check security headers:
  ```bash
  curl -sI https://nexoagenda.alvarocdev.com | grep -iE 'content-security-policy|x-frame|strict-transport'
  ```

## 9. Updating a live deployment

```bash
cd ~/nexo-agenda
php artisan down                 # maintenance mode
git pull
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
php artisan package:discover
# upload the freshly built public/build (or npm run build if Node is available)
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

## 10. Troubleshooting

| Symptom | Likely cause / fix |
|---|---|
| 500 with a blank page | `APP_KEY` missing, or `storage/` and `bootstrap/cache/` not writable (`chmod -R 775`). |
| Styles/JS 404 | `public/build/` not uploaded, or subdomain root isn't `nexo-agenda/public`. |
| Emails not sent | Wrong SMTP port/encryption, or mailbox not created in hPanel. Test with `php artisan tinker` → `Mail::raw(...)`. |
| Reminders never fire | Cron not added, or wrong path in the cron command. Check `storage/logs/laravel.log`. |
| Changes not showing | Stale caches — re-run the `config:cache` / `route:cache` / `view:cache` trio, or `php artisan optimize:clear`. |
| Migrations prompt / abort | Add `--force` (required in production). |

## VPS note

On a VPS, point Nginx/Apache at `public/`, use a real cron for
`schedule:run`, and optionally move `CACHE_STORE`/`QUEUE_CONNECTION`/`SESSION_DRIVER`
to Redis. The application code is unchanged.
