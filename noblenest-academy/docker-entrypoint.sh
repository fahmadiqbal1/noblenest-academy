#!/bin/sh
set -e

echo ""
echo "========================================"
echo "  Noble Nest Academy — Container Boot"
echo "========================================"
echo ""

# ── 0. Fix storage + bootstrap/cache ownership for PHP-FPM (www-data) ───────
# Without this, tempnam() fails in storage/framework/views and every request 500s.
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# ── 1. Install vendor if missing (bind-mount dev scenario) ──────────────────
if [ ! -f "/app/vendor/autoload.php" ]; then
    echo "[1/8] Installing Composer dependencies..."
    cd /app
    composer install --no-dev --classmap-authoritative --no-interaction --optimize-autoloader
else
    echo "[1/8] Vendor directory present — skipping composer install."
fi

# ── 2. Wait for MySQL ────────────────────────────────────────────────────────
echo "[2/8] Waiting for MySQL at ${DB_HOST:-db}:${DB_PORT:-3306}..."
RETRIES=30
until mysqladmin ping -h "${DB_HOST:-db}" -P "${DB_PORT:-3306}" \
        -u "${DB_USERNAME:-noblenest}" -p"${DB_PASSWORD}" \
        --connect-timeout=3 --ssl=FALSE --silent 2>/dev/null; do
    RETRIES=$((RETRIES - 1))
    if [ "$RETRIES" -le 0 ]; then
        echo "ERROR: MySQL did not become ready in time. Check your DB_PASSWORD and DB_HOST."
        exit 1
    fi
    echo "  MySQL not ready, retrying in 2s... ($RETRIES attempts left)"
    sleep 2
done
echo "  MySQL is ready."

# ── 3. Wait for Redis ────────────────────────────────────────────────────────
echo "[3/8] Waiting for Redis at ${REDIS_HOST:-redis}:${REDIS_PORT:-6379}..."
RETRIES=15
until redis-cli -h "${REDIS_HOST:-redis}" -p "${REDIS_PORT:-6379}" \
        ${REDIS_PASSWORD:+-a "${REDIS_PASSWORD}"} ping 2>/dev/null | grep -q PONG; do
    RETRIES=$((RETRIES - 1))
    if [ "$RETRIES" -le 0 ]; then
        echo "WARNING: Redis did not respond. Continuing anyway."
        break
    fi
    echo "  Redis not ready, retrying in 2s... ($RETRIES attempts left)"
    sleep 2
done
echo "  Redis is ready."

# ── Pre-artisan: wipe stale package discovery so pail (dev-only) isn't loaded ─
rm -f /app/bootstrap/cache/packages.php /app/bootstrap/cache/services.php

# ── 4. Generate APP_KEY if missing ───────────────────────────────────────────
echo "[4/8] Checking APP_KEY..."
if [ -z "${APP_KEY}" ]; then
    echo "  APP_KEY is empty — generating..."
    php /app/artisan key:generate --force --no-interaction
else
    echo "  APP_KEY is set."
fi

# ── 5. Clear any stale build-time config cache ───────────────────────────────
echo "[5/8] Clearing stale config/route/view caches..."
php /app/artisan config:clear   --quiet
php /app/artisan route:clear    --quiet
php /app/artisan view:clear     --quiet
php /app/artisan cache:clear    --quiet
echo "  Caches cleared."

# ── 6. Run database migrations ───────────────────────────────────────────────
echo "[6/8] Running database migrations..."
php /app/artisan migrate --force --no-interaction
echo "  Migrations complete."

# ── 7. Seed database if activities table is empty ────────────────────────────
echo "[7/8] Checking if database needs seeding..."
SEED_LOCK="/app/storage/.db_seeded"
if [ ! -f "$SEED_LOCK" ]; then
    echo "  Seeding database (first run)..."
    php /app/artisan db:seed --force --no-interaction || true
    touch "$SEED_LOCK"
    echo "  Seeding complete."
else
    echo "  Database already seeded — skipping."
fi

# ── 8. Optimize (cache config/routes/views with runtime env vars) ────────────
echo "[8/8] Optimizing Laravel..."
php /app/artisan storage:link --force 2>/dev/null || true
php /app/artisan optimize
echo "  Optimization complete."

echo ""
echo "========================================"
echo "  Startup complete — launching Supervisor"
echo "========================================"
echo ""

# ── Clear stale Horizon state from Redis ─────────────────────────────────────
# Prevents crash-loop on container restart: old Horizon master records in Redis
# cause the new master to exit with status 1 immediately on boot.
php /app/artisan horizon:clear --quiet 2>/dev/null || true

# Re-chown: artisan commands above ran as root and left root-owned cache files.
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
