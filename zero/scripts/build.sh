#!/usr/bin/env bash
set -euo pipefail
# Build in sandbox: generate skeleton project if not exists
PROJECT_DIR="$(pwd)/products/skeleton"
if [ ! -d "$PROJECT_DIR" ]; then
    composer create-project --quiet laravel/laravel "$PROJECT_DIR"
fi
cd "$PROJECT_DIR"
php artisan key:generate --quiet
php artisan migrate --force --quiet || true