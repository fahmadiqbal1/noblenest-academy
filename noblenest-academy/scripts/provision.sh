#!/usr/bin/env bash
# =============================================================================
# Noble Nest Academy — VPS provisioning (Hostinger KVM4 + CyberPanel + Ubuntu 24.04)
# =============================================================================
# Idempotent. Safe to re-run. Designed to be invoked from the operator's
# local machine; SSH credentials come from `.env.deploy` at repo root
# (gitignored). NEVER prints secrets.
#
# Usage:
#   bash scripts/provision.sh
#
# Required `.env.deploy` keys (loaded but not echoed):
#   DEPLOY_SSH_HOST          — server IP
#   DEPLOY_SSH_USER          — non-root deploy user (created on first run; root used otherwise)
#   DEPLOY_SSH_KEY_PATH      — path to private key (deploy user)
#   ROOT_SSH_PASSWORD        — root password for FIRST RUN ONLY; remove from .env.deploy after success
#   DEPLOY_DOMAIN            — www.noblenestacademy.com
#   CYBERPANEL_URL           — https://IP:8090
#   CYBERPANEL_USER          — admin
#   CYBERPANEL_PASSWORD      — for CLI operations
# =============================================================================
set -euo pipefail

# ── 0. Load .env.deploy ──────────────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
ENV_FILE="$REPO_ROOT/.env.deploy"
if [[ ! -f "$ENV_FILE" ]]; then
  echo "FATAL: $ENV_FILE not found." >&2
  exit 1
fi
set -a; source "$ENV_FILE"; set +a

: "${DEPLOY_SSH_HOST:?DEPLOY_SSH_HOST not set}"
: "${DEPLOY_SSH_KEY_PATH:?DEPLOY_SSH_KEY_PATH not set}"
: "${DEPLOY_DOMAIN:?DEPLOY_DOMAIN not set}"
DEPLOY_SSH_USER="${DEPLOY_SSH_USER:-deploy}"

LOG_DIR="$REPO_ROOT/logs"
mkdir -p "$LOG_DIR"
LOG="$LOG_DIR/provision-$(date +%Y%m%d_%H%M%S).log"
echo "Log: $LOG"
exec > >(tee -a "$LOG") 2>&1

# ── 1. Create deploy user (one-time root bootstrap) ──────────────────────────
echo "==> Step 1: bootstrap deploy user (if needed)"
if [[ ! -f "$DEPLOY_SSH_KEY_PATH" ]]; then
  echo "Generating deploy keypair at $DEPLOY_SSH_KEY_PATH"
  ssh-keygen -t ed25519 -f "$DEPLOY_SSH_KEY_PATH" -N '' -C "deploy@noblenestacademy"
fi
PUBKEY="$(cat "${DEPLOY_SSH_KEY_PATH}.pub")"

if ! ssh -i "$DEPLOY_SSH_KEY_PATH" -o StrictHostKeyChecking=accept-new \
       -o PasswordAuthentication=no -o ConnectTimeout=5 \
       "${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}" true 2>/dev/null; then
  echo "Deploy user not yet provisioned. Running root bootstrap..."
  : "${ROOT_SSH_PASSWORD:?ROOT_SSH_PASSWORD required for first run (remove after success)}"
  if ! command -v sshpass &>/dev/null; then
    echo "sshpass missing. Install: brew install hudochenkov/sshpass/sshpass"; exit 1
  fi
  sshpass -p "$ROOT_SSH_PASSWORD" ssh -o StrictHostKeyChecking=accept-new \
    "root@${DEPLOY_SSH_HOST}" bash -s -- "$DEPLOY_SSH_USER" "$PUBKEY" <<'REMOTE'
    set -euo pipefail
    DEPLOY_USER="$1"; PUBKEY="$2"
    id "$DEPLOY_USER" &>/dev/null || adduser --disabled-password --gecos "" "$DEPLOY_USER"
    usermod -aG sudo "$DEPLOY_USER"
    echo "${DEPLOY_USER} ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/90-${DEPLOY_USER}
    install -d -m 700 -o "$DEPLOY_USER" -g "$DEPLOY_USER" "/home/${DEPLOY_USER}/.ssh"
    echo "$PUBKEY" >> "/home/${DEPLOY_USER}/.ssh/authorized_keys"
    chmod 600 "/home/${DEPLOY_USER}/.ssh/authorized_keys"
    chown "$DEPLOY_USER:$DEPLOY_USER" "/home/${DEPLOY_USER}/.ssh/authorized_keys"
    sed -i 's/^#*PermitRootLogin .*/PermitRootLogin prohibit-password/' /etc/ssh/sshd_config
    sed -i 's/^#*PasswordAuthentication .*/PasswordAuthentication no/' /etc/ssh/sshd_config
    systemctl restart ssh || systemctl restart sshd
    echo "Bootstrap complete; deploy user ready."
REMOTE
  unset ROOT_SSH_PASSWORD
  echo "✓ Deploy user provisioned + root password auth disabled."
else
  echo "✓ Deploy user already reachable via key. Skipping bootstrap."
fi

# ── Helper for subsequent SSH calls ──────────────────────────────────────────
RSSH() { ssh -i "$DEPLOY_SSH_KEY_PATH" "${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}" "$@"; }
RSUDO() { RSSH "sudo $*"; }

