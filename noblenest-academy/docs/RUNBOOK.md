# Noble Nest Academy — Operations Runbook

**Audience:** on-call engineer / operator.
**Last updated:** Phase 9 (2026-05-17).
**See also:** `docs/SPEC.md` (product), `docs/AUDIT.md` (state per phase), `docs/deploy/hostinger-kvm4.md` (server topology).

---

## 1. Quick health check (from anywhere)

```bash
curl -fsS https://www.noblenestacademy.com/health
# {"status":"healthy",...}

curl -fsS -H "Authorization: Bearer $HEALTH_TOKEN" \
     https://www.noblenestacademy.com/health/detailed | jq .
# Detailed checks: database, cache, queue, opcache, external APIs.
```

`HEALTH_TOKEN` lives in the server `.env`. 401 means missing/wrong token; 503 means one or more checks failing.

---

## 2. Deploy / Rollback

Phase 13's `scripts/deploy.sh` (CI-invoked) does atomic releases under `/home/noblenestacademy.com/releases/{ts}/` and flips a `current` symlink. Five releases retained.

- **Manual rollback (last good release):**
  ```bash
  ssh deploy@$DEPLOY_SSH_HOST
  cd /home/noblenestacademy.com/releases
  PREV=$(ls -1t | sed -n '2p')
  ln -sfn "/home/noblenestacademy.com/releases/$PREV" /home/noblenestacademy.com/current
  sudo systemctl reload php8.3-fpm
  php /home/noblenestacademy.com/current/artisan horizon:terminate
  ```
- **Push a hotfix without CI:** build locally, `scp release-*.tar.gz deploy@host:/tmp/`, extract under `releases/{ts}/`, flip symlink as above.

---

## 3. Database restore from backup

Backups land in `storage/app/backups/db-YYYY-MM-DD_HHmmss.sql.gz` daily at 02:00 server time (`backup:db --retention=30`). 30-day retention.

```bash
ssh deploy@$DEPLOY_SSH_HOST
cd /home/noblenestacademy.com/current
LATEST=$(ls -1t storage/app/backups/db-*.sql.gz | head -1)
gunzip < "$LATEST" | mysql -unoblenest -p$DB_PASSWORD noblenest_prod
```

To restore to a specific timestamp: pass the matching file name.

---

## 4. Rotate keys

| Key | Where | After rotating |
|---|---|---|
| `APP_KEY` | `.env` | redeploy (sessions invalidated). Generate: `php artisan key:generate --show`. |
| `HEALTH_TOKEN` | `.env` | restart php-fpm; update any external monitor configs. |
| `GROQ_API_KEY` | `.env` | restart php-fpm + `php artisan horizon:terminate`. |
| `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET` | `.env` | restart php-fpm; re-verify Stripe webhook URL in dashboard. |
| `PAYPAL_CLIENT_ID` / `PAYPAL_SECRET` | `.env` | restart php-fpm. |
| `HEYGEN_API_KEY` / `SYNTHESIA_API_KEY` / `WHISPER_API_KEY` | `.env` | `horizon:terminate` only — only used by queued jobs. |
| `SENTRY_LARAVEL_DSN` | `.env` (when wired) | restart php-fpm. |
| MySQL / Redis passwords | `/home/deploy/.noblenest.env` AND `.env` | restart php-fpm + horizon + redis-cli auth check. |

`composer audit && npm audit --omit=dev` should be re-run after any rotation that touches packages.

---

## 5. Reseed AI content

Run the backfill any time activities lack localized media:

```bash
php artisan content:backfill-media --locale=all --limit=50 --cost-cap=5.00
# Dispatches ProduceLocalizedVideoJob(s) onto the Horizon queue. Watch /horizon.
```

Provider switching:
```bash
# In .env
VIDEO_AVATAR_DRIVER=null|heygen|synthesia
WHISPER_DRIVER=local|openai
```

Driver flip requires `php artisan horizon:terminate`.

---

## 6. Scale Horizon

```bash
supervisorctl status horizon
sudo supervisorctl restart horizon       # last resort; usually horizon:terminate is enough
php artisan horizon:terminate            # gracefully restarts workers; picks up new code
php artisan horizon:status               # is the master process up?
```

`config/horizon.php` declares the worker pool sizes per env (production should be sized for the queue depth; CyberPanel KVM4 default = 4 workers).

If the queue is backed up:
```bash
php artisan queue:retry all              # retry failed jobs
php artisan queue:flush                  # nuke the failed_jobs table (last resort)
```

---

## 7. Logs

- Local dev: `storage/logs/laravel-YYYY-MM-DD.log` (plain text).
- Production: JSON-structured (one event per line). Tail with `tail -f storage/logs/laravel-*.log | jq .`.
- Every log line carries a `request_id` (UUID, also echoed in the `X-Request-Id` response header). Grep by request_id to reconstruct a request.

---

## 8. Sentry (when wired — Phase 9.1)

`SENTRY_LARAVEL_DSN=…` enables capture. Without a DSN, the SDK no-ops (CI/local stays quiet).

PII scrubber runs `before_send` and strips `name/email/phone/address/parental_consent_*/ip` from event payload before transmission.

Look at: https://sentry.io/organizations/noblenestacademy/issues/ (or GlitchTip equivalent).

---

## 9. Common incidents

| Symptom | First check | Fix |
|---|---|---|
| `/health/detailed` returns 503 | jq the JSON for failing check | Per-check fix below |
| `database: fail` | `mysql -unoblenest -p… -e 'SELECT 1'` | restart MySQL service or check disk |
| `cache: fail` (Redis) | `redis-cli -a $REDIS_PASSWORD PING` | restart redis service |
| `queue: backlog` | `/horizon` dashboard | `php artisan horizon:terminate` to reload workers |
| AI provider 5xx storm | flip driver to `null` in `.env`, `horizon:terminate` | re-run `content:backfill-media` after recovery |
| Stripe webhook 4xx | Dashboard → Webhooks → see failed deliveries | check `stripe_webhook_events` table + `STRIPE_WEBHOOK_SECRET` |
| Auth lockouts | `RateLimiter` named limits; default `auth` is 5/min | wait or `php artisan cache:clear` (nukes ALL throttles) |
| Disk full | `df -h /home/noblenestacademy.com` | prune `storage/logs/`, prune `storage/app/backups/` |

---

## 10. Where to look

| | URL |
|---|---|
| Horizon dashboard | https://www.noblenestacademy.com/horizon (admin only) |
| Health | https://www.noblenestacademy.com/health |
| Detailed health | https://www.noblenestacademy.com/health/detailed (Bearer $HEALTH_TOKEN) |
| CyberPanel | https://157.173.210.224:8090 |
| GitHub branch | https://github.com/fahmadiqbal1/noblenest-academy/tree/release/v1-launch |
| Release PR | https://github.com/fahmadiqbal1/noblenest-academy/pull/11 |
