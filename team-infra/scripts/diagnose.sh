#!/usr/bin/env bash
# diagnose.sh – Performs basic diagnostics on failing containers (e.g., airflow) and tries auto-repair.
# Usage: ./scripts/diagnose.sh <container-name>
set -euo pipefail

NAME=${1:-team-infra_airflow_1}

status=$(podman inspect -f '{{.State.Status}}' "$NAME" 2>/dev/null || echo "unknown")
if [[ "$status" != "running" ]]; then
  echo "Container $NAME in state: $status"
  echo "--- Last 50 log lines ---"
  podman logs "$NAME" | tail -n 50 || true
  echo "--- Attempting restart ---"
  podman restart "$NAME"
  sleep 10
  new_status=$(podman inspect -f '{{.State.Status}}' "$NAME")
  echo "New status: $new_status"
else
  echo "Container $NAME already running."
fi