# Nexo Agenda — Scope

Open source, self-hosted booking platform for service businesses (salons, clinics,
studios, consultants) — the "open source AgendaPro". Multi-business: any business
registers and gets a public booking page at `/{slug}`.

## Value proposition

**For service businesses:** own your bookings, your clients and your data. No
monthly fee, no commission per client, no feature paywalls. Runs on cheap shared
PHP hosting.

**For end clients:** book in seconds from a phone, no account and no app required.

## Competitor pain points → our answers

Documented from user reviews (JoinSecret, Capterra, Software Advice, Trustpilot,
GoodCall — July 2026):

| Pain point (competitor) | Nexo Agenda answer |
|---|---|
| WhatsApp reminders charged separately, slow or undelivered (AgendaPro) | Free `wa.me` prefilled links + email always included — no tiers |
| Reminders sent per service instead of per client (AgendaPro) | Design rule: communications grouped per client/visit |
| No client reviews/feedback feature (AgendaPro) | Built-in reviews (Stage 2) |
| 20% commission per new marketplace client (Fresha), SMS/staff extra fees (Booksy), price increases | Self-hosted, free, zero commissions — core README message |
| Data hard to export and reuse | CSV export of clients and bookings (Stage 2) |
| Native apps crashing, app-dependent workflows | Mobile-first web, nothing to install |
| Slow support, unresolved billing errors | Open source: public issues + translatable help center |

## Product principles

1. **Mobile-first** — designed for the phone first.
2. **Multilanguage es/en/pt** — every string through `__()` from the first commit;
   Spanish (neutral, "tú") is the base locale.
3. **Accessibility WCAG AA** — labels, visible focus, contrast, reduced-motion,
   skip links; dedicated audit in Stage 3.
4. **Privacy & self-contained** — zero external requests (no CDNs, no Google
   Fonts), first-party cookieless analytics (daily anonymous hash).
5. **Brand everywhere** — no visible Laravel defaults; branded mails and error pages.
6. **Robustness** — reserved slugs, rate limiting, security headers + CSP, public
   page cache invalidated by model events, dynamic sitemap + robots, reports/feedback
   system, translatable help center.
7. **Secure & scalable from day one** — Policies + Form Requests shipped with each
   feature (not retrofitted), rate limiting on every public endpoint as it is born,
   indexes and pagination from the first migration, hashed tokens in DB, standard
   Laravel architecture with nothing tied to shared hosting (portable to a VPS
   without rewrites). Stage 3 is an audit, not a first pass.
8. **Portfolio-grade open source** — MIT, full README, CONTRIBUTING, issue/PR
   templates, DEPLOYMENT guide, demo seeder, configurable attribution.

## Domain model (MVP)

- **Business** — slug, name, category, city/neighborhood (structured from day one,
  the directory needs them), timezone, contact, branding, directory opt-in flag.
- **User** — business owner (only login role in v1).
- **Professional** — record under a business (no login in v1), own weekly schedule
  and absences.
- **Service** — name, duration, price (display), mode: in-person | virtual (business
  provides its own video-call link), buffer, booking rules (min/max notice,
  cancellation window).
- **Booking** — client name + email + phone, professional, service, start/end,
  status (pending/confirmed/cancelled/attended/no-show), management token (hashed),
  QR check-in code.
- **Client** — per-business record aggregated by email/phone (light CRM: history,
  no-show count).

Guest booking flow: no client account; a magic link (hashed token) lets the client
view / cancel / reschedule. Reminder email 24h before via scheduled command (cron).

## MVP — in (Stage 1)

Business registration + onboarding, services CRUD, professionals CRUD with weekly
schedules and absences, public booking page with real-time availability, guest
booking with magic-link management, booking dashboard (day/week views), branded
emails (confirmation, reminder, cancellation) with `.ics` attachment and `wa.me`
links, virtual service mode.

## MVP — out (Stage 2)

Light CRM + CSV export, waitlist with cancellation notifications, per-professional
`.ics` subscription feeds, per-business public page theming, front-desk fast day
view, first-party stats (occupancy, no-shows, top services), QR check-in,
client reviews, i18n es/en/pt with visible selector, opt-in public directory
(`/explorar`: search by name, category, city; SEO category pages; review ratings).

## Post-v1 backlog (Stage 5 — no idea gets lost)

- Recurring bookings (weekly/biweekly slots: therapists, classes).
- Session packages (e.g. "10 classes", decremented per booking).
- Payments/deposits: Mercado Pago first, Stripe later.
- Optional client accounts (guest flow stays default).
- Staff logins (professional sees own agenda; roles/permissions).
- WhatsApp Business Cloud API as optional integration (wa.me stays free default).
- Directory: map/geolocation (conflicts with zero-external-requests — needs
  self-hosted tiles or static approach; think carefully).
- Events module or sibling project "Nexo Eventos" (guest lists, ticket QR,
  capacity) — deliberately out of scope; Hi.Events/Alf.io own that space today.
- Cross-pollination with Nexo Links (business page linking both products).
- Closable public registration via env flag (private instances).
- Signed URLs for booking management links: tokens are stored hashed, so only
  the confirmation email carries the magic link today (reminders point to it).
  Laravel signed routes would let every email include the link, statelessly.

## Non-goals

- Calendly-style personal meeting links (Cal.com owns that niche).
- Full event ticketing platform.
- Native mobile apps.
- Google Calendar two-way sync (v1 offers `.ics` feeds instead — zero OAuth,
  zero external dependencies).
