# Nexo Agenda — Decision log

Decisions made during planning (2026-07-17), with rationale. New decisions get
appended with date + reason via `docs:` commits.

| # | Decision | Choice | Why |
|---|---|---|---|
| 1 | Tenancy | Multi-business, open registration | Platform value + portfolio value; a self-hoster can still run it for one business. Closable registration via env → backlog. |
| 2 | Payments in MVP | No — post-v1 (Mercado Pago first) | Avoids PCI/webhooks while the scheduling core is built; booking confirms without payment. |
| 3 | Notifications MVP | Email + `.ics` + prefilled `wa.me` links | Zero cost, zero external deps; attacks AgendaPro's paid/slow WhatsApp pain point. Reminder 24h before via cron. |
| 4 | Client accounts | None — magic link with hashed token | Less friction on mobile = more conversion; real differentiator vs. account-first competitors. Optional accounts → backlog. |
| 5 | Name | **Nexo Agenda** (`nexoagenda`) | Nexo family brand (sibling of Nexo Links); works in es/en/pt; no collisions found (2026-07-17, re-check before deploy). |
| 6 | Palette | Teal/green on neutrals, dark mode | Calm/availability/health; differentiates from the sector's corporate blue. |
| 7 | Isotype | Nexo node docking into a calendar cell | Family DNA (the "nexo" motif) applied to a booked slot. |
| 8 | UI base language | Neutral Spanish, "tú" | Widest LatAm+Spain reach for the base locale; en/pt in i18n phase. |
| 9 | Repo visibility | Private until v1, public in Stage 4 | Owner's call (recommendation was public day 1); Stage 4 includes the go-public checklist. |
| 10 | Scope focus | Bookings for service businesses only | Empty open source niche. Calendly-style meetings → Cal.com's turf; events → Hi.Events/Alf.io's turf. "Nexo Eventos" noted as possible sibling project. |
| 11 | Absorbed features | Virtual service mode (Stage 1), QR check-in (Stage 2) | The useful cores of the meetings/events ideas, without scope drift. Recurring bookings + session packages → backlog. |
| 12 | Directory | Opt-in `/explorar`, Stage 2 (phase 2.10) | Free, zero-commission counter to Fresha's 20% marketplace fee; opt-in respects privacy; needs category+city modeled from Stage 1. |
| 13 | Workflow | One commit per phase; stages run without check-ins; review checkpoint at each stage end | Owner preference; proven with Nexo Links. Stage 4 (server deploy) is guided step-by-step by nature. |
| 14 | Calendar sync | `.ics` attachments + subscription feeds, no Google OAuth | Zero external dependencies; works with every calendar app. |
