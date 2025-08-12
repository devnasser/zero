#!/usr/bin/env bash
# fake ChatOps: parse simple commands and reply
CMD=$1
case "$CMD" in
  status)
    echo "Last build: $(date) – GREEN"
    ;;
  split)
    python3 scripts/auto_split.py ../zero-docs/Kanban.md
    echo "Kanban auto-split executed."
    ;;
  qr)
    echo "QR-REVIEW token generated: $(uuidgen | cut -c1-6)"
    ;;
  approve)
    echo "QR approval recorded. Merging branch... (simulated)" ;;
  *) echo "Unknown command" ;;
esac