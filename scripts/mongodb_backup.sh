#!/usr/bin/env bash
set -euo pipefail
: "${MONGODB_URI:?Set MONGODB_URI in environment}"
BACKUP_DIR="${BACKUP_DIR:-storage/backups/mongodb}"
mkdir -p "$BACKUP_DIR"
STAMP=$(date +%Y%m%d_%H%M%S)
mongodump --uri="$MONGODB_URI" --archive="$BACKUP_DIR/rrr_$STAMP.archive" --gzip
echo "Backup saved: $BACKUP_DIR/rrr_$STAMP.archive"
