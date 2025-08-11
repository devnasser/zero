#!/usr/bin/env bash
set -euo pipefail

TOOLS_VENV=".venv-tools"
PIPBIN="${TOOLS_VENV}/bin/pip"
PRECOMMITBIN="${TOOLS_VENV}/bin/pre-commit"

VENV_READY=false
if [ ! -d "${TOOLS_VENV}" ]; then
  if command -v python3 >/dev/null 2>&1; then
    if python3 -m venv "${TOOLS_VENV}"; then
      VENV_READY=true
    fi
  elif command -v python >/dev/null 2>&1; then
    if python -m venv "${TOOLS_VENV}"; then
      VENV_READY=true
    fi
  fi
else
  VENV_READY=true
fi

if [ "${VENV_READY}" = true ]; then
  "${PIPBIN}" install --upgrade pip || true
  "${PIPBIN}" install pre-commit || true
  "${PRECOMMITBIN}" install || true
  "${PRECOMMITBIN}" install --hook-type commit-msg || true
  echo "Git hooks installed via ${TOOLS_VENV}."
else
  echo "Note: Could not create local venv (${TOOLS_VENV}). Install pre-commit manually or enable python3-venv."
fi