#!/usr/bin/env bash
set -e
SLUG="dokumoku"
VER="${1:-0.1.0}"
TMP="/tmp/${SLUG}-${VER}"
ZIP="${SLUG}-${VER}.zip"

rm -rf "$TMP"
mkdir -p "$TMP"
rsync -a --delete   --exclude ".git"   --exclude "build"   --exclude ".gitignore"   --exclude "*.zip"   ./ "$TMP/$SLUG/"

cd "$TMP"
zip -r "$ZIP" "$SLUG" > /dev/null
mv "$ZIP" -t -
echo "Built $ZIP"
