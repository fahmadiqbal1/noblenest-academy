# Noble Nest Academy — Master Launch Prompt (for Sonnet)

Paste the contents of this file as a single prompt into a fresh Sonnet session at the project root. It is self-contained, prescriptive, and contains every gap that must be closed before launch.

---

## Role and Mission

You are the senior full-stack engineer + product/curriculum lead taking Noble Nest Academy from a half-finished scaffold to a 100 % production-ready, deployable, paying-customer-ready platform. You will plan, refactor, build, seed, test, and ship.

The product is described in `Global online learning academy.pdf` at the workspace root. Re-read it before you start; it defines the curriculum vision (1,200+ activities ages 0–6 + STEM 7–10, 8 languages, AI-generated multimodal content, multicultural pedagogy, parental involvement, accreditation-ready).

**Working directory:** `noblenest-academy/` (the inner folder — the Laravel app lives here).
**Branch:** create `feature/launch-ready-v1` off `feature/lms-scaffold`. Open one PR per phase (8 phases below). Do NOT commit unless explicitly approved per phase.

**Non-negotiable acceptance criteria for "launch ready":**
1. `composer install && npm install && npm run build && php artisan migrate:fresh --seed && php artisan optimize` finishes with zero errors on a clean Ubuntu 24.04 box.
2. Every public page renders with consistent design tokens (no Bootstrap-class fallbacks rendering as raw HTML).
3. Every activity in the seeded library opens to a real, age-appropriate interactive player — never the "You're Ready to Begin!" empty placeholder.
4. Stripe Checkout + Customer Portal + webhook flow works end-to-end with subscriptions (monthly + annual), idempotent, signature-verified, taxed, and traceable.
5. `php artisan test` passes ≥ 95 % (it currently sits around 68 %).
6. Lighthouse mobile ≥ 90 on home, pricing, /activities, /child/{id}/activities, /onboarding/step/1. `pa11y-ci` passes.
7. Production deploy doc walks through KVM4 → live in under 30 min.
8. No dead routes, no dead controllers, no Bootstrap class usage anywhere unless Bootstrap is actually loaded for that surface (it is not — so remove it everywhere).

---

## What is actually broken right now (audit — do not skip)

I performed a deep audit. Before you change anything, verify each finding below by reading the named file. If a finding is wrong, write it up in `docs/AUDIT_DELTA.md`; if right, fix it as part of the relevant phase.

### A. The activity player shows nothing (root cause)

`resources/views/activities/show.blade.php:237-279` decides the CTA from `$activity->activity_type`. It only handles: `tracing`, `drawing`, `puzzle`, `quiz`, `video`, `slides`, `simulation`. Anything else falls into a generic "You're Ready to Begin!" card.

Seeded activity types in `database/seeders/*Seeder.php` (per `grep -h "activity_type" database/seeders/*.php | sort | uniq -c`):

```
175 hands_on
 89 game
 49 craft
 39 real_world
 26 routine
 24 outdoor
 19 vocal
 18 experiment
 17 sensory
 15 observation
 15 interactive
 10 reading
  9 discussion
  7 drawing       ← only matched type with > 2
  7 mindfulness
  3 play
  3 matching
  2 worksheet
  2 quiz
  2 story
  2 creative
  2 movement
  1 sorting
  1 flashcard
  1 creative_play
```

→ ~99 % of seeded activities have no working player. **This is the #1 complaint and the #1 fix.**

### B. Mixed/broken CSS systems

- 76 blade files still use Bootstrap classes (`btn-primary`, `col-md-6`, `container py-4`, `card-body`, `d-flex`, `gap-2`, `row g-3`, `navbar-*`, etc.) — see `grep -rl "container py-\|col-md-\|btn-primary\|navbar-" resources/views`.
- 60+ blades use Bootstrap Icons (`bi bi-*`).
- **No layout actually loads Bootstrap CSS or Bootstrap Icons.** Confirmed: `grep -rn "bootstrap.min\|bootstrap@\|bootstrap-icons" resources/views/layouts` returns zero matches; Vite pipeline ships only Tailwind v4 + custom CSS.
- Three competing custom stylesheets are *all* loaded together on every page:
  - Vite-compiled `resources/css/app.css` (Tailwind v4 + design tokens, 643 lines).
  - `public/css/playful.css` (handwritten `nn-*` classes, 2,079 lines).
  - `public/css/tier-{baby,toddler,preschool,school}.css` (4 files, age-specific overrides).
