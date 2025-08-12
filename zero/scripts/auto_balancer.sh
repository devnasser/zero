#!/usr/bin/env bash
# Auto-balancer: ping ChatOps if any column has >5 open tasks
KANBAN="../zero-docs/Kanban.md"
TODO=$(grep -n "^[-] \[ \]" "$KANBAN" | wc -l)
INPROG=$(grep -n "## 🚧 In-Progress" -A100 "$KANBAN" | grep "^- \[ \]" | wc -l)
THRESH=5
if [ "$TODO" -gt "$THRESH" ] || [ "$INPROG" -gt "$THRESH" ]; then
  echo "[Auto-Balancer] Overload detected (TODO=$TODO, IN=$INPROG). Suggest swarm-coding or re-distribution." > /tmp/chatops_message
fi