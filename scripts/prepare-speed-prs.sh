#!/usr/bin/env bash
set -euo pipefail

PARTICIPANTS_FILE="${PARTICIPANTS_FILE:-tools/speedtest/participants.txt}"
PUSH="${PUSH:-false}"
OPEN_PR="${OPEN_PR:-false}"
DRY_RUN="${DRY_RUN:-false}"
BASE_BRANCH="${BASE_BRANCH:-$(git rev-parse --abbrev-ref HEAD)}"
LABELS="${LABELS:-speedtest}"

if [ ! -f "$PARTICIPANTS_FILE" ]; then
  echo "Participants file not found: $PARTICIPANTS_FILE" >&2
  exit 1
fi

mkdir -p bench
TS=$(date +%Y%m%d-%H%M%S)

while IFS= read -r pid; do
  [ -z "$pid" ] && continue
  BRANCH="speed/${pid}-${TS}"
  if [ "$DRY_RUN" = "true" ]; then
    echo "[DRY_RUN] would create branch $BRANCH from $BASE_BRANCH and add bench/${pid}.txt"
    continue
  fi
  git checkout -b "$BRANCH" "$BASE_BRANCH"
  echo "speedtest ${pid} ${TS}" > "bench/${pid}.txt"
  git add "bench/${pid}.txt"
  git commit -m "chore(speed): add marker for ${pid} (${TS})" --no-verify
  if [ "$PUSH" = "true" ]; then
    git push -u origin "$BRANCH"
    if [ "$OPEN_PR" = "true" ] && command -v gh >/dev/null 2>&1; then
      gh pr create --base "$BASE_BRANCH" --head "$BRANCH" --title "chore(speed): ${pid} ${TS}" --body "Speed test PR for ${pid} at ${TS}" --label "$LABELS" || true
    else
      echo "PR not opened automatically (OPEN_PR=$OPEN_PR, gh available=$(command -v gh >/dev/null 2>&1 && echo yes || echo no))"
    fi
  else
    echo "Prepared branch $BRANCH (not pushed). To push: git push -u origin $BRANCH"
  fi
  git checkout "$BASE_BRANCH"
done < "$PARTICIPANTS_FILE"

echo "Preparation complete."