- Activity blades use *different* design systems per file: `show.blade.php` uses `x-ui.*` Tailwind components; `puzzle.blade.php`, `tracing.blade.php`, `drawing.blade.php` use Bootstrap classes; `video.blade.php` and `slides.blade.php` use bespoke `nn-vp-*` / `nn-slides-*` styles. **Pick one system and migrate.**

### C. Build/asset gaps that will break production

- `public/build/` does not exist → `@vite()` directive throws in production. You must run `npm run build` before deploy.
- `resources/fonts/` contains only a README. All `<link rel="preload" as="font" href="/fonts/...">` will 404. Either ship the WOFF2 files or remove the preloads + `@font-face` declarations and use `font-display: swap` with system fallbacks.
- `vite.config.js` has `ViteImageOptimizer` commented out behind a TODO. Decide on/off and remove the TODO.

### D. Payment / Stripe is partially wired but unfinished

`app/Http/Controllers/PaymentController.php` + `routes/web.php:108-116` give us:
- ✅ Stripe Checkout one-time payment.
- ✅ Webhook signature verification (`STRIPE_WEBHOOK_SECRET` required, no fallback).
- ✅ Idempotency on `checkout.session.completed`.
- ✅ CSRF excluded for `/webhook/stripe` (`bootstrap/app.php`).
- ❌ Mode is `payment` (one-shot), **not** `subscription` — recurring billing is impossible. Customers pay once and the subscription quietly expires after 1 or 12 months with no renewal.
- ❌ No Stripe Customer Portal route → users can't cancel or update card.
- ❌ Pricing built ad hoc (`unit_amount` from a hidden form field). No Stripe Products/Prices. Tax, VAT, currency conversion, discounts, coupons all missing.
- ❌ `customer.subscription.deleted`, `invoice.payment_failed`, `invoice.payment_succeeded` are not handled → no churn tracking, no dunning, no proration.
- ❌ `paypalCheckout()` returns a generic "PayPal not available" flash but the checkout view (`resources/views/checkout.blade.php:46-54, 73-80`) still renders the PayPal button. Either remove or implement.
- ❌ No tax collection (Stripe Tax) — required in the EU/UK.

### E. Routes file is repetitive

`routes/web.php` has 9 separate `Route::middleware(['auth','role:Admin'])->prefix('admin')->name('admin.')->group(...)` blocks. Consolidate into a single block per role.

### F. Dead/unfinished features

- `app/Http/Controllers/PaymentController::paypalCheckout` is a dead end. Remove or finish.
- `resources/views/layouts/app.blade.php` is documented as a "compatibility shim" — Phase 3 was supposed to retire it. Either retire it or remove the shim comment.
- 12+ status/audit markdown files at the project root (`EXECUTIVE_SUMMARY.md`, `FINAL_AUDIT_SUMMARY.md`, `IMPLEMENTATION_COMPLETE.md`, `LAUNCH_READINESS_*.md`, `SPRINT_2_*.md`, etc.) — move to `docs/archive/`.
- `subscription.active` middleware wraps `/activities` and friends but most activities are `is_free => true`. Confirm middleware lets free content through; right now this likely blocks unauthenticated/free users from the catalog.

### G. Content & curriculum gaps vs the PDF spec

- Target is 1,200+ activities for 0–6 across motor, language, cognitive, social-emotional, creative, cultural. The seeders are close to that count but most lack `video_url`, `audio_url`, `thumbnail_url`, and steps.
- 8 required languages: en, fr, ru, zh, es, ko, ur, ar. Most seeded activities are English-only. RTL support exists in `layouts/app.blade.php` but is untested for Arabic/Urdu activity pages.
- STEM track for 7–10 (block coding, robotics, AI literacy) is barely scaffolded.
- IQ + personality assessment at age 9–10 is absent.
- Animation/avatar AI video pipeline (Synthesia / HeyGen / Pictory) is documented but not used.
- No accreditation documentation pack (Cognia / BAC alignment, COPPA / GDPR evidence).

### H. UI / UX

- The home page hero uses `x-ui.button` (Tailwind), the `/activities` page uses `nn-*` classes (playful.css), and the activity show page uses `x-ui.*` again. No consistent visual language.
- Activity cards in `activities/index.blade.php` rely on emoji-only thumbnails. There are no SVG/illustration assets in the design system.
- Step Player (`resources/views/components/step-player.blade.php`) is good but only renders when `$activity->steps` is non-empty. ActivityStepSeeder seeds steps per subject but coverage is uneven — verify every activity has ≥ 3 steps after seed.
- No genuine "easy steps" UX: small kids need clear visual demonstrations (animated SVG, photo sequences, narrated audio). Currently we have text overlays only.

