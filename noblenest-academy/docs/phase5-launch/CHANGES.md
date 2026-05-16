<!-- markdownlint-disable MD013 -->
# Phase 5 — Auth, Onboarding, Dashboards, Parent UX Polish

**Status:** scaffold landed on `feature/launch-ready-v1` alongside Phases 1 + 2 + 3 + 4. Uncommitted.

**Master prompt goal (§5):** collapse repetitive admin route groups, smarten the onboarding, reach parental-consent compliance for under-13, switch on dunning, archive obsolete root markdowns, harden the parent + child UX.

## Done in this scaffold

- [x] **Admin route groups: 9 → 7** ([routes/web.php](../../routes/web.php)). The three adjacent `role:Admin` groups (Courses + Curriculum + Analytics) are now one block. The remaining 5 admin groups stay where they are because they're interleaved with non-admin feature blocks (Onboarding, AI Orchestrator, Maternal, etc.) — moving them would shuffle the URI register table and risk silent route-order regressions. The 3 `role:Teacher` groups are deferred for the same reason (mechanical follow-up).
- [x] **Accept-Language auto-detect** on `OnboardingController::show()` ([OnboardingController.php](../../app/Http/Controllers/OnboardingController.php)). Parses `Accept-Language` with q-weights, picks the highest-ranked locale we support (`en/fr/ru/zh/es/ko/ur/ar`), writes it to `users.preferred_language` + session, and **redirects straight to step 2**. Only triggers when the user has no preference yet, so manual changes via `/lang/{locale}` still win.
- [x] **12 launch-status markdowns moved to `docs/archive/`** — `DEAD_CODE_VERIFICATION_REPORT`, `EXECUTIVE_SUMMARY`, `FINAL_AUDIT_SUMMARY`, `IMPLEMENTATION_COMPLETE`, `ITEMS_1_2_3_4_COMPLETION_SUMMARY`, `LAUNCH_READINESS_FINAL`, `LAUNCH_READINESS_SUMMARY`, `REFACTORING_GUIDE`, `SESSION_COMPLETION_SUMMARY`, `SPRINT_2_DEEP_DOMAIN_ANALYSIS`, `TEST_COVERAGE_REPORT`, `UPGRADE_PLAN_2026`. The inner-app root now has **only `README.md`** at top level.
- [x] **Under-13 Parental Consent gate** (COPPA + GDPR-K):
  - [Migration](../../database/migrations/2026_05_16_143455_add_parental_consent_to_child_profiles.php): adds `parental_consent_at`, `parental_consent_ip`, `parental_consent_user_agent` to `child_profiles`.
  - [RequireParentalConsent middleware](../../app/Http/Middleware/RequireParentalConsent.php): registered as `parental.consent`. When a route binds `{child}` to a `ChildProfile` under 156 months (≈13 yr) AND `parental_consent_at` is null, redirect to the consent page.
  - [PrivacyController::showParentalConsent + recordParentalConsent](../../app/Http/Controllers/PrivacyController.php): GET shows the consent form, POST records timestamp + parent IP + UA for audit (COPPA record-keeping).
  - [Consent view](../../resources/views/privacy/parental-consent.blade.php): clear "what we collect / what we do not / your rights" + three required checkboxes.
  - Routes: `GET / POST /privacy/parental-consent/{child}`.
- [x] **`InvoicePaymentFailed` dunning notification** ([InvoicePaymentFailed](../../app/Notifications/InvoicePaymentFailed.php)). `Notification implements ShouldQueue` so the webhook handler stays sub-500ms and Stripe doesn't retry. Dispatches via `mail` + `database` channels. Hooked into [PaymentController::handleInvoiceFailed](../../app/Http/Controllers/PaymentController.php). Tone: helpful, not alarming. CTA: open the billing portal.

## Validation

- Blade compiles ✓
- Routes compile ✓ (`php artisan route:cache` clean)
- 39 tests pass / 160 assertions ✓
- Vite builds: 365 ms, 134 KB CSS / 24.67 KB gzipped ✓
- New PHP files all lint clean ✓

## Phase 5 carve-outs (follow-ups for the next commit on this branch)

| # | Work | Why deferred |
|---|---|---|
| 1 | Merge the remaining 5 admin route groups into one block | Feature-interleaved with non-admin code; a clean move requires reordering the entire file. |
| 2 | Merge the 3 teacher route groups into one block | Same as above. |
| 3 | Parent dashboard polish: today's plan, weekly streak chart, "1-tap log", share-card generator | Dashboard surface itself exists; the master prompt asks for new widgets. Each is its own deliverable. |
| 4 | Child experience polish: "play next" auto-suggest via `LearningPathService` | Service exists; UI binding is the missing piece. |
| 5 | Resend / ResendDriver activation for transactional email | Mail config + provider key need a production secret. |
| 6 | Apply the `parental.consent` middleware to child-facing routes site-wide | Middleware + route are wired; *applying* it across the activities / quizzes / assessment routes is a focused next pass with route-test coverage. |
