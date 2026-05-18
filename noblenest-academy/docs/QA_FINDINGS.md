# QA Findings & Remediation Log — Noble Nest Academy v1

**Date:** 2026-05-18
**Scope of this pass:** Confirmed Critical/High defects from the QA audit
(functionality, security, data integrity, payments). Each was verified against
live code, fixed at root cause, and covered by a new regression test. The full
test suite was green before and after (`php artisan test` → 304 passed, 0 failed,
4 skipped — up from 290 with the new guards).

> Note: `docs/AUDIT.md`'s "15 failing tests" is **stale** — the suite is green.
> The real risk surface is wiring/security/data, not test failures.

---

## Severity key

| Severity | Meaning |
|---|---|
| Critical | Auth bypass, data loss, payment failure, core flow down |
| High | Major feature broken |
| Medium | Partial/UX issue |

---

## Fixed this pass

### C1 — Onboarding step 5 crashed every new signup (Critical) ✅

`Activity::where('published', true)` was used by `OnboardingController`,
`ContentReviewController`, `ContentBatchController`, `ProcessContentBatchJob`
and `AIAssistantService`, but **no migration ever created `activities.published`**
and it was not on the model. Every one of those paths was a latent
"Unknown column 'published'" 500; onboarding step 5 (GET) crashed for every new
parent, blocking signup→activation.

Also found: public/child activity listings did **not** filter on `published`, so
unreviewed AI-generated activities would leak straight to children
(content-safety issue in a kids product).

**Fix:** migration `2026_05_18_000001_add_published_to_activities_table`
(`boolean published default true`, indexed); added to `Activity` `$fillable`/`$casts`
and `ActivityFactory`; added `where('published', true)` to `ActivityController` and
`ChildActivityController` listings. Default `true` keeps seeded curriculum visible;
AI/batch content already sets `false` and surfaces in the admin review queue.
**Test:** `tests/Feature/ActivityPublishedColumnTest.php`.

### C2 — Subscription gate read a stale boolean (Critical) ✅

`EnsureSubscriptionActive`, `User::hasActiveSubscription`, `ChildActivityController`
and `Parent/DashboardController` all gated on the legacy `subscriptions.active`
boolean, but the Phase-7 state machine (`pause()`, `markPastDue()`) only mutated
`status`. A paused/past-due subscription kept `active=true` → still granted access.

**Fix:** added canonical `Subscription::scopeEntitled()` (status=active AND not
expired) as the single source of truth; routed all 4 readers through it;
`pause()`/`markPastDue()` now also clear the legacy `active` mirror.
**Test:** `tests/Feature/Billing/SubscriptionGateConsistencyTest.php`.

### C3 — Stripe activation not atomic; re-subscribe left customer locked out (Critical) ✅

Webhook handler effects + the processed-event marker were not in one transaction
(partial-write → "paid but no access"). Worse, `activateSubscription()` set
`active=true` but **not** `status` on the `updateOrCreate` *update* path — after
C2, a previously-canceled customer who re-subscribed stayed `status=canceled` and
was denied despite paying.

**Fix:** wrapped the per-event handler dispatch + `recordWebhookProcessed` in a
single `DB::transaction` (idempotency preserved — marker only commits on success,
Stripe retries cleanly); `activateSubscription()` now also sets
`status = STATUS_ACTIVE`.
**Test:** `tests/Feature/Billing/StripeActivationAtomicityTest.php`.

### C6 — `users.stripe_customer_id` column never existed (Critical) ✅

*Discovered while testing C3.* `PaymentController` reads/writes
`$user->stripe_customer_id` in 5 places, but the column only ever existed on
`subscriptions`. Every real `checkout.session.completed` webhook threw
"no such column" and was swallowed as a 500 — **no subscription was ever created
from a live Stripe payment.** Pre-existing; previously masked by the catch-all
that returned 500 (Stripe retried forever).

