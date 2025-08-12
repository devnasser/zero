#!/usr/bin/env bash
set -euo pipefail

# size of sandbox ramfs
SIZE="2048M"
SANDBOX_DIR=$(mktemp -d)

sudo mount -t tmpfs -o size=$SIZE tmpfs "$SANDBOX_DIR"
cp -r /workspace/zero "$SANDBOX_DIR/"
cd "$SANDBOX_DIR/zero"

# Link cache directories to ramfs
mkdir -p /workspace/cache
export OPCACHE_FILE_CACHE="/workspace/cache/opcache"
export VIEW_COMPILED_PATH="/workspace/cache/blade"

make test || { echo "Tests failed"; sudo umount "$SANDBOX_DIR"; exit 1; }
make analyse
make build

sudo umount "$SANDBOX_DIR"