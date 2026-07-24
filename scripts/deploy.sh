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
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Flush the rendered-page cache: it bakes in content-hashed @vite asset URLs, and
# a fresh public/build changes those hashes — stale entries would 404 the CSS.
php artisan cache:clear

php artisan up

echo "✓ Deployed $(git rev-parse --short HEAD)"
