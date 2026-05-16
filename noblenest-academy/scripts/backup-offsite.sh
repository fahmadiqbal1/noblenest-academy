#!/usr/bin/env bash
# Off-site rclone backup — runs after the daily local mysqldump cron
# (see docs/deploy/hostinger-kvm4.md §8).
#
# Mirrors /var/backups/*.sql.gz + storage/app/ to a configured remote
# (S3 / Backblaze B2 / Wasabi). Keeps 30 days of dumps off-site.
#
# Cron line (root):
#   45 2 * * * /var/www/noblenest/current/noblenest-academy/scripts/backup-offsite.sh >> /var/log/noblenest-backup.log 2>&1
#
# Setup:
#   apt -y install rclone
#   rclone config            # interactive — create remote `nn-offsite`
#   # Choose Amazon S3 or Backblaze; paste keys.

set -euo pipefail

REMOTE="${NN_BACKUP_REMOTE:-nn-offsite}"
BUCKET="${NN_BACKUP_BUCKET:-noblenest-backups}"
APP_DIR="${NN_APP_DIR:-/var/www/noblenest}"
LOCAL_BACKUPS="/var/backups"
RETENTION_DAYS="${NN_BACKUP_RETENTION:-30}"
STAMP=$(date '+%F')

command -v rclone >/dev/null || { echo "rclone not installed"; exit 1; }

echo "$(date '+%F %T') · syncing dumps → $REMOTE:$BUCKET/dumps/"
rclone copy "$LOCAL_BACKUPS" "$REMOTE:$BUCKET/dumps/" \
    --include 'noblenest-*.sql.gz' \
    --transfers 4 --checkers 4 \
    --log-level INFO

echo "$(date '+%F %T') · syncing storage/app/ → $REMOTE:$BUCKET/storage-app/$STAMP/"
rclone sync "$APP_DIR/current/noblenest-academy/storage/app" \
    "$REMOTE:$BUCKET/storage-app/$STAMP/" \
    --transfers 4 --checkers 4 \
    --log-level INFO

# Prune remote dumps older than retention days.
echo "$(date '+%F %T') · pruning remote dumps older than ${RETENTION_DAYS}d"
rclone delete "$REMOTE:$BUCKET/dumps/" --min-age "${RETENTION_DAYS}d" --log-level INFO || true

echo "$(date '+%F %T') · backup complete"
