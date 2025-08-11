#!/usr/bin/env bash
set -euo pipefail

if ! command -v pre-commit >/dev/null 2>&1; then
  echo "pre-commit not found. Installing (user scope)..."
  if command -v pip >/dev/null 2>&1; then
    pip install --user pre-commit || true
  else
    echo "pip not available. Please install Python/pip to use pre-commit."
    exit 0
  fi
fi

pre-commit install || true
pre-commit install --hook-type commit-msg || true

echo "Git hooks installed."