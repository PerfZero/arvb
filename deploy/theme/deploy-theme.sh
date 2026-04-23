#!/usr/bin/env bash
set -Eeuo pipefail

if [[ $# -ne 3 ]]; then
  echo "Usage: $0 <theme-archive.tar.gz> <container-name> <container-theme-path>"
  exit 64
fi

THEME_ARCHIVE="$1"
CONTAINER_NAME="$2"
CONTAINER_THEME_PATH="$3"
THEME_NAME="$(basename "$CONTAINER_THEME_PATH")"
THEME_PARENT="$(dirname "$CONTAINER_THEME_PATH")"
BACKUP_ROOT="${BACKUP_ROOT:-/opt/backups/wp-themes}"
KEEP_BACKUPS="${KEEP_BACKUPS:-5}"
STAMP="$(date +%Y%m%d-%H%M%S)"
TMP_DIR="$(mktemp -d "/tmp/${THEME_NAME}-deploy.XXXXXX")"

cleanup() {
  rm -rf "$TMP_DIR"
}
trap cleanup EXIT

require_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Missing required command: $1"
    exit 1
  fi
}

require_cmd docker
require_cmd tar

if [[ ! -f "$THEME_ARCHIVE" ]]; then
  echo "Archive not found: $THEME_ARCHIVE"
  exit 1
fi

if ! docker ps --format '{{.Names}}' | grep -Fxq "$CONTAINER_NAME"; then
  echo "Container is not running: $CONTAINER_NAME"
  exit 1
fi

echo "Extracting archive: $THEME_ARCHIVE"
tar -xzf "$THEME_ARCHIVE" -C "$TMP_DIR"

if [[ ! -d "$TMP_DIR/$THEME_NAME" ]]; then
  echo "Archive must contain top-level directory '$THEME_NAME'"
  exit 1
fi

# Remove macOS metadata files if they exist in archive.
find "$TMP_DIR" -type d -name '__MACOSX' -prune -exec rm -rf {} +
find "$TMP_DIR/$THEME_NAME" -type f -name '._*' -delete
find "$TMP_DIR/$THEME_NAME" -type f -name '.DS_Store' -delete

mkdir -p "$BACKUP_ROOT/$THEME_NAME"
BACKUP_FILE="$BACKUP_ROOT/$THEME_NAME/${THEME_NAME}-${STAMP}.tar.gz"

echo "[1/4] Backing up current theme to $BACKUP_FILE"
docker exec "$CONTAINER_NAME" sh -lc "test -d '$CONTAINER_THEME_PATH'"
docker exec "$CONTAINER_NAME" tar -czf - -C "$THEME_PARENT" "$THEME_NAME" > "$BACKUP_FILE"

echo "[2/4] Replacing theme files"
docker exec "$CONTAINER_NAME" sh -lc "rm -rf '$CONTAINER_THEME_PATH'"
docker cp "$TMP_DIR/$THEME_NAME" "$CONTAINER_NAME:$THEME_PARENT/"

echo "[3/4] Setting ownership and permissions"
docker exec "$CONTAINER_NAME" sh -lc "chown -R www-data:www-data '$CONTAINER_THEME_PATH'"
docker exec "$CONTAINER_NAME" sh -lc "find '$CONTAINER_THEME_PATH' -type d -exec chmod 755 {} \\;"
docker exec "$CONTAINER_NAME" sh -lc "find '$CONTAINER_THEME_PATH' -type f -exec chmod 644 {} \\;"

echo "[4/4] Pruning backups (keep last $KEEP_BACKUPS)"
find "$BACKUP_ROOT/$THEME_NAME" -maxdepth 1 -type f -name '*.tar.gz' -print0 \
  | xargs -0 ls -1t \
  | tail -n "+$((KEEP_BACKUPS + 1))" \
  | xargs -r rm -f

echo "Theme deploy finished successfully"
