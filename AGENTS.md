# Nexo Agenda

> Entry point for any AI/agent working on this project. Follows Alvaro's standards
> system (repo `alvaro`, alvarocdev.com). Keep this file up to date: persist here
> the important context that surfaces during work sessions. This is a **public,
> open source** repository — all documentation is in English and must never
> contain secrets, internal hostnames/paths, or personal/customer data.

## What this project is

Open source, self-hosted booking platform for service businesses (salons, clinics,
studios, consultants) — the "open source AgendaPro". Multi-business: any business
registers and gets a public booking page at `/{slug}` where clients book in seconds
with no account (guest flow + hashed magic link). Second product of the **Nexo**
family (sibling of Nexo Links). **State: in production** (live at
nexoagenda.alvarocdev.com) and public on GitHub.

Full product context lives in `docs/`: [SCOPE](docs/SCOPE.md) (value prop, domain
model, roadmap), [DECISIONS](docs/DECISIONS.md) (17+ decisions with rationale),
[BRAND](docs/BRAND.md), [WIREFRAMES](docs/WIREFRAMES.md).

## Stack

- **PHP 8.3+** (production runs 8.5), **Laravel 13**
- **Blade + Alpine.js 3 + Tailwind CSS 4** (Vite 8)
- **MySQL** in dev/prod (portable to any Laravel-supported DB)
- **Pest** (147 tests / 386 assertions), **Pint**, **Larastan** level 6
- `bacon/bacon-qr-code` for QR check-in. Zero external runtime requests (no CDNs,
  no Google Fonts; fonts self-hosted, system stack fallback)
- Hosting: **Hostinger shared hosting** (LiteSpeed). See [DEPLOYMENT.md](DEPLOYMENT.md).

## How to run

There is **no local PHP/Composer/artisan** on the dev machine — Composer runs via
Docker, and the app runs via Laravel Sail. Node is local (v20).

```bash
# Install PHP deps (no local PHP)
docker run --rm -v "$PWD":/app -w /app composer:latest install

# Boot the stack (app :8080, MySQL :3307, Mailpit :8025)
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

# Front-end (Node 20+ local)
npm install && npm run build

# Optional full demo business (demo@nexoagenda.test / password → /estudio-nexo)
./vendor/bin/sail artisan db:seed --class=DemoSeeder
```

App at http://localhost:8080. Local login for the older demo: `e2e@test.com` /
`password123` (Barberia Demo at `/barberia-demo`).

### Checks (must be green before every commit)

```bash
docker run --rm -v "$PWD":/app -w /app composer:latest sh -c \
  "vendor/bin/pint --test && vendor/bin/phpstan analyse --no-progress && vendor/bin/pest"
node scripts/generate-translations.mjs --check
npm run build
```

### Asset / i18n scripts (Node, local)

- `npm run build` — Vite build (outputs `public/build`, gitignored)
- `npm run brand` — regenerate favicons/OG/manifest from `resources/brand/isotype.svg`
- `npm run translations` — rebuild `lang/{en,pt}.json` from source `__()` strings

## Production

Live at **https://nexoagenda.alvarocdev.com** on Hostinger shared hosting.
The full, reproducible deploy guide is [DEPLOYMENT.md](DEPLOYMENT.md). Key facts:

- The subdomain document root is a **symlink to the app's `public/`** folder (the
  Laravel app lives outside the web root), mirroring the sibling Nexo Links deploy.
- Node is not available on the server: **assets are built locally/in CI and the
  compiled `public/build/` is uploaded** (scp).
- The 24h reminder runs via the Laravel scheduler; on shared hosting a cron runs
  `php artisan schedule:run` every minute.
- CI (GitHub Actions) runs Pint + Larastan + translations `--check` + build + Pest
  on every push/PR.

### Hostinger / LiteSpeed gotchas (learned in production, 2026-07-17→19)

- **`php artisan storage:link` fails** — the host disables PHP `exec()`. Create the
  symlink manually: `ln -s "$PWD/storage/app/public" "$PWD/public/storage"`.
- **Mail uses `MAIL_SCHEME=smtps`** for Hostinger SMTP on port 465 (Laravel 13's
  smtp mailer reads `MAIL_SCHEME`, **not** `MAIL_ENCRYPTION`).
- **LiteSpeed / Force-HTTPS injects a weak `Content-Security-Policy:
  upgrade-insecure-requests`** that replaces the app's strict CSP. It's re-asserted
  at the web-server level in `public/.htaccess` (`Header always unset` + `set`) so
  the strict policy wins — keep it in sync with `app/Http/Middleware/SecurityHeaders.php`.
- Production caches must be rebuilt on deploy: `config:cache` + `route:cache` +
  `view:cache`; run `package:discover` after a `--no-scripts` composer install.

## Project conventions

General standards (branding, docs, quality, language) live in the `alvaro` repo's
`principles/how-i-work.md`. What's specific to **this** project:

- **One commit per phase**, Conventional Commits in English, **push immediately
  after each commit**. Run the checks above + real end-to-end HTTP verification
  before committing.
