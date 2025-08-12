#!/usr/bin/env bash
inotifywait -r -m -e modify,create,delete --format '%w%f' ./ | while read change; do
  echo "[watch] Detected change: $change"
  make test || echo "Tests failed"
 done