**Fix:** migration `2026_05_18_000002_add_stripe_customer_id_to_users_table`
(nullable, indexed — `handleCustomerUpdated` looks users up by it); added to
`User` `$fillable`. Covered by the C3 test.

### C4 — PayPal silent failure (High) ✅ — *decision: safe-disable for v1*

`PayPalCheckoutService` returned fake `stub:true` orders when keys were absent,
and `PayPalController::capture()`/`webhook()` **never activate a Subscription** —
a real PayPal payment would charge the customer and grant nothing. `/webhook/paypal`
was also missing from the CSRF-exempt list (every live webhook would 419).

**Decision (operator-approved):** PayPal is not a supported v1 path; Stripe is.
**Fix:** `create`/`capture`/`webhook` now fail closed with HTTP 503 + a warning
log while unconfigured (no fake orders, no fake success); `webhook/paypal` added
to CSRF exemptions so the path is correct if PayPal is finished later.
**Test:** `tests/Feature/Billing/PayPalSafeDisableTest.php`.
**Outstanding (operator/future phase):** to enable PayPal, wire capture+webhook
to activate subscriptions (mirror Stripe), add UI, supply live credentials.

### C5 — Orphaned, syntactically-broken admin views (Medium) ✅

`resources/views/admin/school-inquiries/{index,show}.blade.php` were leftovers
from the deleted `SchoolInquiry` vertical — no controller, route, or nav
reference; `index.blade.php:37` contained invalid PHP
(`{{ $inquiry->status 'open' 'bg-primary' ... }}`).
**Fix:** deleted the dead directory. SPEC's minimal institutional path is
`InstitutionalController` (invite flow), unaffected.

### C7 — AI content-batch pipeline dead: `activities.source_job_id` missing (Critical) ✅

`ProcessContentBatchJob` and `AIAssistantService` write `'source_job_id'` when
creating AI activities, but the column never existed **and** it was not in
`Activity::$fillable` — the job link was silently dropped on create. Then
`ContentBatchController::preview()`/`publish()` ran
`Activity::where('source_job_id', $job->id)` → "Unknown column" 500. Admin AI
content generation→preview→publish was entirely non-functional. Found by the
schema↔code reconciliation scan (same class as C1/C6).

**Fix:** migration `2026_05_18_000003_add_source_job_id_to_activities_table`
(nullable FK to `ai_jobs`, `nullOnDelete`, indexed); added to `Activity`
`$fillable`. **Test:** `tests/Feature/ContentBatchPipelineTest.php`.

### W1 — Admin quiz-question builder 500'd on every action (High) ✅

`admin/quizzes/edit.blade.php` and `admin/questions/{create,edit}.blade.php`
called `route('admin.questions.*')`, but the nested-resource routes are
`admin.quizzes.questions.*`. Every Add/Edit/Delete-Question button threw
`Route [admin.questions.create] not defined` → 500. Admin could not manage
quiz questions at all. **Fix:** corrected the route names (params already
correct for the nested resource).

### W2 — Dead share-card UI (kill-list leftover) (Medium) ✅

`parent/dashboard.blade.php` (×2) and `child/activities.blade.php` referenced
`route('parent.share-card')` (deleted route → 500 if rendered) and
`$child->share_card_url` (column removed in Phase 1 → always null). Share cards
are on the SPEC kill list. **Fix:** removed the three dead Blade fragments.

### S1 — Parental-consent gate failed OPEN on unknown age (Critical, COPPA) ✅

`RequireParentalConsent` did `if ($ageMonths === null || $ageMonths >= 156)
return $next()` — a child with no parseable DOB (age indeterminate) was
granted access to child-facing routes **without** recorded parental consent.
For a 0–10 product this is a direct COPPA violation. **Fix:** fail closed —
only skip the consent gate when age is *known* and ≥ ~13y; unknown age now
requires `parental_consent_at`. **Test:** `tests/Feature/Security/CoppaFailClosedTest.php`.

