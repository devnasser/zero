#!/usr/bin/env bash
set -euo pipefail

TOOLS_VENV=".venv-tools"
PYBIN="${TOOLS_VENV}/bin/python"
PIPBIN="${TOOLS_VENV}/bin/pip"
PRECOMMITBIN="${TOOLS_VENV}/bin/pre-commit"

VENV_CREATED=false
if [ ! -d "${TOOLS_VENV}" ]; then
  if command -v python3 >/dev/null 2>&1; then
    if python3 -m venv "${TOOLS_VENV}"; then
      VENV_CREATED=true
    else
      VENV_CREATED=false
    fi
  elif command -v python >/dev/null 2>&1; then
    if python -m venv "${TOOLS_VENV}"; then
      VENV_CREATED=true
    else
      VENV_CREATED=false
    fi
  fi
else
  VENV_CREATED=true
fi

if [ "${VENV_CREATED}" = true ]; then
  "${PIPBIN}" install --upgrade pip || true
  "${PIPBIN}" install pre-commit || true
  if [ -f .pre-commit-config.yaml ]; then
    "${PRECOMMITBIN}" install || true
  fi
  echo "Bootstrap complete. Tools venv: ${TOOLS_VENV}"
else
  echo "Note: Could not create local venv (${TOOLS_VENV})."
  echo "Pre-commit not installed automatically due to environment constraints."
  echo "In CI this will still run. Locally, install pre-commit via pipx or system package, or enable venv support (e.g., apt install python3-venv)."
fi