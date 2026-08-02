# design.md — Nexo family design system

> System authority for every public landing in the Nexo family. Hallmark reads this file first:
> on design.md-managed projects the diversification rule is **inverted** — pages must share the
> system, not differ from each other. This file is **byte-identical in the six tools** (canonical
> copy: `alvaro/templates/nexo-ui/design.md`; `nexo-doctor` enforces equality). To deviate, amend
> this file and propagate — never override per page.

## System

- Genre: modern-minimal (utility SaaS, not editorial marketing).
- Macrostructure (family-wide, single): **Workbench** — functional opening, real screenshots as
  proof, tabular spec sheet, conversational FAQ, single closing CTA.
- Theme: custom (vibe: "utilitario Nexo, violeta sobrio, es-first").
- Axes: paper light/dark via tokens · display sans · accent hue cool ~290° (violet).
- Section skeleton, in order, each tagged with its marker:
  `data-landing-section="hero"` → `"producto"` → `"datos"` → `"preguntas"` → `"cierre"`.
- Legitimate variety lives INSIDE sections (each tool's screenshots, specs, FAQs) — never in the
  structure between tools.
- **Mobile-first, always**: most usage is on phones. Design and review at 375px FIRST, then
  768/1280. One clean column, tap targets ≥44px, screenshots legible without zoom, no horizontal
  scroll, primary CTA within thumb reach. Every shots.json includes a mobile capture (375×812)
  alongside desktop; gate evidence is mobile light + dark.

## Tokens

`nexo-tokens.css` is the source of truth (generated from `nexo-brand/palette.json`; never edit by
hand). Reference tokens by name — `--nexo-bg`, `--nexo-surface`, `--nexo-text`, `--nexo-text-muted`,
`--nexo-primary`, `--nexo-border-control`, `--nexo-radius-*`, `--nexo-shadow-*` — and consume them
through the semantic utilities (`bg-surface`, `text-ink`, `text-muted`, `border-line`). No raw hex,
no raw Tailwind palette pairs in views.

- Accent: violet, **solid only**, ≤~5% of the viewport. Gradients (background or
  `background-clip: text`) are banned — the violet-gradient hero is the #1 AI tell and violet is
  our brand hue; the edge we walk is: solid, sparing, never blended.

## Typography

- Family face: **Instrument Sans** (Alvaro's pick, 2026-08-01), self-hosted via
  `laravel-vite-plugin/fonts` — `bunny('Instrument Sans', { weights: [400, 500, 600] })` in
  `vite.config.js`, exactly the mechanism nexoid already ships. Zero CDN at runtime; the woff2
  files build into `public/build/assets/`.
- Role: full UI (body + display), declared once:
  `--font-sans: 'Instrument Sans', var(--nexo-font-sans);` — system stack stays as fallback.
- Display voice: weight does the work — headings 600 with slightly negative tracking
  (`-0.01em`…`-0.02em`); body 400. It is a discreet face by design: the family reads calm, not
  loud. Mono outlier (`--nexo-font-mono`): wordmark + tabular figures only.
- Headings always roman (no italics); emphasis by weight or accent. `tabular-nums` on every figure.
- H1 ≤50 chars; `overflow-wrap: anywhere` on display text (Spanish words are long).

## CTA voice

- Primary = `.nexo-btn--primary` (solid violet, min 44px). Secondary = `.nexo-btn--ghost`.
- Button label = the exact verb of the action: «Acortar enlace», «Crear evento», «Crear tu página».
  Never «Empezar ahora», «Click aquí», «Submit».
- One primary CTA in `hero`, the same verb again in `cierre`. Nothing else competes.

## Motion stance

Motion-cut: the system's .15s transitions; at most ONE orchestrated entrance (stagger ≤500ms total);
`transform`/`opacity` only; no `transition: all`, no bounce easings, no universal scroll fade-up.
`prefers-reduced-motion` collapses everything to an opacity crossfade ≤150ms. Focus ring instant,
never animated. Landing interactivity ships in the Vite bundle or not at all (CSP is hash-based).

## Copy

- Spanish-first, **tuteo neutro** (family rule since 2026-07-29). Keys in English, es/pt in maps.
- The es h1 IS the tool's tagline from `config/nexo-ecosystem.php` — one claim per tool across
  every surface (switcher, cards, alvarocdev, landing, og-image). `nexo-doctor` checks equality.
- Real data or nothing: specs come from config/limits the code enforces; no invented metrics,
  no testimonials, no logos (gate 46). FAQ answers come from `lang/*/help.php` — a person's voice.
- Banned openings (es tells): «Potencia tu…», «Desata/Desbloquea el poder de…», «Donde X se
  encuentra con Y», «Empodera…», «Reimagina…», «Impulsa tu flujo de trabajo», «Soluciones
  innovadoras», «Integración perfecta/sin fisuras», «En el panorama digital actual», «Al siguiente
  nivel», «De última generación».

## Family

- Members: nexotools · nexoid · nexolinks · nexoagenda · nexoshort · nexoevents.
- Nav = `x-nexo-header`, footer = `x-nexo-footer` (the shared chrome IS the family's nav/footer —
  never invent marketing chrome, no AI-nav, no 4-column AI-footer).
- Screenshots: real captures in `<figure>` with hairline border, ordinal stacked captions
  («1 — Pega la URL…»), light AND dark variants switched by `[data-theme]`, explicit
  `width/height`, WebP, lazy below the fold. Never re-drawn browser chrome, never invented UI.
  Captured from a local instance with `DemoSeeder` data via `alvaro/scripts/nexo-shots.mjs`.
- Every landing CSS starts with the Hallmark stamp
  (`/* Hallmark · macrostructure: Workbench · theme: custom Nexo (design.md) · … */`) and logs to
  `.hallmark/log.json` — future audits grade execution against this system, not against rotation.
- Not imported from anywhere: centered 100vh hero, identical-card grids, violet gradients, emoji
  icons, invented numbers.

## Variants

- **nexotools (hub)**: the registry-driven tool grid IS its `producto` section (dynamic from
  `config/nexo-ecosystem.php`, `HomeTest` enforces it). Everything else follows the skeleton.
- **nexoshort**: the landing lives on the panel host only; the short host (`nxo.li`) stays
  minimal, cookieless and chrome-free (ADR-001).

## Exports

Extended on demand. Screenshot manifest format: `shots.json` per tool (routes, viewport, themes) —
see `alvaro/scripts/nexo-shots.mjs`.
