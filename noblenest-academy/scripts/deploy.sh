#!/usr/bin/env bash
# Zero-downtime production deploy for Noble Nest Academy on Hostinger KVM4.
# See docs/deploy/hostinger-kvm4.md for the initial server setup.
#
# Strategy:
#   - Clone HEAD (or a specific SHA) into releases/<timestamp>/
#   - Symlink shared .env + storage + public/fonts
#   - composer + npm + migrate + cache warm
#   - Atomic mv -T of the `current` symlink
#   - Reload php-fpm + horizon
#   - Prune old releases
#
# Usage (on the VPS as the `deploy` user):
#   bash scripts/deploy.sh             # deploy HEAD of $NN_BRANCH
#   bash scripts/deploy.sh <sha>       # deploy a specific commit
#
# Env (export or write to /etc/noblenest/deploy.env):
#   NN_APP_DIR        absolute path to the app root (default /var/www/noblenest)
#   NN_REPO           git clone URL (required)
#   NN_BRANCH         git branch to deploy (default feature/lms-scaffold)
#   NN_PHP_FPM_SVC    systemd service name (default php8.3-fpm)
#   NN_HORIZON_SVC    supervisor program (default noblenest-horizon)
#   NN_KEEP_RELEASES  number of releases to retain (default 5)

set -euo pipefail

NN_APP_DIR="${NN_APP_DIR:-/var/www/noblenest}"
NN_REPO="${NN_REPO:?NN_REPO must be set (git clone URL)}"
NN_BRANCH="${NN_BRANCH:-feature/lms-scaffold}"
NN_PHP_FPM_SVC="${NN_PHP_FPM_SVC:-php8.3-fpm}"
NN_HORIZON_SVC="${NN_HORIZON_SVC:-noblenest-horizon}"
NN_KEEP_RELEASES="${NN_KEEP_RELEASES:-5}"
NN_INNER="noblenest-academy"

TARGET_SHA="${1:-}"

log()  { echo "$(date '+%F %T') · $*"; }
fail() { echo "deploy failed: $*" >&2; exit 1; }

RELEASES_DIR="$NN_APP_DIR/releases"
CURRENT_LINK="$NN_APP_DIR/current"
SHARED_DIR="$NN_APP_DIR/shared"
STAMP=$(date '+%Y%m%d%H%M%S')
RELEASE_DIR="$RELEASES_DIR/$STAMP"

mkdir -p "$RELEASES_DIR" "$SHARED_DIR/storage" "$SHARED_DIR/public/fonts"
[ -f "$SHARED_DIR/.env" ] || fail "shared/.env missing — copy production .env to $SHARED_DIR/.env first"

log "cloning $NN_REPO ($NN_BRANCH) → $RELEASE_DIR"
git clone --branch "$NN_BRANCH" --depth 1 "$NN_REPO" "$RELEASE_DIR"
cd "$RELEASE_DIR/$NN_INNER"

if [ -n "$TARGET_SHA" ]; then
    log "checking out $TARGET_SHA"
    git fetch --depth 1 origin "$TARGET_SHA"
    git checkout "$TARGET_SHA"
fi
DEPLOYED_SHA=$(git rev-parse --short HEAD)
log "deploying $DEPLOYED_SHA"

# Shared symlinks.
ln -sfn "$SHARED_DIR/.env"          .env
rm -rf storage
ln -sfn "$SHARED_DIR/storage"       storage
ln -sfn "$SHARED_DIR/public/fonts"  public/fonts

log "composer install --no-dev --optimize-autoloader"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

log "npm ci + fonts + build"
npm ci
npm run fonts || log "WARN: font fetch failed — continuing with existing files"
npm run build

log "migrate + cache warm"
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

log "atomic swap: current → $RELEASE_DIR"
ln -sfn "$RELEASE_DIR" "$CURRENT_LINK.new"
mv -T "$CURRENT_LINK.new" "$CURRENT_LINK"

log "reload $NN_PHP_FPM_SVC"
sudo systemctl reload "$NN_PHP_FPM_SVC"

if command -v supervisorctl >/dev/null 2>&1; then
    log "restart $NN_HORIZON_SVC"
    sudo supervisorctl restart "$NN_HORIZON_SVC" || log "WARN: horizon restart skipped"
fi

log "stripe:sync-prices (idempotent)"
php artisan stripe:sync-prices || log "WARN: stripe:sync-prices failed — review manually"

log "pruning releases (keep $NN_KEEP_RELEASES)"
cd "$RELEASES_DIR"
ls -1t | tail -n +$((NN_KEEP_RELEASES + 1)) | xargs -r rm -rf

log "deploy complete — current → $DEPLOYED_SHA"
