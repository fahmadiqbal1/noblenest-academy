<!-- markdownlint-disable MD013 -->
# Deploy to Hostinger KVM4 (Ubuntu 24.04)

**Target:** a fresh KVM4 VPS → live HTTPS-secured Noble Nest Academy in ≤ 30 minutes.

This doc is a **runbook**, not narrative. Every step is a paste-and-run shell block. Assumes you have `ssh root@<vps-ip>` access and a registered domain whose DNS A record points at the VPS.

## 0. Prerequisites

- Domain (e.g. `noblenest.example`) with DNS A record → VPS public IP.
- Stripe live + restricted keys + webhook secret (from dashboard).
- Resend (or equivalent) API key for transactional email.
- A `git` remote you can clone from on the VPS (GitHub deploy key or HTTPS PAT).

## 1. Base server hardening (10 min)

```bash
ssh root@<vps-ip>
apt update && apt -y upgrade
apt -y install ufw fail2ban unattended-upgrades curl gnupg ca-certificates
dpkg-reconfigure -plow unattended-upgrades       # accept defaults
ufw default deny incoming && ufw default allow outgoing
ufw allow OpenSSH && ufw allow http && ufw allow https
ufw --force enable
systemctl enable --now fail2ban
adduser --disabled-password --gecos "" deploy
usermod -aG sudo deploy
rsync --archive --chown=deploy:deploy ~/.ssh /home/deploy
# Disable root SSH login + password auth:
sed -i 's/^#\?PermitRootLogin .*/PermitRootLogin no/; s/^#\?PasswordAuthentication .*/PasswordAuthentication no/' /etc/ssh/sshd_config
systemctl restart ssh
exit                                            # reconnect as `deploy` from here on
```

## 2. PHP 8.3 / Nginx / MySQL 8 / Redis 7 / Node 18 (8 min)

```bash
# PHP 8.3 + extensions
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt -y install php8.3-fpm php8.3-cli php8.3-mysql php8.3-redis php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl php8.3-imagick

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Nginx
sudo apt -y install nginx

# MySQL 8
sudo apt -y install mysql-server
sudo mysql_secure_installation                  # interactive — set root pw

# Redis 7
sudo apt -y install redis-server
sudo systemctl enable --now redis-server

# Node 18 (for `npm run build` + `npm run fonts`)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt -y install nodejs

# Supervisor (Horizon)
sudo apt -y install supervisor

# Certbot
sudo apt -y install certbot python3-certbot-nginx
```

## 3. Database + user

```bash
sudo mysql <<SQL
CREATE DATABASE noblenest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'noblenest'@'localhost' IDENTIFIED BY 'CHANGEME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON noblenest.* TO 'noblenest'@'localhost';
FLUSH PRIVILEGES;
SQL
```

## 4. App clone + bootstrap

```bash
sudo mkdir -p /var/www/noblenest
sudo chown deploy:deploy /var/www/noblenest
cd /var/www/noblenest
git clone <repo-url> repo
cd repo/noblenest-academy
cp .env.example .env

# Fill .env: APP_KEY (generate), DB_*, REDIS_*, STRIPE_*, RESEND_API_KEY, APP_URL=https://noblenest.example
nano .env
php artisan key:generate --force

composer install --no-dev --optimize-autoloader
npm ci
npm run fonts                                   # downloads WOFF2 → public/fonts/
npm run build

php artisan migrate --force
php artisan db:seed --force --class=Database\\Seeders\\AssessmentBatterySeeder
php artisan db:seed --force --class=Database\\Seeders\\CulturalModulesSeeder
php artisan db:seed --force --class=Database\\Seeders\\StemPathwaySeeder
php artisan content:generate database/seed-data/toddler-activities.csv
php artisan stripe:sync-prices

php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## 5. Nginx vhost

```bash
sudo tee /etc/nginx/sites-available/noblenest >/dev/null <<NGINX
server {
    listen 80;
    server_name noblenest.example;
    root /var/www/noblenest/repo/noblenest-academy/public;
    index index.php index.html;

    add_header X-Frame-Options SAMEORIGIN;
    add_header X-Content-Type-Options nosniff;
    add_header Referrer-Policy strict-origin-when-cross-origin;

    location / { try_files \$uri \$uri/ /index.php?\$query_string; }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
    }

    location ~ /\.(?!well-known).* { deny all; }
    location ~ ^/(storage|build|fonts)/ { try_files \$uri =404; expires 30d; }
}
NGINX

sudo ln -sf /etc/nginx/sites-available/noblenest /etc/nginx/sites-enabled/noblenest
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx

# Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
```

## 6. HTTPS

```bash
sudo certbot --nginx -d noblenest.example --redirect --hsts --email ops@noblenest.example --agree-tos --non-interactive
```

## 7. Horizon (queues)

```bash
sudo tee /etc/supervisor/conf.d/noblenest-horizon.conf >/dev/null <<HORIZON
[program:noblenest-horizon]
process_name=%(program_name)s
command=php /var/www/noblenest/repo/noblenest-academy/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/horizon.log
stopwaitsecs=3600
HORIZON

sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start noblenest-horizon
```

## 8. Daily backups (cron)

```bash
sudo tee /etc/cron.d/noblenest-backup >/dev/null <<CRON
30 2 * * * www-data /usr/bin/mysqldump --single-transaction --quick noblenest | gzip > /var/backups/noblenest-\$(date +\%F).sql.gz && /usr/bin/find /var/backups -name 'noblenest-*.sql.gz' -mtime +14 -delete
CRON
sudo mkdir -p /var/backups && sudo chown www-data:www-data /var/backups
```

## 9. Stripe webhook endpoint

In the Stripe dashboard (Developers → Webhooks → Add endpoint):

- Endpoint URL: `https://noblenest.example/webhook/stripe`
- Events: `checkout.session.completed`, `customer.subscription.updated`, `customer.subscription.deleted`, `invoice.payment_succeeded`, `invoice.payment_failed`, `customer.updated`
- Copy the signing secret into `.env` as `STRIPE_WEBHOOK_SECRET`, then `php artisan config:cache`.

## 10. Smoke test

```bash
curl -fsSL https://noblenest.example/up                   # health: 200
curl -fsSL https://noblenest.example/health/detailed      # detailed
curl -fsSL https://noblenest.example/sitemap.xml | head   # sitemap renders
curl -fsSL https://noblenest.example/                     # home: 200
```

If all four return HTTP 200, the site is live. Total wall time on a fresh KVM4: ~25 min.

## Zero-downtime updates (after first deploy)

`scripts/deploy.sh` (lands in Phase 7 follow-up) automates: pull → `composer install` → `npm run build` → `php artisan migrate --force` → cache:clear → cache:warm → atomic symlink → restart php-fpm + horizon. Use it from your local machine via `ssh deploy@noblenest bash -s < scripts/deploy.sh`.
