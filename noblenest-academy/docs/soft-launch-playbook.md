<!-- markdownlint-disable MD013 -->
# Soft-launch playbook

For the 7-day friends-and-family beta. Every step below has an owner; if no
owner is named, default to the on-call engineer.

## T−7 days

- [ ] Run `php artisan stripe:sync-prices` against the **test** account.
- [ ] Verify `RESEND_API_KEY`, `SENTRY_LARAVEL_DSN`, `STRIPE_WEBHOOK_SECRET` are present in production `.env`.
- [ ] Confirm `/up`, `/health/detailed`, `/sitemap.xml` all return 200 from the public domain.
- [ ] Add Sentry: `composer require sentry/sentry-laravel && php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"`.
- [ ] Add Plausible: set `PLAUSIBLE_DOMAIN` in `.env`, `php artisan config:cache`.
- [ ] Send invites to the beta cohort (target: 5 families).
- [ ] Pre-load each invitee's email into Stripe as a `customer` so Customer Portal sign-in is one-click.

## T−2 days

- [ ] Final database backup before the beta (`mysqldump` + rclone offsite — `scripts/backup-offsite.sh`).
- [ ] Set Horizon supervisor to `autostart=true`.
- [ ] Synthetic soak test: a script that hits 6 public URLs every 5 min for 24h. Watch Horizon backlog stay at 0 and Sentry remain quiet.
- [ ] CI green (`php artisan test` of the 4 gated test files; `npm run build`).
- [ ] **Status page** live at https://status.noblenest.example (uptime-kuma or Better Stack).

## T−0 — Launch morning

- [ ] On-call rotation set for the week. Names + Slack handles + phone numbers in `#nn-oncall-week`.
- [ ] Stripe test → live mode flip (move `STRIPE_*` env vars to the live keys; run `php artisan stripe:sync-prices` against live).
- [ ] Send the "doors open" email to the cohort.
- [ ] Tail-watch:
  - `tail -f storage/logs/laravel.log`
  - Sentry "Issues" view filtered to `environment:production` last 24h
  - Stripe dashboard → Events
  - `/health/detailed` every 60 s via a uptime-kuma check

## During the week

- [ ] Daily 15-min standup at 09:00 UTC. Anyone can call an incident.
- [ ] **Severity bands:**
  - **P1**: site can't render or accept payments. Page on-call immediately. Rollback via `scripts/deploy.sh <previous-sha>` is the default action.
  - **P2**: a feature is broken for a known cohort. Hotfix + deploy within 24 h.
  - **P3**: cosmetic / nice-to-have. Backlog item.
- [ ] Daily user feedback synthesis from email + Posthog / Plausible custom event funnel.

## Rollback procedure

```bash
# On the VPS as the deploy user:
ssh deploy@noblenest.example
cd /var/www/noblenest
# Show last 5 releases:
ls -1t releases | head -5
# Atomic-symlink-swap to a previous release:
ln -sfn /var/www/noblenest/releases/<previous-stamp> /var/www/noblenest/current.new
mv -T /var/www/noblenest/current.new /var/www/noblenest/current
sudo systemctl reload php8.3-fpm
sudo supervisorctl restart noblenest-horizon
# Verify:
curl -fsSL https://noblenest.example/up
```

This is the same path `scripts/deploy.sh` uses for forward deploys; pointing the
symlink backwards is the cleanest revert.

## End of week

- [ ] Retro: what went well, what broke, what's next.
- [ ] Decide on public launch (T+7) or extend beta.
- [ ] Move blocking findings into GitHub issues with the `soft-launch-blocker` label.
- [ ] Update `docs/phase8-launch/CHANGES.md` "follow-ups" section with anything new.
