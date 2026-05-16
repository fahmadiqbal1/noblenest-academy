<!-- markdownlint-disable MD013 -->
# Phase 7 — Deployment to Hostinger KVM4 + CI/CD (scaffold)

**Status:** runbook landed; CI/CD pipeline + zero-downtime deploy script are follow-ups.

## Done in this scaffold

- [x] **[docs/deploy/hostinger-kvm4.md](../deploy/hostinger-kvm4.md)** — full Ubuntu 24.04 runbook: server hardening (ufw, fail2ban, ssh disable-root), PHP 8.3 / Nginx / MySQL 8 / Redis 7 / Node 18 / supervisor / certbot, app clone + `.env` + `composer install` + `npm run fonts` + `npm run build` + migrate + seed + content:generate + stripe:sync-prices, Nginx vhost, HTTPS via certbot, Horizon under supervisor, daily mysqldump backup cron with 14-day rotation. **Total wall time on a fresh KVM4: ~25 min.**

## Phase 7 follow-ups (for the next commit on this branch)

| # | Work | Notes |
|---|---|---|
| 1 | `scripts/deploy.sh` zero-downtime deploy script | Atomic-symlink release strategy: `releases/<sha>` + `current` symlink + `php artisan migrate --force` + cache warm + restart php-fpm + horizon. |
| 2 | `.github/workflows/ci.yml` | PHP CS Fixer + PHPStan level 6 + `php artisan test` + `npm run build` + `npm run a11y` + `npm run lighthouse`. |
| 3 | `.github/workflows/deploy.yml` | SSH-deploys to KVM4 on green main. |
| 4 | `rclone` off-server backup of `/var/backups/*.sql.gz` + `storage/app/` to S3 / B2. |
| 5 | Confirm `SecureCredentialsManager::rotate()` works end-to-end. |
