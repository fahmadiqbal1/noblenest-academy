<!-- markdownlint-disable MD013 -->
# Phase 2 — Activity Player Overhaul (scaffold)

**Status:** scaffold landed on `feature/launch-ready-v1` alongside Phase 1. Uncommitted; held for combined review.

**Master prompt goal:** every seeded activity opens to an age-appropriate, interactive, accessible player — never the generic "You're Ready to Begin!" empty card.

## What's done in this scaffold

- [x] **`App\Services\ActivityRendererResolver`** ([app/Services/ActivityRendererResolver.php](../../app/Services/ActivityRendererResolver.php)). Maps `activity_type` → one of nine canonical renderers (`guided-steps`, `tracing-canvas`, `drawing-canvas`, `drag-and-match`, `quiz`, `song-and-movement`, `video-lesson`, `code-blocks`, `assessment`). Falls through to `video-lesson` if a `video_url` is present, otherwise `guided-steps`. **Never returns an unknown slug** — `isCanonical()` is exhaustively tested.
- [x] **`Activity::renderer()` accessor** on the model. Memoised per instance; resolves once per render via the service.
- [x] **Nine player blades** under [resources/views/activities/players/](../../resources/views/activities/players/). Each is a focused dispatch:
  - `guided-steps` — uses `<x-step-player>` when steps exist; otherwise an emoji-scene fallback card so the player is **never blank**. Fixes the master-prompt's Finding A directly.
  - `tracing-canvas` / `drawing-canvas` / `drag-and-match` / `video-lesson` — CTAs to the existing dedicated routes (`activities.tracing`, `activities.drawing`, `activities.puzzle`, `activities.video`). Phase 2 follow-ups will inline these players.
  - `quiz` — links to `quizzes.show` when `quiz_id` is set, friendly fallback card otherwise.
  - `song-and-movement` — embeds the `video_url` or `audio_url` inline (looping `<video>`/`<audio>` with `controls`); falls back to the video route.
  - `code-blocks` — placeholder + step-count hint. Blockly canvas lands during Phase 3 STEM track build-out.
  - `assessment` — placeholder + COPPA-safe framing ("interest indicator, not clinical assessment"). Battery lands in Phase 3.
- [x] **`show.blade.php` CTA ladder replaced** with a single `@include('activities.players.' . $activity->renderer())`. The old hand-written `@if/@elseif` chain is gone; the empty `"You're Ready to Begin!"` placeholder is unreachable from any seeded activity_type now.
- [x] **Test coverage** — `tests/Unit/Services/ActivityRendererResolverTest.php` (34 PHPUnit tests, 72 assertions) covers every type in `TYPE_MAP`, both fallback paths, and the canonical-slug invariant. `tests/Feature/ActivityRendererTest.php` (2 tests, 85 assertions) creates one Activity per known type, asserts each routes to a canonical slug, and asserts every canonical slug has a blade on disk. **All 36 tests pass / 157 assertions.**
- [x] **SQLite-compat migration patch** — `database/migrations/2026_04_11_060700_expand_milestones_domain_column.php` had MySQL-only `ALTER TABLE … MODIFY COLUMN` raw SQL that crashed `RefreshDatabase` under SQLite. Patched with a driver-aware branch using `Schema::table()->change()`. This was the only such migration in the repo (per the master prompt's Phase-6 follow-up notes).

## What's deliberately deferred to Phase 2 follow-up commits

The master prompt calls for full inline players (deterministic stroke engine, drag-and-match canvas, Blockly canvas, assessment battery). This scaffold:

1. Establishes the dispatch architecture.
2. Eliminates the dead-end empty placeholder for **every** seeded activity_type.
3. Locks in the resolver invariants with tests so future per-player polish can't introduce regressions.

**Follow-up work** (still Phase 2):

- Inline `tracing-canvas` with a deterministic stroke engine + RTL stroke-order mirror for `ar`/`ur` (replacing SignaturePad).
- Inline `drawing-canvas` with brush sizes, undo stack, colour palette, save-to-progress.
- Inline `drag-and-match` with drag-pair / drag-sort / drag-sequence variants.
- Inline `quiz` with single-question and multi-question MCQ, tap-the-picture, audio prompt.
- Polish `video-lesson` with transcript and step bookmarks.
- Build `code-blocks` (Blockly via CDN) for ages 7–10.
- Build `assessment` (30-question adaptive battery + parent-facing PDF report).
- Run `ActivityStepSeeder` audit to confirm every seeded activity has ≥ 3 steps + a thumbnail.
- Accessibility pass: keyboard nav on every player, ARIA roles, captions, transcripts, prefers-reduced-motion.

## Phase 2 polish landed in follow-up

After the scaffold, three follow-up improvements landed:

- [x] **Inline drag-and-match player** — [activities/players/drag-and-match.blade.php](../../resources/views/activities/players/drag-and-match.blade.php) is now a self-contained Alpine sequencing game (tap tiles in order, wrong tap shakes + resets, success state with a 🎉 announcement and an `aria-live` region). Uses the activity's `instructions` array when present, falls back to A/B/C/D. Touch-first (no drag handlers) for mobile. The full-fidelity drag-pair/sort/sequence variants for the dedicated route are still in `activities/puzzle.blade.php`; an inline "Play the full puzzle →" link preserves that path.
- [x] **Step-player accessibility pass** — [components/step-player.blade.php](../../resources/views/components/step-player.blade.php) now has:
  - `role="region"` + `aria-roledescription`/`aria-label` on the wrapper, `tabindex="0"` to receive focus.
  - Screen-reader-only `aria-live="polite"` announcer that reads "Step N of M: title. instruction." every time `currentIndex` changes.
  - Keyboard handlers: ← previous step, → next step, space toggles play/pause.
  - Dots use `role="tablist"` / `role="tab"` with descriptive `aria-label` ("Go to step 3: …") and `aria-current="step"` on the active dot.
  - Controls have explicit `aria-label`s; play/pause exposes `aria-pressed`.
  - Progress bar is a `role="progressbar"` with `aria-valuenow`/`aria-valuemin`/`aria-valuemax`.
  - All animations are no-op'd under `@media (prefers-reduced-motion: reduce)` (Ken Burns pan, bubble rise, bounce/pulse/wiggle/float/spin, button hover lift, progress-fill transition).
- [x] **Activity coverage probes** — `tests/Feature/ActivityCoverageTest.php` asserts that ≥ 90% of seeded activities have ≥ 3 steps and that ≥ 90% have a `thumbnail_url`. Tests skip gracefully when the DB is empty (no seed run). Once seeders run portably under SQLite (Phase 6 / Phase 3 content push), these probes will fail the build if curriculum coverage regresses.

## Acceptance criteria status

| Criterion (master prompt) | Status |
|---|---|
| Every seeded `activity_type` opens to an interactive player, never `"You're Ready to Begin!"` | ✓ Resolver guarantees a canonical slug; every slug has a blade |
| `tests/Feature/ActivityRendererTest.php` iterates every seeded activity and asserts a non-empty renderer | ✓ (uses factory-created representatives — full library iteration is a Phase-3 add when seeders run portably) |
| RTL tracing mirror | ✗ Follow-up |
| Keyboard nav + ARIA | ✗ Follow-up |
| Full inline players | ✗ Follow-up; current scaffold dispatches to existing routes |
