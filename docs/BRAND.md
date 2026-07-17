# Nexo Agenda — Brand

Second product of the **Nexo** family (sibling of [Nexo Links](https://nexolinks.alvarocdev.com)).
Convention: two words for display ("Nexo Agenda"), joined for domain/slug
(`nexoagenda`, nexoagenda.alvarocdev.com, repo `nexo-agenda`).

Name collision check (2026-07-17): no scheduling product found under
"Nexo Agenda"/"NexoAgenda" (only nexo-sa.com, audio equipment — unrelated).
Re-verify before deploying.

## Personality

Calm, reliable, human. A tool that removes friction — never salesy, never noisy.
Voice in Spanish: neutral Latin American "tú" ("Reserva tu turno"), warm but
professional. Base locale `es`; `en` and `pt` added in the i18n phase.

## Isotype

A **Nexo node integrated into a calendar cell**: rounded-square calendar cell with
a connector node (circle + short stroke) docking into it — the family's "nexo"
(link/connection) motif applied to a booked slot. Hand-drawn SVG, single accent
color on transparent, legible at 16px. Master lives in `resources/brand/isotype.svg`;
favicons/OG/manifest icons are generated from it via committed scripts
(`scripts/*.mjs`, sharp) — never edited by hand.

## Color

Teal/green: availability, calm, health — deliberately apart from the generic
corporate blue of the scheduling market. All pairings must pass WCAG AA.

| Token | Light | Dark | Use |
|---|---|---|---|
| `brand-600` | `#0d9488` | — | Primary actions, links (on white: 4.5:1 with white text ✗ → use on light bg with dark text or as bg for white text at `brand-700`) |
| `brand-700` | `#0f766e` | — | Primary button bg (white text ≥ 4.5:1) |
| `brand-400` | — | `#2dd4bf` | Accent on dark surfaces |
| `ink` | `#0f172a` | `#e2e8f0` | Body text |
| `surface` | `#ffffff` | `#0f172a` | Background |
| `surface-2` | `#f1f5f9` | `#1e293b` | Cards, alternate rows |

Exact scale is implemented as Tailwind theme tokens in Phase 0.3; contrast is
re-checked there and in the Stage 3 accessibility audit.

## Typography

System font stack only (zero external requests):
`ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif`.
Numbers in schedules/tables use `font-variant-numeric: tabular-nums`.

## Application rules

- No visible Laravel defaults anywhere: branded auth pages, emails, error pages
  (404/419/500), empty states.
- Every generated asset (favicons, OG images, manifest) comes from a committed,
  regenerable script.
- Businesses get limited public-page theming (accent color, logo) in Stage 2 —
  the Nexo Agenda brand frames the product UI, the business brand frames its
  public page.
- Instance attribution configurable by env; alvarocdev instance shows
  "powered by alvarocdev.com".
