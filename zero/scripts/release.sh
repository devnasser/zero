#!/usr/bin/env bash
set -euo pipefail
ts=$(date +%Y%m%d-%H%M%S)
zip -qr "/workspace/archive/release-$ts.zip" /workspace/zero/products/skeleton