#!/usr/bin/env bash
# Create tar.gz base image of skeleton project for fast sandbox cloning
set -e
BASE=../base_image.tgz
SRC=../products/skeleton
if [ ! -d "$SRC" ]; then
  echo "Skeleton project not found: $SRC"
  exit 1
fi

tar -czf "$BASE" -C "$SRC" .

echo "Base image created: $BASE"