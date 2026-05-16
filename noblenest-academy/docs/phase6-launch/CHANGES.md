<!-- markdownlint-disable MD013 -->
# Phase 6 — Tests, Performance, Observability (scaffold)

**Status:** SEO + sitemap landed; full a11y / Sentry / N+1 audit are follow-ups.

## Done in this scaffold

- [x] **`sitemap.xml` endpoint** — [SitemapController](../../app/Http/Controllers/SitemapController.php), routed at `/sitemap.xml`. Lists 6 public URLs (`/`, `/pricing`, `/login`, `/register`, `/terms`, `/privacy`) with `hreflang` alternates for each of the 8 supported locales. Cached for 1 hour via `Cache::remember`.
- [x] **`robots.txt`** rewritten to whitelist `/` and explicitly `Disallow` `/admin/`, `/privacy/`, `/webhook/`, `/api/`, `/_styleguide`, `/onboarding/`, `/billing/`, `/payment/`. Points to `/sitemap.xml`.
- [x] **Structured data** — `<x-seo.organization-jsonld />` Blade component emits `EducationalOrganization` schema.org JSON-LD on every page (added to `layouts/partials/head.blade.php`). Carries name, URL, logo, multilingual `inLanguage` list, audience ages 0–10, and social profile placeholders from env.

## Phase 6 follow-ups (for the next commit on this branch)

| # | Work | Notes |
|---|---|---|
| 1 | Sentry integration | `composer require sentry/sentry-laravel`, set `SENTRY_LARAVEL_DSN`, hook into `bootstrap/app.php` exception report. |
| 2 | Fix the ~65 currently-failing application tests | Most need fixture seeding; some need CSRF middleware setup. |
| 3 | Dusk browser tests for the 3 critical paths | register → onboard → activity → complete; checkout → webhook → activation; admin → curriculum view. |
| 4 | N+1 audit via `barryvdh/laravel-debugbar` in `local` | Fix every offender surfaced. |
| 5 | `/health/detailed` Stripe API connectivity check | Already exists; verify it actually pings Stripe. |
| 6 | Replace pa11y-ci with `@axe-core/cli` | Dodges the HTMLCS Puppeteer crash that blocked 2 / 6 URLs in Phase 1's audit. |
