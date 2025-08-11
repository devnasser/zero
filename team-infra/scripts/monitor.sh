#!/usr/bin/env bash
# monitor.sh – Generates a snapshot report of system + container metrics in simple table form.
# Usage: ./scripts/monitor.sh
set -euo pipefail

# Colours disabled for raw pipe output

printf "\n===== SYSTEM =====\n"
free -m | awk 'NR==1{printf "|%s|%s|%s|%s|%s|%s|\n",$1,$2,$3,$4,$6,$7}; NR==2{printf "|Mem|%s MiB|%s MiB|%s MiB|%s MiB|%s MiB|\n",$2,$3,$4,$5,$7}'

printf "\n===== DISK =====\n"
df -h / | awk 'NR==1{print "|FS|Size|Used|Avail|Use%|Mounted|"}; NR==2{printf "|%s|%s|%s|%s|%s|%s|\n",$1,$2,$3,$4,$5,$6}'

printf "\n===== CONTAINERS =====\n"
podman ps --format "table {{.Names}}\t{{.Status}}\t{{.Image}}" | cat

printf "\n===== LOAD =====\n"
uptime | sed 's/^/| /' | sed 's/  / | /g'