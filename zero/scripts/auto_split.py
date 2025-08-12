#!/usr/bin/env python3
"""Auto-split long Kanban tasks (>4h) into 2-hour sub-tasks.
Very simple placeholder: duplicates the line with "(split-1)" suffix.
Run: python scripts/auto_split.py Kanban.md
"""
import sys
from pathlib import Path
import re

if len(sys.argv) < 2:
    print("usage: auto_split.py Kanban.md")
    sys.exit(1)
board = Path(sys.argv[1])
text = board.read_text().splitlines()
new_lines = []
for line in text:
    new_lines.append(line)
    # new logic: split any marker (>30m|>1h|>2h|>4h) into 15m chunks
    match = re.search(r'\(>(\d+)([hm])\)', line)
    if match:
        amount = int(match.group(1))
        unit = match.group(2)
        from os import getenv
        chunk = int(getenv('SPLIT_MAX_MIN', '10'))
        minutes = amount*60 if unit=='h' else amount
        parts = (minutes + (chunk-1))//chunk
        base = re.sub(r'\(>.*?\)', '', line).strip()
        for i in range(1, parts+1):
            new_lines.append(f"{base} (split-{i})")
board.write_text('\n'.join(new_lines))
print('Auto-split complete')