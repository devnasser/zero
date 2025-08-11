#!/usr/bin/env bash
set -euo pipefail

command -v python >/dev/null 2>&1 || true
command -v pip >/dev/null 2>&1 || true

if command -v pip >/dev/null 2>&1; then
  pip install --user --upgrade pip || true
  pip install --user pre-commit || true
fi

if [ -f .pre-commit-config.yaml ]; then
  pre-commit install || true
fi

echo "Bootstrap complete."