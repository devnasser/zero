#!/usr/bin/env python3
"""Auto-split long Kanban tasks (>4h) into 2-hour sub-tasks.
Very simple placeholder: duplicates the line with "(split-1)" suffix.
Run: python scripts/auto_split.py Kanban.md
"""
import sys
from pathlib import Path

if len(sys.argv) < 2:
    print("usage: auto_split.py Kanban.md")
    sys.exit(1)
board = Path(sys.argv[1])
text = board.read_text().splitlines()
new_lines = []
for line in text:
    new_lines.append(line)
    if '(>4h)' in line:
        base = line.replace('(>4h)', '').strip()
        new_lines.append(base + ' (split-1)')
        new_lines.append(base + ' (split-2)')
board.write_text('\n'.join(new_lines))
print('Auto-split complete')