- **i18n**: every user string goes through `__('literal spanish')` (base locale
  `es`). Variables inside `__($x)` are **not** detected by the generator. After
  adding strings, update `scripts/translations/{en,pt}.json` and run
  `npm run translations`; a guardian test + CI `--check` enforce sync.
- **Never cache Eloquent models** — cache plain arrays of primitives (decision #17);
  cached models can unserialize incomplete and 500 the page. See
  `app/Services/PublicPageCache.php`.
- **CSP** intentionally uses `'unsafe-eval'` (Alpine) and `'unsafe-inline'` styles
  (decision #16); no inline `<script>` exists. Don't migrate to Alpine's CSP build.
- Branded/translated **error pages only render with `APP_DEBUG=false`** (local shows
  Ignition; covered by tests).
- Tests force `Accept-Language: es` in `tests/TestCase.php` (requests default to
  `en-us`) — don't change it.
- Larastan can't infer casts/columns → use `@property` docblocks.
- Factories set explicit defaults (in-memory has no DB defaults); tests hitting
  ICS/CSV use fixed names (Faker injects commas).
- `$request->string()` returns a `Stringable` (an object — always truthy): for
  values that get persisted, compared, or passed to `Mail::to()`, use
  `$request->validated()` / `->input()` instead (a `?: null` on a Stringable
  never fires — this caused a real bug in phase 1.8).
- Don't compare date-cast attributes with `where('col', $value)`: the cast
  serializes to `Y-m-d 00:00:00`, which breaks equality against DATE columns on
  SQLite (tests). Use `whereDate()` (see `WaitlistController`).
- The `composer:latest` container (our test runner) has **no GD and no Node**:
  `UploadedFile::fake()->image()` throws — use `createWithContent()` with real
  tiny PNG bytes; Node-dependent tests must `skip()` when `node` is absent.
- `sail:install` **rewrites `phpunit.xml`** (points tests at MySQL). If it ever
  re-runs, restore `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:`.
- The brand generator inlines `resources/brand/isotype.svg` stripping the root
  `<svg>` tag, so `fill`/`stroke` must live **on each path**, never on the root
  (a root-level `fill="none"` gets lost and paths render filled black).
- The web server serves static files in `public/` before routes — avoid adding
  files whose names collide with routes (`robots.txt`/`sitemap.xml` are dynamic
  routes; the static `public/robots.txt` was removed).
- **Branding/attribution** is env-configurable (`NEXO_ATTRIBUTION_URL` /
  `NEXO_ATTRIBUTION_TEXT`, `config/nexo.php`) so third-party instances stay neutral;
  Alvaro's instance sets the "powered by alvarocdev.com" values. This is the
  open-source-multi-instance form of the standard branding footer — the shared
  component is intentionally **not** hardcoded.

## Architecture (orientation, not a substitute for the code)

- `app/Services/` — domain logic kept out of controllers: `Availability`
  (timezone-aware slot engine), `PublicPageCache`, `BusinessStats`,
  `ClientDirectory` (light CRM aggregated from bookings — there is no `Client`
  model), `IcsFile`, `QrSvg`, `WaitlistNotifier`.
- `app/Http/Requests/` Form Requests + `app/Policies/` ship with each feature (not
  retrofitted). `app/Http/Middleware/`: `SecurityHeaders`, `SetLocale`.
- Public endpoints are rate-limited as they're added; magic-link tokens stored
  hashed (sha256).

## Decisions

Decision log with rationale is in [docs/DECISIONS.md](docs/DECISIONS.md) — don't
duplicate it here; append new decisions there via `docs:` commits.

- **2026-07-19** — Created `AGENTS.md` (standards-system validation, first pass).

## Accumulated context

<!-- Newest first, dated. Persist non-obvious context for the next session. -->

- **2026-07-19** — Context extraction from the Stage 0–2 build session: added the
  Stringable/`whereDate`/GD/`sail:install`/SVG-inlining gotchas to the
  conventions above. Also for the record: `laravel-lang` was tried and **doesn't
  support Laravel 13 yet** (that's why translations are generated by our own
  script), and the one red CI run in history (i18n commit) was the translation
  guardian test doing its job — fixed in the next commit.
- **2026-07-19** — Standards-system validation pass. Open-source audit: `.env`
  never committed, no real secrets/APP_KEY in git history. One hygiene finding: a
  tracked, non-gitignored `database/database.sqlite` (empty — base Laravel tables,
  0 rows; no personal data). Pending decision to untrack + gitignore it.
- **2026-07-17→19** — Stage 4 (open source + deploy). Community files (README,
  LICENSE MIT, CONTRIBUTING, CODE_OF_CONDUCT, SECURITY, issue/PR templates), CI
  extended with translations `--check`, `DEPLOYMENT.md` written, repo made public.
  Deployed live to Hostinger; discovered the LiteSpeed CSP override, the disabled
  `exec()` (storage:link), and `MAIL_SCHEME=smtps` (all documented above). Also
  restored the missing `storage/**` and `bootstrap/cache` `.gitignore` files and
  untracked 96 compiled Blade views that had been committed.
- **Pending on the deploy**: the reminder **cron** (`schedule:run`) and a real
  end-to-end test (register business → book → SMTP mail arrives).
