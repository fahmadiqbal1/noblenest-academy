# Launch Readiness Checklist — Noble Nest Academy

One-page pre-launch checklist. Work through top-to-bottom before cutting the production deploy.

---

## 1. Environment

- [ ] `APP_ENV=production` in production `.env`
- [ ] `APP_DEBUG=false` in production `.env`
- [ ] `APP_KEY=` is set (non-empty; run `php artisan key:generate` if missing)
- [ ] `APP_URL=` matches the live domain (including `https://`)
- [ ] `HEALTH_TOKEN=` is set to a strong random string (used by `/health/detailed`)
- [ ] `DB_PASSWORD=` is set to a strong unique password (not `CHANGE_ME_STRONG_PASSWORD`)
- [ ] `REDIS_PASSWORD=` set if Redis auth is enabled on VPS
- [ ] `STRIPE_KEY=`, `STRIPE_SECRET=`, `STRIPE_WEBHOOK_SECRET=` all set for live mode
- [ ] `PAYPAL_CLIENT_ID=`, `PAYPAL_SECRET=` set; `PAYPAL_MODE=live`
- [ ] `MAIL_HOST=`, `MAIL_USERNAME=`, `MAIL_PASSWORD=` set for transactional email
- [ ] `DAILY_CO_API_KEY=`, `DAILY_CO_DOMAIN=` set for live class feature
- [ ] `ANTHROPIC_API_KEY=` set (AI assistant)
- [ ] `VAPID_PUBLIC_KEY=` and `VAPID_PRIVATE_KEY=` generated (`php artisan web-push:generate-keys`)

## 2. Assets

- [ ] `npm install` run (installs `pa11y-ci`, `@lhci/cli`, `vite-plugin-image-optimizer`)
- [ ] `npm run build` exits 0 (Vite build green, no errors)
- [ ] Font files downloaded to `public/fonts/` (see `resources/fonts/README.md` and `docs/LAUNCH_TODO.md`)
- [ ] Images optimized (see `docs/LAUNCH_TODO.md` for enabling `vite-plugin-image-optimizer`)
- [ ] `public/build/` committed or deployed to VPS (if not using Vite in Docker)

## 3. Database

- [ ] All migrations applied: `php artisan migrate --force`
- [ ] Production seeders safe: `DatabaseSeeder` now gates `Password1!` test accounts behind `local/testing` environment — content seeders run unconditionally
- [ ] Verify no `.noblenest.test` email accounts exist in production DB: `SELECT email FROM users WHERE email LIKE '%noblenest.test%';`
- [ ] Feature flags configured via Admin panel (if applicable)
- [ ] `storage/.db_seeded` lock file present (prevents re-seeding on container restart)

## 4. Monitoring & Health

- [ ] `GET /health` returns `200 OK` (unauthenticated)
- [ ] `GET /health/detailed` with `Authorization: Bearer <HEALTH_TOKEN>` returns `200` with JSON status
- [ ] Laravel Horizon running (`php artisan horizon` or via Supervisor)
- [ ] Queue workers processing: check `horizon` dashboard at `/horizon`
- [ ] Log channel set to `daily` and `LOG_LEVEL=error` in production

## 5. Security

- [ ] `SecurityHeaders` middleware active — verify response headers include `X-Frame-Options`, `X-Content-Type-Options`, `Content-Security-Policy`
- [ ] CSP allows Stripe (`js.stripe.com`), PayPal (`*.paypal.com`), Daily.co (`*.daily.co`), Alpine CDN (`cdn.jsdelivr.net`)
- [ ] `SESSION_SECURE_COOKIE=true` and `SESSION_SAME_SITE=lax`
- [ ] HTTPS enforced (Nginx redirects `http://` → `https://`)
- [ ] `.env` file NOT web-accessible (Nginx `deny all` for `\.env$` pattern)

## 6. Accessibility

- [ ] `npm run a11y` passes (pa11y-ci WCAG2AA on all 6 URLs in `.pa11yci.json`)
- [ ] Manual keyboard-only navigation test: Tab through `/`, `/pricing`, `/login`, `/register`
- [ ] Screen reader spot-check: VoiceOver (macOS) or NVDA (Windows) on landing page and auth flow

## 7. Performance (Lighthouse)

- [ ] `npm run lighthouse` passes: Performance ≥ 90, Accessibility ≥ 95, Best Practices ≥ 95, SEO ≥ 95
- [ ] Font files present in `public/fonts/` (preload `<link>` tags are already in marketing/auth/app layouts)
- [ ] `php artisan optimize` run post-deploy (`config:cache`, `route:cache`, `view:cache`, `event:cache`)

## 8. Manual Smoke-Test Script

Run this end-to-end flow after every production deploy:

1. **Register** → `POST /register` with a new email → confirm redirect to onboarding
2. **Onboarding** → complete parent onboarding steps → confirm redirect to parent dashboard
3. **Parent Dashboard** → verify welcome card, progress ring, activity feed render
4. **Add Child** → fill child profile form → confirm child appears in dashboard
5. **Child Dashboard** → switch to child view → verify age-tier styling and activity cards
6. **Activity** → open any activity card → confirm step-player loads
7. **Complete Step** → advance through all steps → confirm completion badge/toast
8. **Pricing** → visit `/pricing` → confirm tier cards and CTA buttons render (no broken styles)
9. **Logout** → confirm redirect to `/login` or marketing home
10. **Health check** → `curl -s http://yourdomain.com/health` → confirm `{"status":"ok"}`

## 9. Rollback Plan

| Item | Value |
|------|-------|
| Previous deploy SHA | Run `git log --oneline -5` on VPS before deploying |
| DB backup location | `/var/backups/noblenest/db-<date>.sql.gz` (run `mysqldump` before migrations) |
| Feature-flag kill switch | Admin panel → Feature Flags → disable per-feature; or set `APP_MAINTENANCE_DRIVER=file` and run `php artisan down` |
| Rollback command | `git checkout <previous-sha> && php artisan optimize && sudo systemctl reload php8.3-fpm` |

---

## Items Requiring Human Action Before Launch (Top 5)

1. **Download fonts** to `public/fonts/` — preload tags are wired, but files are missing. System-ui fallback is active. Run the script in `resources/fonts/README.md`.
2. **Set `HEALTH_TOKEN`** in production `.env` — required for `/health/detailed` to return data (returns 401 without it).
3. **Configure live Stripe keys** — `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` must be live-mode values. Also verify Stripe webhook endpoint is registered in Stripe dashboard pointing to `https://yourdomain.com/stripe/webhook`.
4. **Enable image optimization** — uncomment `ViteImageOptimizer` in `vite.config.js` after confirming `sharp` is available in the build environment. See `docs/LAUNCH_TODO.md`.
5. **Run `npm install`** on the server/CI — `pa11y-ci`, `@lhci/cli`, and `vite-plugin-image-optimizer` were added to `package.json` but require an install pass before `npm run build` or `npm run a11y` will work.
