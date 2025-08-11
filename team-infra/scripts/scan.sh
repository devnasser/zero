#!/usr/bin/env bash
# scan.sh – Deep scan for container issues (collects status + last logs) without altering state.
# Usage: ./scripts/scan.sh [container-name|all]
set -euo pipefail

TARGET=${1:-all}

echo "===== CONTAINER STATUS ====="
podman ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Image}}"

echo "\n===== POTENTIAL ISSUES ====="
list=$(podman ps -a --format "{{.Names}} {{.Status}}" | awk '$2!="Up" {print $1}')

for c in $list; do
  if [[ "$TARGET" == "all" || "$TARGET" == "$c" ]]; then
    echo "\n-- $c (state not running) --"
    podman logs "$c" | tail -n 50 || true
  fi
done