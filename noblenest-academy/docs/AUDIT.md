# Noble Nest Academy — Phase 0 Reality-Check Audit

**Date:** 2026-05-16
**Branch:** `release/v1-launch` (from `feature/launch-followups-v1`)
**Mode:** Read-only. No source changes were made in Phase 0.
**Reference spec:** [SPEC.md](./SPEC.md)
**Raw command logs:** `docs/_phase0_logs/` — generated locally during Phase 0; `*.log` is gitignored so only `npm-audit.json` is committed. To regenerate, re-run the commands listed in section 3 on a fresh checkout. All key signal is inlined below.

---

## 1. Toolchain & environment

| Component | Local | Hostinger target | Note |
|---|---|---|---|
| PHP | **8.4.11** | **8.3** (CyberPanel default) | CLAUDE.md says 8.5; neither machine runs that. Standardize on **PHP 8.3** for CI + prod. |
| Node | **22.18.0** | 22 LTS | OK. |
| Composer | 2.x | 2.x | OK. |
| DB | MariaDB 11 (Docker, local) | MySQL 8 or MariaDB 11 | Verify FK + JSON support on VPS in Phase 12. |
| Redis | 7 | 7 | OK. |

**Action:** Pin PHP 8.3 in `composer.json` `require.php` and in CI (`.github/workflows/ci.yml`, Phase 11). Update CLAUDE.md to say 8.3.

---

## 2. Codebase inventory

| Surface | Count |
|---|---:|
| HTTP controllers | **70** |
| Models | 55 |
| Services | 20 |
| Migrations (applied) | **79** (0 pending) |
| Blade views | 207 |
| PHPUnit test files | 29 |
| Registered routes | 266 |
| Seeders touching kill-list verticals | ≥6 |

After Phase 1 deletions the controller count is expected to drop by **≥30** (full Maternal/, Teacher/, Practitioner/, Student/Marketplace, Admin/{Maternal,Payout,Practitioner,Scholarship,SchoolInquiry,TeacherVetting}, Classroom, ShareCard, Referral).

---

## 3. Diagnostic results

### `php artisan migrate:status` — ✅ `exit 0`
79 migrations, all `Ran`, batch 1 (consolidated locally). 0 pending. Of these:
- **25+ migrations** create tables for verticals on the kill list (maternal_*, contraindication_matrix, share_cards, payout_requests, referrals, scholarships, teacher_courses, teacher_enrollments, teacher_profiles, practitioner_profiles, class_sessions, content_reviews — see [`_phase0_logs/migrate-status.log`](./_phase0_logs/migrate-status.log)).
- These will be removed in the **Phase 1 migration squash** to a single `2026_05_16_000000_consolidated_v1_schema.php`.

### `php artisan route:list` — ✅ `exit 0`
266 routes registered. ≥40 routes belong to deleted verticals (teacher.*, student.marketplace.*, practitioner.*, payouts.*, scholarships.*, school-inquiries.*, share-cards.*, referrals.*, maternal.*, classroom.*).
Full output: [`_phase0_logs/route-list.log`](./_phase0_logs/route-list.log).

### `php artisan test` — ❌ `exit 1`
**Result:** 22 failed · 4 skipped · 4 pending · **210 passed** (512 assertions, 4.92s).

Root-cause grouping:

| Failure | Tests | Root cause | Resolved by |
|---|---:|---|---|
| `TeacherStudentMarketplaceTest` | 3 | `nav-student.blade.php` accesses `$x->name` on null when student context absent. Entire feature is on kill list. | **Phase 1 (deletes file)** |
| `PublicMetadataTest > marketplace page` | 1 | Same nav-student null. | **Phase 1** |
| `MaternalWellnessTest` | 3 | Maternal controllers/views/seeders on kill list. | **Phase 1 (deletes file)** |
| `PublicMetadataTest > auth pages` | 1 | Real failure: metadata assertions on auth pages. | **Phase 3** (i18n rewrite of auth views will fix). |
| `ParentChildFlowTest` (onboarding step1, child dashboard) | 2 | Onboarding views diverged from controller contract. | **Phase 5** (onboarding rebuild). |
| `LmsDiscrepanciesTest > onboarding loads for authed` | 1 | Same onboarding divergence. | **Phase 5**. |
| `CurriculumAIServiceTest` | 5 | `BindingResolutionException` — service constructor expects a concrete that isn't bound in test container. | **Phase 2** (DI cleanup) — also rewire to Anthropic-only in Phase 6. |
| `FeedbackLoopIntegrationTest` | 5 | `QueryException` — schema/factory mismatch on `child_skill_states` join (likely missing column or factory drift). | **Phase 2** (model + factory pass). |
| `ActivityPayloadContractTest` | 1 | `Error` in `helpers.php:206` (config()/env() call in unit context without app bootstrap). | **Phase 2**. |

