# Project Guidelines — Noble Nest Academy

Short answer to the current question: we are fixing and completing the Copilot‑generated app already in this repository. We do not rebuild from scratch unless a minimal bootstrap is necessary to run, test, or migrate the existing scaffold. During this session, a clean Laravel skeleton was created only to host and run the existing LMS scaffolding; all further work focuses on fixing gaps and finishing features.

---

## Project structure
- Laravel app lives in: `noblenest-academy/`
- LMS scaffolding source lives in: `scaffolding/lms/` (copied into the Laravel app by the installer)
- Scripts:
  - `scripts/install_lms_scaffold.php` — copies scaffolding files and appends routes into the Laravel app
  - `scripts/fetch_repo.php` — fetches a GitHub repo into a directory (not used in production)
- Tests live in: `noblenest-academy/tests`
- i18n JSON: `scaffolding/lms/resources/lang/i18n.json` (used by `App\Helpers\I18n`)

## What Junie does
- Fixes errors, integrates pieces added by Copilot, and completes missing parts with minimal, targeted changes.
- Uses Form Requests for validation, RoleMiddleware for role checks, Blade + Bootstrap only.
- Keeps user‑facing strings behind `I18n::get()` and updates i18n keys as needed.

## How to run the app locally
1) cd noblenest-academy
2) composer install
3) cp .env.example .env && php artisan key:generate
4) Configure DB in .env
5) php artisan migrate
6) php artisan serve (http://127.0.0.1:8000)

## How to run tests
- From repo root: `php artisan test` (root-level shim delegates into noblenest-academy)
- Or:
  - cd noblenest-academy
  - php artisan test

Junie should run tests after each significant change, and ensure the application boots (no route or config errors) before submitting.

## Build/caches for release
- php artisan config:cache
- php artisan route:cache
- php artisan view:cache
- composer install --no-dev --optimize-autoloader
- php artisan migrate --force

## Coding style
- PHP 8.2+/Laravel 11+ conventions (PSR‑12)
- Blade + Bootstrap 5 components; keep JS minimal and framework‑agnostic (optional Alpine for micro‑interactions)

## Branch and commit guidelines
- Branches: `feature/<area>-<short-desc>` or `fix/<area>-<short-desc>`
- Conventional commits, e.g., `fix(routes): remove duplicate php open tag in web.php`

## Definition of Done (MVP)
- Auth & roles working, RoleMiddleware enforced
- Admin Course CRUD finished with validation and pagination
- Parent/Child flows operational
- AI assistant mock endpoint working from the home modal
- i18n keys present for all user‑facing strings
- Tests pass via `php artisan test`

## Clarification on “rebuild vs. fix”
- Primary mode: Fix and complete what Copilot generated.
- Rebuild only when: the base cannot boot or test without a minimal Laravel skeleton (done once this session). After that, we avoid wholesale rewrites and keep changes minimal.
