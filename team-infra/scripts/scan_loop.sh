#!/usr/bin/env bash
# scan_loop.sh – Runs scan.sh every 60s and appends to logs/scan.log
set -euo pipefail
DIR=$(dirname "$0")
LOG_DIR="$DIR/../logs"
mkdir -p "$LOG_DIR"
LOG_FILE="$LOG_DIR/scan.log"

echo "[scan_loop] Started at $(date) logging to $LOG_FILE"
while true; do
  echo -e "\n===== $(date) =====" >> "$LOG_FILE"
  "$DIR/scan.sh" all >> "$LOG_FILE" 2>&1 || true
  sleep 60
done