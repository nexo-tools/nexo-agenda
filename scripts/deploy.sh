#!/usr/bin/env bash
# Server-side deploy helper: run from the app root over SSH.
set -euo pipefail
cd "$(dirname "$0")/.."

php artisan down --retry=30 || true

git pull origin main
# --no-scripts: shared hosts often disable proc_open, which Composer
# needs to run post-install scripts; we run package:discover directly.
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
php artisan package:discover --ansi
php artisan migrate --force
# Instance identity for the legal pages (nexo-ui standard). These are NOT secrets
# — a name and a public contact address — but they are per-instance: the repo
# cannot ship them, because .env.example is what a self-hoster copies and they
# would inherit the upstream author as their data controller. So they arrive as
# GitHub org variables and get written here, idempotently, before config:cache.
upsert_env() {
  local key="$1" value="$2"
  [ -z "$value" ] && return 0
  if grep -q "^${key}=" .env; then
    # Rewrite in place without a temp file the web server could serve.
    sed -i.bak "s|^${key}=.*|${key}=\"${value}\"|" .env && rm -f .env.bak
  else
    printf '\n%s="%s"\n' "$key" "$value" >> .env
  fi
}
upsert_env NEXO_LEGAL_OPERATOR "${NEXO_LEGAL_OPERATOR:-}"
upsert_env NEXO_LEGAL_CONTACT "${NEXO_LEGAL_CONTACT:-}"

php artisan config:cache
php artisan route:cache
php artisan view:cache
# Flush the rendered-page cache: it bakes in content-hashed @vite asset URLs, and
# a fresh public/build changes those hashes — stale entries would 404 the CSS.
php artisan cache:clear

php artisan up

echo "✓ Deployed $(git rev-parse --short HEAD)"
