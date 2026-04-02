#!/usr/bin/env bash
# =============================================================
# Noble Nest Academy — Production Deploy Script
# Run on your VPS as the "deploy" user:
#   bash /var/www/noblenest/scripts/deploy.sh
# =============================================================

set -euo pipefail

APP_DIR="/var/www/noblenest"
SIDECAR_DIR="$APP_DIR/services/curriculum-ai"
PHP="php8.3"

echo "=============================="
echo " Noble Nest Deploy — $(date)"
echo "=============================="

cd "$APP_DIR"

# 1. Pull latest code
echo "[1/8] Pulling latest code from GitHub..."
git pull origin main

# 2. Install PHP dependencies (no dev packages in production)
echo "[2/8] Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction --quiet

# 3. Run any new database migrations
echo "[3/8] Running database migrations..."
$PHP artisan migrate --force

# 4. Cache all Laravel config for speed
echo "[4/8] Caching Laravel config, routes, views, events..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache
$PHP artisan icons:cache 2>/dev/null || true

# 5. Ensure storage symlink exists
echo "[5/8] Checking storage symlink..."
$PHP artisan storage:link --quiet 2>/dev/null || true

# 6. Update Python sidecar dependencies
echo "[6/8] Updating curriculum AI sidecar..."
cd "$SIDECAR_DIR"
pip install -q -r requirements.txt

# 7. Restart services
echo "[7/8] Restarting PHP-FPM and queue workers..."
cd "$APP_DIR"
sudo systemctl reload php8.3-fpm
sudo supervisorctl restart noblenest-horizon:*
sudo supervisorctl restart noblenest-curriculum-ai 2>/dev/null || true

# 8. Gracefully restart Horizon (waits for running jobs to finish)
echo "[8/8] Restarting Horizon..."
$PHP artisan horizon:terminate

echo ""
echo "=============================="
echo " Deploy complete!"
echo "=============================="
