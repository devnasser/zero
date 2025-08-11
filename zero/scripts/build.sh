#!/usr/bin/env bash
set -euo pipefail

# Mount tmpfs for cache if not mounted
CACHE_DIR="/workspace/cache"
sudo mkdir -p "$CACHE_DIR"
if ! mountpoint -q "$CACHE_DIR"; then
  sudo mount -t tmpfs -o size=2048M tmpfs "$CACHE_DIR"
fi
export OPCACHE_FILE_CACHE="$CACHE_DIR/opcache"
export VIEW_COMPILED_PATH="$CACHE_DIR/blade"

# Build in sandbox: generate skeleton project if not exists
PROJECT_DIR="$(pwd)/products/skeleton"
if [ ! -d "$PROJECT_DIR" ]; then
    composer create-project --quiet laravel/laravel "$PROJECT_DIR"
    cd "$PROJECT_DIR"
    # Add Octane & RoadRunner for super-fast server
    composer require --quiet laravel/octane spiral/roadrunner-laravel
    php artisan octane:install --server=roadrunner --no-interaction
else
    cd "$PROJECT_DIR"
fi

php artisan key:generate --quiet || true
php artisan migrate --force --quiet || true