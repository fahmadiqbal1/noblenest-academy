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