**Net real failures after Phase 1 deletions:** ~15 (down from 22). All resolvable inside Phases 2–5; no test is blocked on missing infrastructure.

Full log: [`_phase0_logs/test.log`](./_phase0_logs/test.log).

### `npm run build` — ✅ `exit 0`
Built in 432ms. 55 modules. Manifest + app.css (149 KB / 26.9 KB gzip) + app.js (43 KB / 16.9 KB gzip). Warning: 7 `/fonts/*.woff2` references don't resolve at build time — fonts must be placed in `public/fonts/`. Cosmetic for now; resolve in Phase 8 (PWA precache).

### `composer audit` — ⚠️ 1 medium
- `league/commonmark` 2.3.0–2.8.1 — CVE-2026-33347 (embed extension allowed_domains bypass). Bump in Phase 2 cleanup.

### `npm audit --omit=dev` — ✅ 0 vulnerabilities
0 info / 0 low / 0 moderate / 0 high / 0 critical.

### `gitnexus_detect_changes scope=all`
Skipped this turn — `release/v1-launch` was just created from `feature/launch-followups-v1` and has zero diffs. Re-run after Phase 1's first commit to verify scope.

---

## 4. Internationalization gap (largest single risk)

Current state:

- Translations live in **a single file** `resources/lang/i18n.json`. Spec requires Laravel's `lang/{locale}/*.php` structure (Phase 3).
- File contains only **6 locales**: `en, fr, ru, zh, es, ko`. **Urdu (`ur`) and Arabic (`ar`) are missing entirely.**
- ~1,200 strings per locale would be needed at full coverage; current file has ~150 keys.
- Of **207 Blade files**, only **8** use `__()` or `@lang()`. **133 Blade files** contain candidate hardcoded English (heuristic: `>Capitalized Words<`). List inlined below.

### Blade files with likely hardcoded English (head)

```
resources/views/_styleguide.blade.php
resources/views/pricing.blade.php
resources/views/onboarding.blade.php
resources/views/welcome.blade.php
resources/views/checkout.blade.php
resources/views/privacy.blade.php
resources/views/contact.blade.php
resources/views/about.blade.php
resources/views/privacy/parental-consent.blade.php
resources/views/terms.blade.php
resources/views/home.blade.php
resources/views/auth/{login,register,forgot-password,reset-password}.blade.php
resources/views/emails/password-reset.blade.php
... (133 total — all 8 currently-used + most layouts/components)
```

