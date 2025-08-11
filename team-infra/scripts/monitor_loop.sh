#!/usr/bin/env bash
# monitor_loop.sh – Runs monitor.sh every 30s saving to logs/monitor.log
set -euo pipefail
DIR=$(dirname "$0")
LOG_DIR="$DIR/../logs"
mkdir -p "$LOG_DIR"
LOG_FILE="$LOG_DIR/monitor.log"

echo "[monitor_loop] Started at $(date) logging to $LOG_FILE"
while true; do
  echo -e "\n===== $(date) =====" >> "$LOG_FILE"
  "$DIR/monitor.sh" >> "$LOG_FILE" 2>&1 || true
  sleep 30
done