# ── 2. UFW firewall ──────────────────────────────────────────────────────────
echo "==> Step 2: UFW firewall (22/80/443/8090)"
RSUDO "ufw allow 22 && ufw allow 80 && ufw allow 443 && ufw allow 8090 && ufw default deny incoming && ufw default allow outgoing && yes | ufw enable" || true

# ── 3. fail2ban ──────────────────────────────────────────────────────────────
echo "==> Step 3: fail2ban for sshd"
RSUDO "apt-get update -qq && apt-get install -y fail2ban && systemctl enable --now fail2ban"

# ── 4. PHP 8.3 + extensions + Composer 2 + Node 22 + Redis 7 ─────────────────
echo "==> Step 4: PHP 8.3 + extensions + Composer + Node 22 + Redis"
RSUDO "apt-get install -y php8.3 php8.3-bcmath php8.3-gd php8.3-intl php8.3-mbstring php8.3-mysql php8.3-redis php8.3-zip php8.3-imagick php8.3-pcntl php8.3-curl php8.3-xml supervisor certbot"
RSSH "php -v | head -1"
RSSH "command -v composer || (curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer)"
RSSH "command -v node || (curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash - && sudo apt-get install -y nodejs)"
RSUDO "apt-get install -y redis-server && systemctl enable --now redis-server"

# ── 5. CyberPanel website + SSL ──────────────────────────────────────────────
echo "==> Step 5: CyberPanel website + Let's Encrypt SSL for ${DEPLOY_DOMAIN}"
RSUDO "cyberpanel createWebsite --domainName ${DEPLOY_DOMAIN} --owner admin --package Default --php 8.3 || true"
RSUDO "cyberpanel issueSSL --domainName ${DEPLOY_DOMAIN} || true"

# ── 6. MySQL database + user ─────────────────────────────────────────────────
echo "==> Step 6: MySQL prod DB + user (password generated server-side, written to /home/${DEPLOY_SSH_USER}/.noblenest.env mode 600)"
RSUDO "test -f /home/${DEPLOY_SSH_USER}/.noblenest.env || {
  DB_PW=\$(openssl rand -base64 24 | tr -d '/+=' | head -c 32)
  RD_PW=\$(openssl rand -base64 24 | tr -d '/+=' | head -c 32)
  HE_TK=\$(openssl rand -base64 24 | tr -d '/+=' | head -c 32)
  mysql -e \"CREATE DATABASE IF NOT EXISTS noblenest_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'noblenest'@'localhost' IDENTIFIED BY '\$DB_PW'; GRANT ALL ON noblenest_prod.* TO 'noblenest'@'localhost'; FLUSH PRIVILEGES;\"
  echo \"DB_PASSWORD=\$DB_PW\" > /home/${DEPLOY_SSH_USER}/.noblenest.env
  echo \"REDIS_PASSWORD=\$RD_PW\" >> /home/${DEPLOY_SSH_USER}/.noblenest.env
  echo \"HEALTH_TOKEN=\$HE_TK\" >> /home/${DEPLOY_SSH_USER}/.noblenest.env
  chown ${DEPLOY_SSH_USER}:${DEPLOY_SSH_USER} /home/${DEPLOY_SSH_USER}/.noblenest.env
  chmod 600 /home/${DEPLOY_SSH_USER}/.noblenest.env
  sed -i \"s/^# *requirepass .*/requirepass \$RD_PW/\" /etc/redis/redis.conf || echo \"requirepass \$RD_PW\" >> /etc/redis/redis.conf
  sed -i 's/^bind .*/bind 127.0.0.1/' /etc/redis/redis.conf
  systemctl restart redis-server
}"

# ── 7. Release + shared directories ──────────────────────────────────────────
echo "==> Step 7: release + shared dirs"
RSUDO "install -d -o ${DEPLOY_SSH_USER} -g ${DEPLOY_SSH_USER} /home/noblenestacademy.com/releases /home/noblenestacademy.com/shared /home/noblenestacademy.com/shared/storage /home/noblenestacademy.com/shared/public-uploads"

# ── 8. Supervisor (Horizon + scheduler) ──────────────────────────────────────
echo "==> Step 8: Supervisor configs"
RSUDO "tee /etc/supervisor/conf.d/laravel-horizon.conf >/dev/null <<EOF
[program:laravel-horizon]
process_name=%(program_name)s
command=php /home/noblenestacademy.com/current/artisan horizon
autostart=true
autorestart=true
user=${DEPLOY_SSH_USER}
redirect_stderr=true
stdout_logfile=/var/log/laravel-horizon.log
stopwaitsecs=3600
EOF"
RSUDO "tee /etc/supervisor/conf.d/laravel-schedule.conf >/dev/null <<EOF
[program:laravel-schedule]
process_name=%(program_name)s
command=php /home/noblenestacademy.com/current/artisan schedule:work
autostart=true
autorestart=true
user=${DEPLOY_SSH_USER}
redirect_stderr=true
stdout_logfile=/var/log/laravel-schedule.log
EOF"
RSUDO "supervisorctl reread && supervisorctl update"

# ── 9. Smoke ─────────────────────────────────────────────────────────────────
echo "==> Step 9: provision smoke checks"
RSSH 'php -v && node -v && command -v composer && command -v supervisorctl'
RSUDO "ls -la /home/noblenestacademy.com/"

echo "==> Provisioning complete. Next: Phase 13 deploy via GitHub Actions."