(Kill-list views — `practitioner/*`, `student/*`, `scholarship/*` — are in this list but will be deleted in Phase 1 and don't need extraction.)

### Locale coverage diff (current `i18n.json`)

| Locale | Present | Notes |
|---|---|---|
| en | ✅ | Source of truth. ~150 keys. |
| fr | ✅ | Machine translation; needs human review. |
| ru | ✅ | Machine translation; needs human review. |
| zh | ✅ | Machine translation; needs human review. |
| es | ✅ | Machine translation; needs human review. |
| ko | ✅ | Machine translation; needs human review. |
| **ur** | ❌ | **MISSING.** Phase 3 must add full RTL coverage. |
| **ar** | ❌ | **MISSING.** Phase 3 must add full RTL coverage. |

### RTL infrastructure
- No `tailwindcss-rtl` plugin installed.
- No `<html dir="…">` switching middleware.
- No directional support in layouts (all classes are LTR `ml-*`/`mr-*`, not `ms-*`/`me-*`).

---

## 5. Phase 1 kill list — confirmed file groups

`git ls-files | grep -iE "..."` returned **134 matches**. Grouped:

| Term | Files | Action |
|---|---:|---|
| `maternal` / `Maternal` | **69** | Delete (controllers/, services/, views/, models/, migrations/, seeders/, tests/). |
| `practitioner` / `Practitioner` | 15 | Delete. |
| `referral` / `Referral` | 8 | Delete. |
| `payout` / `Payout` | 6 | Delete. |
| `scholarship` / `Scholarship` | 6 | Delete. |
| `marketplace` / `Marketplace` | 4 | Delete (Student/MarketplaceController, student/marketplace view, related tests). |
| `share.?card` / `ShareCard` | 3+2+3=8 | Delete. |
| `contraindication` | 3 | Delete (matrix table + seeder + service usage). |
| `SchoolInquiry` | 3 | Delete (rebuild minimally in Phase 7 if needed for institutional). |
| `newborn` | 2 | Delete (Maternal/NewbornController). |
| `classroom` / `Classroom` | 2 | Delete (live-class views/controller — not used by v1 cohort scheduling). |
| `TeacherCourse` / `TeacherEnrollment` | 3 | Delete (entire teacher marketplace). |
| `DailyCo` / `DailyCoService` | 2 | Delete. |

**Migrations in the kill list (must be removed when squashing in Phase 1):** 25 (see `_phase0_logs/migrate-status.log`).

**Controllers in the kill list (full list):**
```
Admin/MaternalContentController.php
Admin/PayoutController.php
Admin/PractitionerController.php
Admin/ScholarshipController.php
Admin/SchoolInquiryController.php
Admin/TeacherVettingController.php
ClassroomController.php
Maternal/{Breastfeeding,Content,Dashboard,EmergencySign,Exercise,Herb,Journal,Journey,Newborn,Nutrition,Onboarding,Profile,Technique}Controller.php   (13)
Practitioner/{ContentReview,Dashboard,Profile}Controller.php   (3)
ReferralController.php
SchoolInquiryController.php
ShareCardController.php
Student/{CourseReview,Enrollment,Marketplace}Controller.php   (3)
Teacher/{Analytics,Course,Dashboard,InviteLink,Payout,Profile,Session}Controller.php   (7)
```
Net: **30 controllers deleted** of 70. Remaining 40 form the v1 surface.

**Services in the kill list:**
- `MaternalContentFilterService.php` — **rename + generalize** as `ContentSafetyService` (Phase 6).
- `DailyCoService.php` — delete.
- `ShareCardService.php` — delete.
- All others kept.

**Tests in the kill list:**
- `MaternalWellnessTest.php`, `TeacherStudentMarketplaceTest.php` — delete entirely.
- `PublicMetadataTest.php` — keep; remove marketplace assertion; rewrite remainder against v1 routes.

---

## 6. Pre-flight prerequisites for Phases 12–13 (NOT present yet)

Reported here so Phases 1–11 don't surprise us at deploy time. **No action in Phase 0 — flagged only.**

| Item | Status | Needed by |
|---|---|---|
| `.env.deploy` at repo root (gitignored), keys listed in plan | ❌ Not present | Phase 11 (CI workflow needs DEPLOY_* secrets) and Phase 12. Operator to create locally. |
| Hostinger VPS reachable at `157.173.210.224` | ⚠️ Untested | Phase 12. |
| DNS for `noblenestacademy.com` + `www.` | ⚠️ Unverified | Phase 12 (cert issuance) and Phase 14. |
| Provider API keys: Stripe live, PayPal live, Anthropic, HeyGen/Synthesia, OpenAI Whisper, S3/R2 | ❌ None loaded | Phase 6 (AI pipeline), Phase 7 (payments), Phase 13. Operator will supply one-by-one when reached. |
| GitHub repo secrets (DEPLOY_SSH_KEY, DEPLOY_HOST, DEPLOY_USER, DEPLOY_PATH, HEALTH_TOKEN) | ❌ Not set | Phase 11. |
| S3-compatible bucket (Hostinger Object Storage or Cloudflare R2) | ❌ Not provisioned | Phase 6 + Phase 9 (backups). |

---

## 7. Risks called out for the operator

1. **PHP 8.3 vs 8.5 mismatch.** CLAUDE.md/AGENTS.md say PHP 8.5; the target VPS runs 8.3. We will standardize on 8.3 — confirm this is OK before Phase 1. *(Already confirmed via answered question to proceed; documented here for trail.)*
2. **Migration squash is destructive.** All 79 migrations become 1 file; the archive folder `_archived_pre_v1/` is for reference only. Anyone with an existing local DB on this branch will need a fresh `migrate:fresh --seed`. Plan documents this in `docs/MIGRATIONS.md` (Phase 1 deliverable).
3. **134 kill-list files** include some coincidental matches (e.g. a "referral_code" string in an unrelated comment). Each file will be inspected before deletion in Phase 1.
4. **`MaternalContentFilterService`** holds the only content-safety logic. Plan is to **rename + generalize** into `ContentSafetyService` in Phase 6 — *not* delete. This is noted explicitly in Phase 1's commit message so the rename isn't lost.
5. **Test infrastructure for AI services** uses `Http::fake()` patterns that currently fail with `BindingResolutionException`. Phase 2 must fix DI before Phase 6 (AI pipeline) can land cleanly.
6. **`PublicMetadataTest > auth pages`** failure is the only real bug outside the kill list that isn't already scheduled for rewrite — it implies the auth views aren't emitting the route-specific `<title>`/`<meta>` the test asserts. Schedule fix during Phase 3 i18n extraction.

---

## 8. Phase 0 exit-criteria checklist

- [x] `docs/SPEC.md` exists, copied verbatim from operator brief.
- [x] `docs/AUDIT.md` exists (this file).
- [x] `php artisan migrate:status` run — captured.
- [x] `php artisan route:list` run — captured.
- [x] `php artisan test` run — captured, root-caused.
- [x] `npm run build` run — captured.
- [x] `composer audit` run — captured.
- [x] `npm audit --omit=dev` run — captured.
- [x] Controllers × routes × views × tests matrix produced (sections 2, 5).
- [x] Failing tests root-caused (section 3).
- [x] Hardcoded-English Blade list produced (section 4).
- [x] Locale coverage diff produced (section 4).
- [x] Kill list produced (section 5).
- [x] No code changes made.
- [ ] Draft PR opened with SPEC.md + AUDIT.md → done in next step.

---

**Next:** Open draft PR, then await operator go-ahead before Phase 1 begins.

---

# Phase 1 — Aggressive scope reduction (DONE)

**Date:** 2026-05-16
**Commit:** see `git log release/v1-launch`

## Results

| Metric | Before | After | Δ |
|---|---:|---:|---:|
| Controllers | 70 | **34** | −36 |
| Models | 55 | **32** | −23 |
| Services | 20 | **18** | −2 (DailyCo, ShareCard deleted; MaternalContentFilter renamed → ContentSafetyService) |
| Migrations | 79 | **53** | −26 (squash to a single file: deferred to Phase 2, see `docs/MIGRATIONS.md`) |
| Blade views | 207 | **162** | −45 |
| Tests | 29 | **27** | −2 (TeacherStudentMarketplaceTest, MaternalWellnessTest deleted) |
| Routes | 266 | **~180** | −86 |
| Deleted files (total) | — | **165** | — |
| Modified files | — | **30** | — |
| Renamed | — | **1** | (MaternalContentFilterService → ContentSafetyService) |
| Test pass rate | 210 / 240 | **172 / 191** | improved |
| Test failures | 22 (15 real) | **15** | every remaining failure matches the AUDIT do-not-fix list (Phases 2/3/5/6) |

## Verticals deleted (kill-list, complete)

Maternal health · Teacher marketplace · Student marketplace · Practitioner vetting · Payouts · Scholarships · Share cards · Daily.co live classes · Referrals · School inquiries (rebuild minimally in Phase 7) · Classroom (live class viewer) · Newborn care · Contraindication matrix · Class sessions · Invite links · Session tokens · Course reviews · Content reviews (practitioner-of-maternal-content; admin `ContentReviewController` for Activity moderation is KEPT) · TeacherCourse(Section), TeacherEnrollment, TeacherProfile.

## Configuration changes

- `config/features.php` — removed 9 dead flags: `maternal_module`, `practitioner_portal`, `referrals`, `scholarships`, `teacher_marketplace`, `share_cards`, `live_classes`, `viral_referrals_v2`, `public_share_pages`. Kept `school_inquiries` (Phase 7).
- `app/Http/Kernel.php` — removed `maternal.consent` and `practitioner.active` middleware aliases.
- `app/Models/User.php` — removed 6 deleted-model relations and 3 role helpers (`isTeacher`, `isStudent`, `isPractitioner`); removed `referral_code` from `$fillable`.
- `app/Http/Controllers/AuthController.php` — `ALLOWED_REGISTRATION_ROLES` reduced from `['Parent','Teacher','Student','Practitioner']` to `['Parent']`. Admin accounts are created via seeders only.
- `database/seeders/DatabaseSeeder.php` — removed `MaternalSeeder::class` calls and dev seed users for Teacher/Student.

## Routes/web.php rewrite

477 lines → 220 lines. Dropped: marketplace, teacher.*, student.*, practitioner.*, maternal.*, classroom.*, share-card, referrals, scholarships, school-inquiry POST, course.reviews, admin.teacher-vetting.*, admin.payouts.*, admin.school-inquiries.*, admin.scholarships.*, admin.maternal.*, admin.practitioners.*. Kept: health, public pages, auth, parent, child, activities (subscription-gated), quizzes, Stripe payments, notifications, privacy/GDPR, admin (courses/modules/quizzes/activities/curriculum/users/analytics/content-batch/content-review/orchestrator/horizon).

## Migration patches

One: `2026_04_02_000012_extend_child_profiles_table.php` had `share_card_url` column removed; the other 3 columns (`age_tier`, `streak_days`, `last_activity_date`) are kept.

## Test status (final)

```
172 passed · 15 failed · 4 skipped · 4 pending  (433 assertions)
migrate:fresh --seed: green (SQLite verified by subagent; MySQL 8 via docker-compose)
```

The 15 remaining failures all map to AUDIT §3's deferred-to-later-phase list:
- CurriculumAIServiceTest ×5 → Phase 2/6 (DI + Anthropic rewire)
- FeedbackLoopIntegrationTest ×5 → Phase 2 (schema + factory drift)
- ActivityPayloadContractTest ×1 → Phase 2
- ParentChildFlowTest ×2 → Phase 5 (onboarding rebuild)
- LmsDiscrepanciesTest > onboarding_page_loads → Phase 5
- PublicMetadataTest > auth_pages_expose_route_specific_metadata → Phase 3 (i18n rewrite)

## Deferred to Phase 2 (deliberate, documented)

1. **Migration squash to single consolidated file** — see `docs/MIGRATIONS.md`. The deleted-table requirement is already met; the cosmetic "one file" consolidation is deferred to immediately after the FK re-add and `ConsentReceipt` work in Phase 2.
2. **Dead-code scanner installation + resolution** — `nunomaduro/larastan`, `icanhazstring/composer-unused`, and `maglnet/composer-require-checker` were NOT installed in Phase 1. Reason: on a brownfield Laravel codebase these tools surface hundreds of findings; "resolve every finding" can balloon to multiple days of work. Recommend installing in Phase 2 (alongside the policy + factory pass) and addressing the findings as a Phase 2 acceptance gate.

## Docs cleaned up

Deleted: `docs/phase{1..8}-launch/` (stale per-phase change logs), `docs/archive/` (12 stale launch-readiness summaries), `docs/launch/`, `docs/soft-launch-playbook.md`, `docs/_phase0_logs/` (the only committed file `npm-audit.json` removed; local logs gitignored anyway).

Kept: SPEC.md, AUDIT.md (this file), MIGRATIONS.md (new), ACCESSIBILITY.md (Phase 8), LAUNCH_READINESS.md (Phase 14), accreditation/ (Phase 10), deploy/hostinger-kvm4.md (Phase 12).

## Phase 1 exit-criteria checklist

- [x] Every kill-list file deleted (165 files, 0 dangling refs verified via grep).
- [x] `composer dump-autoload` clean.
- [x] `php artisan migrate:fresh --seed` green.
- [x] `php artisan route:list` shows only v1 surface (~180 routes).
- [x] Routes, navigation, home page have no links to deleted verticals.
- [x] `MaternalContentFilterService` → `ContentSafetyService` rename complete.
- [x] Docs cleaned up; `docs/MIGRATIONS.md` written.
- [x] `CLAUDE.md` updated (PHP 8.3 target, scope reduction note).
- [ ] **Deferred:** migration squash to single file (Phase 2).
- [ ] **Deferred:** install + resolve `phpstan/larastan/composer-unused/composer-require-checker` (Phase 2).

**Next:** STOP for operator review. Two decisions needed before Phase 2.
