# Launch-Ready v1 — Audit Verification

**Verified:** 2026-05-15 against `feature/lms-scaffold` @ `152ed76` · No code changed yet.

Each finding in `docs/LAUNCH_MASTER_PROMPT.md` was checked against the working tree. Outcomes below.

## Findings: confirmed, corrected, or amended

| # | Master-prompt finding | Status | Evidence / delta |
|---|---|---|---|
| A | Activity show falls through to empty placeholder for ~99 % of seeded types | **Partly confirmed** | `resources/views/activities/show.blade.php:240-279` matches only `tracing`, `drawing`, `puzzle`, `quiz`, `video`/`video_url`, `slides`, `simulation`. **However**, line 264-267 adds a steps-based fallback ("Interactive Lesson View") for any activity where `$activity->steps->count() > 0` — so the truly-empty card only fires when an activity has no matching type, no video, no quiz, AND no steps. Real blast radius depends on `ActivityStepSeeder` coverage, which has 427 lines but uneven per-subject distribution. We will measure post-seed in Phase 2 before claiming "99 %". |
| B | 76 blades use Bootstrap classes, 60+ use `bi bi-*`, no Bootstrap is loaded | **Confirmed, worse than stated** | `grep` on `container py-\|col-md-\|btn-primary\|navbar-[a-z]\|card-body\|d-flex\|row g-[0-9]` returns **87** blade files (not 76). `bi bi-*`: 58 files. `grep -rn "bootstrap.min\|bootstrap@\|bootstrap-icons" resources/views/layouts` returns zero — Bootstrap is not loaded anywhere. |
| B | Three competing CSS systems all loaded together | **Confirmed** | `public/css/` contains `playful.css`, `tier-baby.css`, `tier-toddler.css`, `tier-preschool.css`, `tier-school.css` alongside the Vite-compiled `app.css`. |
| C | `public/build/` missing, fonts missing | **Confirmed** | `public/build/` does not exist. `resources/fonts/` contains only `README.md`. (`public/fonts/` not listed in original audit but also empty.) |
| D | Stripe is `mode: payment`, only `checkout.session.completed` handled | **Confirmed** | `PaymentController.php:77` sets `'mode' => 'payment'`. `stripeWebhook` (`:104-269`) has a single `if ($event->type === 'checkout.session.completed')` branch — no subscription/invoice/customer events. |
| D | No Customer Portal, no Products/Prices, PayPal button dangles | **Confirmed** — not re-verified by file read; will check in Phase 4. | |
| E | 9 separate `role:Admin` route groups | **Confirmed** | `grep -c "role:Admin" routes/web.php` = 9. |
| F | `layouts/app.blade.php` is a documented "compatibility shim" | **Confirmed** | Header comment self-describes as "COMPATIBILITY SHIM (Phase 2)" with an explicit Phase-3-retire note. |
| F | 12+ root-level launch-status markdowns | **Confirmed** | Inner-app root contains **13** `.md` files. 12 are launch-status / sprint / audit reports (the 13th is `README.md`, which stays). |
| F | `docs/` already partially populated | **New observation** | `docs/` already contains `LAUNCH_MASTER_PROMPT.md`, `LAUNCH_READINESS.md`, `LAUNCH_TODO.md`, `ACCESSIBILITY.md`. The latter three duplicate root-level files — confirm canonical version in Phase 5 cleanup. |
| G | Curriculum count, multi-locale gap, IQ assessment missing | **Confirmed by absence** | `Activity::count()` not measured yet (DB not seeded in this session). `app/Services/ActivityRendererResolver.php` does not exist. |
| H | Step Player only renders when `steps` non-empty | **Confirmed** | `Activity::steps()` at `app/Models/Activity.php:142` is a `hasMany` ordered by `step_number`. Renderer logic in `show.blade.php:264` short-circuits when count > 0. |

## New deltas the master prompt did not flag

1. **README.md is out of sync with the launch direction.** Lines 13, 43, 348 still market Bootstrap 5.x as the UI; line 312 lists only 6 languages (EN/FR/RU/ZH/ES/KO), not the 8 the master prompt requires (adds UR + AR). Must update during Phase 1 (visual baseline) or Phase 5 (docs/README polish).
2. **AGENTS.md ≈ CLAUDE.md.** The outer-repo `AGENTS.md` file is essentially a copy of the GitNexus instruction block. There is no separate agent-style operator guide. Either consolidate or document the duplication.
3. **GitNexus and code-review-graph indexes are both empty.** `mcp__code-review-graph__list_graph_stats_tool` returns `Files: 0, Total nodes: 0`. Neither `.gitnexus/` nor `.code-review-graph/` directories exist. CLAUDE.md and `docs/LAUNCH_MASTER_PROMPT.md` both make impact analysis a **MUST**, but the underlying indexes do not yet exist for this repo. **Decision required before Phase 1 touches code** — see "Open decisions" below.
4. **`activity_type` totals are slightly different from the master prompt's grep output.** Re-running with a tighter regex confirms the *shape* (most types are unmapped) but the per-type counts shift by ±1–5 because the original grep also captured age/subject literals on the same lines. Treat the histogram in the master prompt as directional, not exact. Final numbers will land in `docs/curriculum-coverage.md` during Phase 3.
5. **`activities/show.blade.php` already does some emoji in user-facing copy** ("Start Tracing ✏️", "🎨", "🧩", "🧠", "🎬", "📖", "🎯", "🌟"). These are kid-facing and intentional — they are *not* the "no emojis in code" rule violations the master prompt warns about.

## Open decisions (blocking, in order)

1. **Graph index before Phase 1?** CLAUDE.md says `gitnexus_impact` is mandatory before any edit, but no index exists. Options:
   - (a) Run `npx gitnexus analyze` + `mcp__code-review-graph__build_or_update_graph_tool` once, up-front. Adds ~5–15 min of build time but unblocks every later phase.
   - (b) Amend the rule to "use the graph when populated, fall back to grep" and proceed. Faster start, but every later impact check needs verbalisation.
   - **Recommended:** (a). It pays for itself by Phase 2.
2. **Font policy** (Phase 1 step 5). Master prompt offers Option A (ship WOFF2) or B (drop preload + use Google Fonts CDN). Need confirmation before I script `fetch-fonts.sh` or rip the preloads.
3. **Existing `docs/LAUNCH_READINESS.md` + `docs/LAUNCH_TODO.md`** overlap with root-level launch-status files marked for archival. Phase 5 will sort this — flagging now so they don't get duplicated into `docs/archive/`.

## What I will do once you approve this audit

1. Build the code graph (if Decision 1 = (a)).
2. Create branch `feature/launch-ready-v1` off `feature/lms-scaffold`.
3. Open Phase 1 PR scoped to: delete `playful.css` + `tier-*.css`, swap every Bootstrap class to Tailwind/`x-ui.*`, swap every `bi-*` to `x-ui.icon`, font decision per (2), retire `layouts/app.blade.php` shim, add `/_styleguide` admin page. Tests + screenshots + `docs/phase1-launch/CHANGES.md`.
4. Wait for your review before Phase 2.

## What I will NOT do without further direction

- Run `php artisan migrate:fresh --seed` against your local DB.
- Modify any of the 12 root-level launch-status markdowns (Phase 5).
- Touch Stripe configuration or any `.env` (Phase 4).
- Commit anything until Phase 1 PR is explicitly approved.