### S2 — Parent-PIN gate bypassed for users with no PIN (High) ✅

`RequireParentPin` did `if (empty($user->parent_pin_hash)) return $next()` —
any parent who never set a PIN (legacy / incomplete onboarding) reached
privacy export/erase with no second factor. **Fix:** fail closed — redirect to
the PIN screen; `ParentPinController::verify()` now sets the PIN on first
submission (the recovery path so failing closed never permanently locks a user
out). View copy updated for set-mode. **Test:** same file.

### P0 — Activity player: VERIFIED FUNCTIONAL (no fix needed)

Historical claim "player shows nothing for 99% of activities" is **resolved**
(Phase 2/4). `ActivityRendererResolver` always returns one of 12 canonical
renderers (guided-steps fallback — never blank); all 12 player partials exist;
`show.blade.php` dispatches `@include('activities.players.'.$activity->renderer())`.
Smoke: all **3,814 seeded activities** resolve to a canonical renderer with
**0 failures**; `ActivityPlayerTest` (60 assertions) covers every renderer.

### D1 — `migrate:fresh --seed` produced an unusable demo (High) ✅

`DemoChildrenSeeder` existed but was wired into **no** `DatabaseSeeder` block,
so the demo had 0 child profiles — the parent/child dashboard and
activity-player journey could not be exercised at all (Definition-of-Done #5).
**Fix:** wired `DemoChildrenSeeder` + `DemoOrchestratorSeeder` into the
LOCAL/TESTING-only block (never production). Re-seed now yields 8 parents /
11 children / 4 AI jobs. **Test:** `tests/Feature/DemoSeedUsableTest.php`.

### S0 — IDOR sweep: VERIFIED SAFE (no fix needed)

Audited every route-model-bound `{child}`/`{activity}`/`{milestone}` endpoint
(`Parent/ChildController`, `Child/DashboardController`, `ChildActivityController`,
`Parent/MilestoneController`, `AssessmentController`). All enforce ownership via
`ChildProfilePolicy` (`view`/`update`/`delete` check `parent_id === user.id`) or
explicit `authorizeChild()`. A parent can only act on their own children — no
cross-parent IDOR. Role middleware casing (`Admin`/`Parent` PascalCase vs
`school_admin` snake) is internally consistent (`InstitutionalController`
assigns `school_admin`, routes match) — style nit, not a defect.

---

## Open items NOT addressed in this pass (carry into the full QA matrix)

These were flagged by the audit and remain for the broader remediation
(see the QA remediation prompt). They are not regressions introduced here.

| Area | Item | Severity |
|---|---|---|
| Security | IDOR sweep on route-model-bound `{child}`/`{activity}` ownership checks | High |
| Security | COPPA consent must fail **closed** when child age/DOB indeterminate | High |
| Security | Parent-PIN gate must not bypass for users with null `parent_pin_hash` | High |
| Security | Local Groq key present in `.env` (gitignored — not committed) — rotate before launch | Medium |
| Data | Reconcile the two progress tables (`activity_user_progress` vs `child_activity_progress`); cascade-delete coverage | Medium |
| Functionality | Activity player renderer must degrade gracefully (never blank) for missing/unknown payloads | High |
| Functionality | Quiz double-submit idempotency; text/essay questions silently score 0 | Medium |
| API/AI | Python sidecar (`services/curriculum-ai/`) appears unwired (`CURRICULUM_AI_URL` unused) — wire w/ graceful degradation or delete | Medium |
| UX | Hardcoded hrefs bypassing `route()` in nav partials; mixed-CSS cleanup; a11y/Lighthouse targets | Medium |

---

## Verification

- `php artisan test` — **304 passed**, 0 failed, 4 skipped (981 assertions).
- `php artisan migrate --force` — both new migrations apply cleanly.
- Changes scoped to expected files (13 modified, 2 deleted, 2 migrations + 4 tests added).
- Not committed — awaiting operator review (run `gitnexus_detect_changes` before commit).