---

## Deliverable: 8-Phase Plan

Open one PR per phase. Each PR must include: code, tests, screenshots (or recorded gif/video for animations), and a `docs/phaseN-launch/CHANGES.md` summary. Do not start phase N+1 until phase N is reviewed and merged.

### Phase 1 — Visual & asset baseline (FIRST — everything else depends on it)

Goal: one and only one design system, rendering correctly in production.

1. Decide and document: **Tailwind v4 + the Blade `x-ui.*` component library** is the one true system. Everything else gets deleted or migrated.
2. Delete `public/css/playful.css`, `public/css/tier-*.css`. Move any visual rules still needed into `resources/css/app.css` or component-scoped utilities. Keep the design tokens (`@theme { … }`) — they are good.
3. Remove every Bootstrap class from `resources/views/**/*.blade.php`. Replace with Tailwind utilities or `x-ui.*` components. **Do not** add Bootstrap. Files most affected (run the grep first to get the current list): `activities/{drawing,puzzle,tracing,index}.blade.php`, `checkout.blade.php`, `onboarding.blade.php`, `profile.blade.php`, `privacy.blade.php`, `terms.blade.php`, `practitioner/**`, `scholarship/**`, `admin/{teacher-vetting,practitioners,content-batch,school-inquiries,courses,users,children,modules,payouts,questions,scholarships}/**`, `teacher/**`, `student/**`, `classroom/room.blade.php`.
4. Replace every `bi bi-*` icon usage with the `x-ui.icon` component backed by Lucide (already a dependency). Add any missing icons to `resources/views/components/ui/icon.blade.php`.
5. Drop or self-host fonts:
   - **Option A (recommended):** ship the WOFF2 files into `public/fonts/` (Baloo 2 Regular/Bold, Nunito Regular/SemiBold/Bold, Inter Regular/Medium/SemiBold/Bold). Add a `scripts/fetch-fonts.sh` that downloads them from Google Fonts under the SIL Open Font License.
   - **Option B:** delete the `<link rel="preload">` lines and `@font-face` block; rely on Google Fonts CDN.
6. Retire `layouts/app.blade.php` shim: every blade `@extends('layouts.app')` should be migrated to its proper role layout (`layouts.parent`, `layouts.child`, `layouts.teacher`, `layouts.admin`, `layouts.marketing`, `layouts.auth`). Then delete `layouts/app.blade.php`.
7. Build a small visual style-guide page at `/_styleguide` (admin-only) showing every `x-ui.*` component and the design tokens — used as the visual regression baseline.

Acceptance: `npm run build` produces a built `public/build/`. Lighthouse mobile ≥ 90 on `/`, `/pricing`, `/activities`, `/login`, `/register`. `pa11y-ci` passes. No `bi-` or `btn-primary` or `col-md-` strings remain in `resources/views`.

### Phase 2 — Activity player overhaul (the user's biggest complaint)

Goal: every activity in the seed library opens to an age-appropriate, interactive, accessible player.

1. Treat `activity_type` as the **content shape**, not the player. Introduce a `activity_renderer` derived from `(activity_type, age_tier, has_video, has_steps)` via a single service `App\Services\ActivityRendererResolver`. Map all 24 seeded types to one of these canonical renderers:
   - `guided-steps` — animated step player (default; what `step-player.blade.php` already does, polished). Goes to `hands_on`, `craft`, `routine`, `real_world`, `mindfulness`, `discussion`, `outdoor`, `observation`, `interactive`, `creative`, `creative_play`, `worksheet`, `experiment`, `vocal`, `sensory`, `movement`, `play`, `reading`, `story`, `flashcard`.
   - `tracing-canvas` — finger/mouse tracing on alphabets, numbers, shapes (replace SignaturePad with a deterministic stroke engine; handle RTL stroke order for Arabic/Urdu).
   - `drawing-canvas` — free draw with brush sizes, colours, undo, save.
   - `drag-and-match` — replaces `puzzle.blade.php`. Drag-pair, drag-sort, drag-sequence. Used for `matching`, `sorting`, `puzzle`.
   - `quiz` — single-question + multi-question, MCQ + tap-the-picture, audio prompt. Used for `quiz`.
   - `song-and-movement` — looping video + lyric overlay + tap-along beat tracker. Used for music/movement activities.
   - `video-lesson` — clean player + transcript + step bookmarks (you already have this in `video.blade.php`, polish it).
   - `code-blocks` — block-based programming canvas for ages 7–10, using Blockly via CDN.
   - `assessment` — gated IQ + personality battery (age 9–10, COPPA-safe).
