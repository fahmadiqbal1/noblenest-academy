#!/usr/bin/env bash
# Phase 11 — rollback helper. Flips `current` to the previous release.
#
# Usage: scripts/rollback.sh
#
# Finds the second-most-recent release and re-points `current` to it.

set -euo pipefail

APP_BASE="${APP_BASE:-/home/noblenestacademy.com}"
RELEASES="${APP_BASE}/releases"

cd "${RELEASES}"

# Sort by mtime desc; pick the 2nd entry (the previous release).
PREV="$(ls -1t | sed -n '2p' || true)"

if [ -z "${PREV}" ]; then
  echo "rollback: no previous release found in ${RELEASES}" >&2
  exit 1
fi

PREV_DIR="${RELEASES}/${PREV}"
echo "rollback: flipping current → ${PREV_DIR}"

ln -sfn "${PREV_DIR}" "${APP_BASE}/current"

sudo systemctl reload php8.3-fpm || true
php "${APP_BASE}/current/artisan" horizon:terminate || true

echo "rollback: done."
