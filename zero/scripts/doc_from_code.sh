#!/usr/bin/env bash
# Simple Doc-from-Code: extract PHPDoc lines and store to zero-docs/api/auto.md
set -e
OUT="../zero-docs/api/auto.md"
echo "# Auto-generated API Docs" > "$OUT"
grep -R "^ \*" ../zero/app | sed 's/^ \*//' >> "$OUT"
echo "Generated $OUT"