2. Build `resources/views/activities/players/{guided-steps,tracing,drawing,drag-and-match,quiz,song,video,code-blocks,assessment}.blade.php`. Move all per-player JS into `resources/js/players/*.js` and code-split them with Vite dynamic import.
3. Replace the CTA logic in `activities/show.blade.php` with `@include('activities.players.'.$renderer)`.
4. Add `Activity::renderer()` accessor (memoised) and `App\Services\ActivityRendererResolver::resolve(Activity)` returning the renderer slug. Cover with unit tests for every seeded type.
5. Ensure every activity has ≥ 3 `ActivityStep` rows (run `ActivityStepSeeder` after the activity seeders) and at least a `thumbnail_url`. Where audio/video is genuinely missing, the player must fall back gracefully (use the existing emoji-scene fallback, never a blank stage).
6. Replace the `nn-vp-*` CSS in `video.blade.php` with the Tailwind dark theme tokens added in Phase 1.
7. RTL: the tracing player must mirror the canvas and stroke order when `app()->getLocale() in ['ar','ur']`. Test with `أ ب ت ث` and Urdu `ا ب پ ت`.
8. Accessibility: keyboard navigation on every player, ARIA roles on dots/controls, captions for video, transcripts for audio, prefers-reduced-motion respect on all CSS animations.

Acceptance: open **any** activity in each tier (baby/toddler/preschool/school) → interactive content renders within 1s on a mid-tier phone, never the empty placeholder. Add a feature test `tests/Feature/ActivityRendererTest.php` that iterates every seeded activity and asserts a non-empty renderer was selected.

### Phase 3 — Activity content + curriculum push to 1,200+

Goal: meet the PDF promise of a real curriculum library.

1. **Audit current counts** per age tier + subject. Produce a coverage matrix in `docs/curriculum-coverage.md`.
2. Generate the missing activities via the existing AI orchestrator (`app/Http/Controllers/Admin/OrchestratorController.php` + Python sidecar `services/curriculum-ai/`). Where the sidecar is not configured, write a deterministic content seeder per gap from a structured CSV. Target ≥ 200 activities per age tier (1,200+ for 0–6) plus ≥ 200 for 7–10.
3. Each generated activity must have: `title`, `description`, `benefit_explanation`, `skills_improved` (array), `learning_objectives`, `materials_needed`, `instructions_for_parent`, `safety_warnings`, `age_min/age_max`, `duration_minutes`, ≥ 3 `ActivityStep` rows, and a `thumbnail_url` (placeholder SVG illustration if no real asset).
4. Translation pass: every activity has rows for the 8 supported languages. Use the AI sidecar batch translation endpoint or a fallback batch via OpenAI Batch API. Store translations in `activity_translations(activity_id, locale, field, value)` — add the migration.
5. **STEM 7–10 track:** Scratch/Blockly intro, Python intro via Brython sandbox, simple electronics simulator, AI literacy lessons (image recognition demo via TensorFlow.js).
6. **IQ + Personality assessment**: a 30-question adaptive battery + a parent-facing report. Use the WPPSI-IV / Big Five Mini for kids reference (do not claim clinical accuracy; market as "interest and strength indicator"). Output a PDF report.
7. **Cultural modules per PDF**: Japanese (empathy, group activities), Chinese (calligraphy, pinyin), Scandinavian (outdoor play, democratic circle time), Islamic (Quran, Arabic — gated by `child.is_muslim`).

Acceptance: `php artisan tinker → Activity::count()` ≥ 1,400. `Activity::whereHas('steps')->count()` equals `Activity::count()`. Every locale has ≥ 95 % translation coverage.

### Phase 4 — Stripe-only, real subscriptions, full lifecycle

Goal: a real billing system that a paying customer can use.

1. Switch Stripe Checkout from `mode: payment` to `mode: subscription`. Define products + prices in Stripe (monthly + annual + family + lifetime). Sync them via a `php artisan stripe:sync-prices` console command that reads `config/billing.php` and writes `App\Models\PricingTier`.
2. Replace the hidden-amount form with a `price_id` lookup based on (plan, region) resolved server-side. **Never trust client-supplied amounts.**
3. Implement the **Stripe Customer Portal** route (`/billing/portal`) so users can cancel/upgrade/update card. Configure the portal in `config/services.php`.
4. Handle these webhooks idempotently with row-level dedup in `stripe_webhook_events`:
   - `checkout.session.completed` → activate subscription.
   - `customer.subscription.updated` → reflect new period / plan.
   - `customer.subscription.deleted` → mark `subscriptions.active = false`, set `cancelled_at`.
   - `invoice.payment_succeeded` → extend `ends_at`.
   - `invoice.payment_failed` → flag account, email dunning notice (Mailable + queued).
   - `customer.updated` → keep email in sync.
