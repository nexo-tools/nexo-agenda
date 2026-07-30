# Contributing to Nexo Agenda

Thanks for your interest in improving Nexo Agenda! This project is open source
and self-hosted, and contributions of every size are welcome — bug reports,
translations, docs, and code.

## Ground rules

- Be respectful. This project follows a [Code of Conduct](CODE_OF_CONDUCT.md).
- Discuss non-trivial changes in an issue first, so effort isn't wasted.
- Keep pull requests focused: one logical change per PR.
- Never commit secrets, real customer data, or a populated `.env`.

## Development setup

You don't need PHP or Composer installed locally — everything runs through
Docker. See the [Quickstart in the README](README.md#quickstart-local--docker--laravel-sail).
Node 20+ is expected locally for the asset and translation scripts.

```bash
cp .env.example .env
docker run --rm -v "$PWD":/app -w /app composer:latest install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
npm install && npm run build
./vendor/bin/sail artisan db:seed --class=DemoSeeder   # optional demo data
```

## Before every commit

All of these must be green — CI enforces the same set:

```bash
docker run --rm -v "$PWD":/app -w /app composer:latest sh -c \
  "vendor/bin/pint --test && vendor/bin/phpstan analyse --no-progress && vendor/bin/pest"
node scripts/generate-translations.mjs --check
npm run build
```

- **Pint** — code style (Laravel preset). Run without `--test` to auto-fix.
- **Larastan** — static analysis at level 6. Add `@property` docblocks where the
  analyzer can't infer casts/columns.
- **Pest** — the test suite. New behavior needs new tests.
- **Translations** — see below.

## Internationalization

Every user-facing string goes through `__()` with a **literal English** source
string. English is the key language, so `lang/en.json` does not exist — an
untranslated key already reads as English, which is also why
`APP_FALLBACK_LOCALE=en`.

```php
__('Book your appointment')       // ✅ detected by the generator
__($someVariable)                 // ❌ not detected — never do this
```

An all-lowercase key with no spaces (`__('address')`) is read as a lang-file
lookup, not as a literal, and is skipped. Field names for validation messages
therefore live in `lang/{locale}/validation.php` under `attributes`, not in a
`FormRequest::attributes()` method.

After adding or changing a `__('…')` string:

1. Add the matching entries to `scripts/translations/es.json` and
   `scripts/translations/pt.json`.
2. Run `npm run translations` to rebuild `lang/es.json` and `lang/pt.json`.
3. `node scripts/generate-translations.mjs --check` must pass (a guardian test
   also enforces this in CI).

Spanish copy is neutral **tuteo** ("Crea tu cuenta"), never voseo.

## Commit & PR conventions

- **Conventional Commits** in English: `feat:`, `fix:`, `docs:`, `refactor:`,
  `test:`, `chore:`.
- One logical change per commit where practical.
- Scope-changing ideas go into [`docs/SCOPE.md`](docs/SCOPE.md); notable
  architectural choices into [`docs/DECISIONS.md`](docs/DECISIONS.md).
- Fill in the PR template and confirm the checklist before requesting review.

## Architecture notes

- Standard Laravel — Form Requests + Policies ship with each feature, not
  retrofitted. Public endpoints are rate-limited as they're added.
- Zero external runtime requests (no CDNs, no Google Fonts). Keep it that way.
- Don't cache Eloquent models — cache plain arrays of primitives
  (see decision #17 in [`docs/DECISIONS.md`](docs/DECISIONS.md)).

Questions? Open a [discussion or issue](https://github.com/nexo-tools/nexo-agenda/issues).
