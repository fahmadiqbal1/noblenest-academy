#!/usr/bin/env bash
# Phase 11 — production deploy helper (invoked by .github/workflows/deploy.yml
# AND runnable locally on the VPS).
#
# Usage: scripts/deploy.sh <release-id>
#
# Layout on the VPS:
#   /home/noblenestacademy.com/
#     releases/<id>/          ← extracted release tarball
#     shared/.env             ← persistent secrets
#     shared/storage/         ← persistent storage
#     shared/public-uploads/  ← persistent user uploads
#     current  → releases/<id>   (atomic symlink flip)
#
# A previous, more elaborate version is preserved at scripts/deploy.sh.legacy
# for reference (git-pull-based zero-downtime flow).

set -euo pipefail

RELEASE_ID="${1:?need release id}"
APP_BASE="${APP_BASE:-/home/noblenestacademy.com}"
REL_DIR="${APP_BASE}/releases/${RELEASE_ID}"
SHARED="${APP_BASE}/shared"

cd "${REL_DIR}"

# Symlink shared resources into the new release.
ln -sfn "${SHARED}/.env"           "${REL_DIR}/.env"
ln -sfn "${SHARED}/storage"        "${REL_DIR}/storage"
ln -sfn "${SHARED}/public-uploads" "${REL_DIR}/public/uploads"

php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan event:cache
php artisan view:cache
php artisan route:cache
php artisan config:cache

# Atomic symlink flip.
ln -sfn "${REL_DIR}" "${APP_BASE}/current"

# Reload php-fpm + Horizon.
sudo systemctl reload php8.3-fpm
php "${APP_BASE}/current/artisan" horizon:terminate || true

# Prune all but last 5 releases.
cd "${APP_BASE}/releases"
ls -1t | tail -n +6 | xargs -r rm -rf