5. Enable Stripe Tax (`automatic_tax: { enabled: true }`) and Stripe Radar (default rules).
6. Remove PayPal from UI + controller (delete `paypalCheckout`, the form blocks in `checkout.blade.php`, the `PAYPAL_*` env keys; `services.paypal` stays as `null` so other code that references it doesn't break — or remove it cleanly with `grep`).
7. **Regional pricing**: the existing `PricingService` picks a tier from country → use this to pick the corresponding Stripe Price ID (one per region per plan). Add `pricing_tiers.stripe_price_id_monthly`/`_yearly` columns.
8. **Free trial**: 7-day trial on the first paid plan (`subscription_data.trial_period_days = 7`).
9. **Receipts + invoices**: `payment_intent_data.receipt_email` and Stripe-hosted invoices, both linked from the parent dashboard "Billing" page.
10. **Coupons / scholarships**: existing `App\Models\Scholarship` flow should issue a 100 % Stripe coupon redeemable once. Wire it to the public `scholarship/apply` form.
11. Tests: webhook signature failure, double-delivery, refund, plan upgrade mid-cycle, dunning. Use `stripe-mock` in CI.

Acceptance: a tester completes `Sign up → onboard → choose plan → Stripe Checkout → activate → cancel via portal → reactivate` end-to-end in test mode, and every event is reflected in the DB within 5 s.

### Phase 5 — Auth, onboarding, dashboards, parent UX polish

1. Audit `routes/web.php` and collapse the 9 separate admin route groups into one. Same for the 2 `role:Teacher` blocks.
2. Onboarding: keep the 3-step flow but make step 1 (language) auto-detect from `Accept-Language` and skip itself if confident.
3. Parent dashboard: today's plan, weekly streak, child progress chart, "1-tap log" for offline activities, share-card generator.
4. Child experience: use age-tier mascot/theme already designed, but harden it after Phase 1 visual rebuild. Add a "play next" auto-suggest based on `LearningPathService`.
5. Notifications: real email + push (already scaffolded in `app/Notifications/*`). Activate Mailable + ResendDriver.
6. Privacy: COPPA + GDPR data-export + delete already exist (`PrivacyController`). Verify and add the "Parental Consent" gate for kids under 13.
7. Remove all 12 root-level launch-status markdowns into `docs/archive/`. Keep only `README.md`, `CHANGELOG.md`, `LICENSE`, `SECURITY.md`.

Acceptance: a parent can sign up, complete onboarding, see a real dashboard within 60 s. A 5-year-old can navigate to an activity and back without help (test with the keyboard-only path).

### Phase 6 — Tests, performance, observability

1. Push test coverage from 68 % → 95 %+. The fastest wins: fix the 28 CSRF-token-missing tests (set up the `withMiddleware` properly), seed the 18 fixture-missing tests, add Stripe webhook signature tests, add the new `ActivityRendererTest`.
2. Add `tests/Browser` Dusk specs for the critical paths: register → onboard → activity → complete → mark badge; checkout → webhook → activation; admin login → curriculum view.
3. Performance: enable opcache (already in Docker), Redis cache, query log review, add eager loading audit (use `barryvdh/laravel-debugbar` in `local`). Fix every N+1 surfaced.
4. Observability: ship Sentry (free tier OK), `LOG_CHANNEL=stack` with daily file + Sentry breadcrumbs. Add `/up` (already there) + a `/health/detailed` smoke for Stripe API connectivity.
5. SEO: structured data (`Organization`, `Course`, `EducationalOrganization`, `Offer`), sitemap.xml, robots.txt already there, hreflang tags per language.

Acceptance: `php artisan test` ≥ 95 % green; Lighthouse mobile ≥ 90 on listed pages; Sentry receiving a deliberately triggered error in `staging`.

### Phase 7 — Deployment to Hostinger KVM4 + CI/CD

1. `.docker/` already exists — finish it. Confirm `docker-compose.yml` boots cleanly with `docker compose up --build`.
2. Production deploy doc `docs/deploy/hostinger-kvm4.md`: Ubuntu 24.04 setup (Nginx, PHP-FPM 8.3, MySQL 8, Redis 7, supervisor for Horizon, certbot, fail2ban, ufw, daily backups via `mysqldump → rclone`).
3. CI: a new GitHub Action `.github/workflows/ci.yml` running PHP CS Fixer, PHPStan level 6, `php artisan test`, `npm run build`, `npm run a11y`, `npm run lighthouse`. CD: a separate workflow that SSH-deploys to KVM4 on green main.
4. Zero-downtime deploy script (`scripts/deploy.sh`): build assets locally → rsync release dir → atomic symlink → `php artisan migrate --force` → cache warmers → restart php-fpm + horizon.
5. Backups: a daily cron rotating 14 days of `mysqldump` + `storage/app` to off-server S3/B2.
6. Secrets: confirm `SecureCredentialsManager` is wired and `php artisan credentials:rotate` works.

Acceptance: spin a fresh KVM4, follow the doc → live at the domain in ≤ 30 min, HTTPS green, Stripe webhook arrives, a real parent account can subscribe and complete an activity.

### Phase 8 — Final polish, accreditation pack, soft launch

1. Accreditation pack (`docs/accreditation/`): syllabus per age tier, learning outcomes, assessment rubrics, evidence of cultural sensitivity reviews, COPPA + GDPR + UK Age-Appropriate-Design-Code compliance memos.
2. Marketing pages: `/`, `/pricing`, `/for-schools`, `/about`, `/contact`, `/blog` (Markdown-rendered).
3. Cookie consent banner (analytics opt-in, performance opt-in, marketing opt-in) — required for EU.
4. Set up Posthog or Plausible for analytics behind cookie consent.
5. Add Statuspage/healthchecks.io heartbeat to the daily Horizon job.
6. Run a 24-hour internal soak: synthetic parent + child usage every 5 min, watch for memory leaks and Horizon backlog.
7. Write the soft-launch playbook: who watches what, on-call schedule, rollback procedure.

Acceptance: a 5-person friends-and-family beta runs for 7 days with no P1 issues.

---

## Hard rules while you work

- **Read before you write.** Always read every file you intend to modify in full first. The codebase has multiple compatibility shims and "Phase X" comments that look like dead code but aren't — verify with `grep` and the GitNexus / code-review-graph MCP tools.
- **Use the project's MCP tools.** This project is indexed by GitNexus and code-review-graph. Per `CLAUDE.md`, you MUST run `gitnexus_impact` before modifying any function/class, and `gitnexus_detect_changes` before each commit.
- **One PR per phase.** Atomic, reviewable, revertable.
- **Tests with every change.** Every controller method, service method, and renderer needs a feature or unit test. New blade views get a smoke test that the route returns 200 with the expected `x-ui` components present.
- **Migrations are forward-only** for the bits already shipped (35+ migrations are applied). For new schema, write reversible migrations and a `down()`.
- **No emojis in code or comments** unless the file is a kid-facing template that genuinely needs them. The user has not asked for an emoji-heavy chat tone from you.
- **No new dependencies without justification.** Tailwind v4, Alpine, Stripe PHP, Lucide, Blockly are already in. Anything else: open an ADR in `docs/adr/`.
- **No mocks for the database in feature tests.** Use real migrations against SQLite-in-memory or MySQL test container.
- **No silent fallbacks for payment.** If Stripe is misconfigured, log + 5xx — never grant free access.
- **Security:** every form CSRF-protected, every admin route gated, every uploaded image validated (PNG magic-byte check already exists in `ActivityController::decodeVerifiedPng` — extend the pattern to other uploads).
- **i18n:** every new user-facing string goes through `I18n::get()` (or Laravel's `__()` if you migrate to native trans files — make the call in Phase 5).

---

## First action when you start

1. Read the PDF (`Global online learning academy.pdf` at workspace root). Internalise the vision.
2. Read this entire prompt.
3. Read `CLAUDE.md`, `AGENTS.md`, and `README.md` in the project root.
4. Run `gitnexus_query({query: "activity player"})`, `gitnexus_query({query: "stripe checkout"})`, `gitnexus_query({query: "onboarding"})` and produce a one-page `docs/launch/audit.md` confirming or correcting my findings.
5. Open the branch `feature/launch-ready-v1` and start Phase 1.
6. Ask me only if a decision blocks all forward motion. Default to the recommendations above and document the choice in the phase's `CHANGES.md`.

Ship it.
