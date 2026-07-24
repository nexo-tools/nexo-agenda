<div align="center">

<img src="resources/brand/isotype.svg" width="88" alt="Nexo Agenda isotype">

# Nexo Agenda

**Open source, self-hosted booking platform for service businesses.**
Own your bookings, your clients and your data — no monthly fee, no per-client
commission, no feature paywalls. Runs on cheap shared PHP hosting.

[![CI](https://github.com/alvarocdev-git/nexo-agenda/actions/workflows/ci.yml/badge.svg)](https://github.com/alvarocdev-git/nexo-agenda/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-0d9488.svg)](LICENSE)
![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777bb4.svg)
![Laravel 13](https://img.shields.io/badge/Laravel-13-ff2d20.svg)
![Tests](https://img.shields.io/badge/tests-147%20passing-0f766e.svg)

[Live demo](https://nexoagenda.alvarocdev.com/estudio-nexo) ·
[Deployment guide](DEPLOYMENT.md) ·
[Contributing](CONTRIBUTING.md) ·
[Scope & roadmap](docs/SCOPE.md)

</div>

---

Nexo Agenda is the **open source alternative to AgendaPro, Fresha and Booksy**:
a multi-business booking system where any business registers, sets up its
services and professionals, and gets a public booking page at `/{slug}`. Clients
book in seconds from a phone — **no account, no app**. It's the second product of
the **Nexo** family (sibling of [Nexo Links](https://nexolinks.alvarocdev.com)).

## Why it exists

Commercial booking tools charge monthly fees, take a commission on every new
marketplace client, gate reminders behind paid tiers, and make your data hard to
export. Nexo Agenda answers each of those pain points directly:

| Pain point in commercial tools | Nexo Agenda answer |
|---|---|
| 20% commission per marketplace client, price hikes | Self-hosted, free, **zero commissions** |
| WhatsApp / SMS reminders charged separately | Free prefilled `wa.me` links + email always included |
| No built-in reviews or feedback | Reviews with moderation, translatable help center |
| Data locked in, hard to export | One-click CSV export of clients and bookings |
| Native apps that crash | Mobile-first web, nothing to install |
| Account required to book | Guest booking with a hashed magic link |

## Screenshots

Screenshots are generated from the demo seeder so they always match the current
UI — see [Generating screenshots](#generating-screenshots). Once captured into
`docs/screenshots/`, they render here:

<!-- Uncomment when the PNGs exist in docs/screenshots/
| Public booking page | Booking flow (4 steps) | Owner dashboard |
|---|---|---|
| ![Public page](docs/screenshots/public-page.png) | ![Booking flow](docs/screenshots/booking-flow.png) | ![Dashboard](docs/screenshots/dashboard.png) |
-->

Try it live instead: **[nexoagenda.alvarocdev.com/estudio-nexo](https://nexoagenda.alvarocdev.com/estudio-nexo)**.

## Features

**Booking core**
- Multi-business: open registration, reserved slugs, public page at `/{slug}`
- Services (in-person or virtual with a video link), buffers, min/max notice, cancellation window
- Professionals with weekly schedules and absences
- Timezone-aware availability engine
- Guest booking in 4 steps — no account — with a hashed **magic link** to view / cancel / reschedule
- Owner dashboard (day / week) + manual bookings

**Communications (zero external cost)**
- Branded confirmation, reminder and cancellation emails
- `.ics` calendar attachment + prefilled `wa.me` WhatsApp links
- Automatic 24h reminder via a scheduled command

**Differentiators**
- Light CRM per business (visit history, no-show count) + CSV export
- Waitlist with automatic cancellation notifications
- Per-professional `.ics` subscription feeds
- Per-business public-page theming (accent color, logo)
- Front-desk (counter) fast day view + QR check-in
- First-party, cookieless statistics (occupancy, no-shows, top services)
- Client reviews with moderation and public ratings
- Opt-in public directory `/explorar` with search by name/category/city and SEO category pages

**Quality & operations**
- **i18n** es / en / pt with a visible selector (custom generator, guardian test)
- Security headers + self-contained CSP (zero external requests), rate limiting, hashed tokens
- WCAG AA accessibility audited
- Public-page cache invalidated by model events, DB indexes and pagination from day one
- Branded, translated error pages (404/403/419/429/500/503)
- Dynamic `sitemap.xml` + `robots.txt`, help center and feedback system

## Tech stack

- **PHP 8.3+**, **Laravel 13**
- **Blade** + **Alpine.js** + **Tailwind CSS 4** (Vite)
- **MySQL** (portable to any Laravel-supported DB)
- **Pest** (147 tests / 386 assertions), **Pint**, **Larastan** (level 6)
- Zero external runtime requests: no CDNs, no Google Fonts, system font stack

## Quickstart (local, Docker / Laravel Sail)

```bash
git clone https://github.com/alvarocdev-git/nexo-agenda.git
cd nexo-agenda

cp .env.example .env

# Install PHP deps (no local PHP required)
docker run --rm -v "$PWD":/app -w /app composer:latest install

# Boot the stack (app, MySQL, Mailpit)
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

# Front-end assets (Node 20+)
npm install
npm run build

# Optional: seed a full demo business
./vendor/bin/sail artisan db:seed --class=DemoSeeder
```

Then open **http://localhost:8080**. The demo seeder creates
`demo@nexoagenda.test` / `password` with a public page at `/estudio-nexo`.

### Handy commands

```bash
npm run brand          # regenerate favicons / OG / manifest from the isotype
npm run translations   # rebuild lang/{en,pt}.json from source strings
./vendor/bin/sail artisan schedule:work   # run the reminder scheduler locally
```

### Running the checks

```bash
docker run --rm -v "$PWD":/app -w /app composer:latest sh -c \
  "vendor/bin/pint --test && vendor/bin/phpstan analyse --no-progress && vendor/bin/pest"
node scripts/generate-translations.mjs --check
```

## Deployment

See **[DEPLOYMENT.md](DEPLOYMENT.md)** for a complete, step-by-step guide to
deploying on shared hosting (Hostinger) with a subdomain, the reminder cron, and
SMTP. Nothing ties the app to shared hosting — it's a standard Laravel app,
portable to a VPS without rewrites.

### Generating screenshots

Screenshots in this README come from the demo seeder so they always match the
current UI:

```bash
./vendor/bin/sail artisan migrate:fresh --seed --seeder=DemoSeeder
```

Then capture the pages below (browser at a 390×844 mobile viewport for public
pages, 1440×900 for the dashboard) and save them under `docs/screenshots/`:

- `public-page.png` → `/estudio-nexo`
- `booking-flow.png` → `/estudio-nexo/reservar/{service}` (the horarios/datos step)
- `dashboard.png` → `/` after logging in as `demo@nexoagenda.test` / `password`

## Configuration highlights

Every string that follows is set via `.env` (see `.env.example`):

- `APP_LOCALE=es` (base locale; `en` / `pt` via the selector)
- `NEXO_ATTRIBUTION_URL` / `NEXO_ATTRIBUTION_TEXT` — optional "powered by" footer
- `SESSION_SECURE_COOKIE=true` in production (HTTPS)

Business categories and reserved slugs live in [`config/nexo.php`](config/nexo.php).

## Contributing

Contributions are welcome — read **[CONTRIBUTING.md](CONTRIBUTING.md)** and our
**[Code of Conduct](CODE_OF_CONDUCT.md)**. Found a security issue? See
**[SECURITY.md](SECURITY.md)** (please don't open a public issue).

## Documentation

- [`docs/SCOPE.md`](docs/SCOPE.md) — value proposition, domain model, roadmap
- [`docs/DECISIONS.md`](docs/DECISIONS.md) — architectural decision log
- [`docs/BRAND.md`](docs/BRAND.md) — brand, palette, isotype
- [`docs/WIREFRAMES.md`](docs/WIREFRAMES.md) — screen wireframes

## Nexo ecosystem

Nexo is a family of open-source, self-hostable tools that share one visual identity
([nexo-brand](https://github.com/nexo-tools)), one optional account
([Nexo ID](https://github.com/nexo-tools/nexo-id) SSO) and one set of engineering
standards. Every tool runs **fully standalone** — the ecosystem is opt-in.

| Tool | What it is | Repo |
| --- | --- | --- |
| **Nexo Tools** | Ecosystem hub — discover the tools and hop between them with one account | [nexo-tools](https://github.com/nexo-tools/nexo-tools) |
| **Nexo Links** | Link-in-bio you host yourself (Linktree alternative) | [nexo-links](https://github.com/nexo-tools/nexo-links) |
| **Nexo Agenda** | Bookings for service businesses (AgendaPro / Fresha / Booksy alternative) | — you are here |
| **Nexo Short** | Self-hosted URL shortener | [nexo-short](https://github.com/nexo-tools/nexo-short) |
| **Nexo Events** | Event tickets and passes | [nexo-events](https://github.com/nexo-tools/nexo-events) |
| **Nexo ID** | One account for every tool — OAuth 2.0 / OIDC SSO | [nexo-id](https://github.com/nexo-tools/nexo-id) |

New to Nexo? Start at **[nexotools.alvarocdev.com](https://nexotools.alvarocdev.com)**.
Built by **[alvarocdev.com](https://alvarocdev.com)** — the tech behind Nexo.

## License

[MIT](LICENSE) © [Alvaro Carrizales](https://alvarocdev.com) (alvarocdev)
