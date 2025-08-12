#!/usr/bin/env bash
# Create or restore snapshot for SQLite in-memory db testing
CMD=$1
SNAP="database.sqlite.snap"
if [[ "$CMD" == "create" ]]; then
  cp database/database.sqlite "$SNAP"
  echo "Snapshot created: $SNAP"
elif [[ "$CMD" == "restore" ]]; then
  cp "$SNAP" /dev/shm/test.sqlite
  export DB_CONNECTION=sqlite
  export DB_DATABASE=/dev/shm/test.sqlite
  echo "Snapshot restored to in-memory file"
else
  echo "usage: snapshot_db.sh create|restore"
fi