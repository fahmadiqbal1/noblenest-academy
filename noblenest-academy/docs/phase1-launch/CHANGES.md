<!-- markdownlint-disable MD013 -->
# Phase 1 — Visual & Asset Baseline

**Status:** in progress on `feature/launch-ready-v1`. No commits yet — per the master prompt, this branch is held until Phase 1 is reviewed and approved.

**Goal (from master prompt):** one and only one design system (Tailwind v4 + `x-ui.*`), rendering correctly in production. Delete `playful.css` + `tier-*.css`, remove all Bootstrap classes, replace `bi-*` icons with Lucide via `x-ui.icon`, self-host fonts, retire `layouts/app.blade.php`, ship a `/_styleguide`.

## What's done

- [x] **Fonts self-hosted** — `scripts/fetch-fonts.sh` downloads the 9 WOFF2 files (Baloo 2, Nunito, Inter) from `rsms.me` (Inter) and Bunny Fonts (Baloo 2 + Nunito, SIL OFL, GDPR-friendly). Result: `public/fonts/` populated (~542 KB total). Wired into `package.json` as `npm run fonts` and into `launch:checks`. `.gitignore` excludes `public/fonts/*.woff2` so the files are fetched at install/build time rather than committed.
- [x] **`feature/launch-ready-v1` branch created** off `feature/lms-scaffold` @ `152ed76`.
- [x] **Audit + audit-delta verified** in `docs/launch/audit.md`. Bootstrap class usage is *worse* than reported (87 blades, not 76); icon count was tighter than reported (128 unique `bi-*`, vs "60+ files"). All Stripe, font, and Vite findings confirmed. New finding the audit missed: `resources/views/classroom/room.blade.php` is a standalone view that DOES load Bootstrap CSS + JS via CDN — fixed in this phase.
- [x] **Code-review-graph index built** for impact analysis: 556 files / 2016 nodes / 12243 edges / 23 communities / 116 flows. (GitNexus index is *not* populated — its MCP tools are not loaded in this session, so the CRG index is the single source of truth for impact checks.)
- [x] **Test baseline (corrected from prompt)** — actual test pass rate is **4 / 197** (~2 %), not the 68 % the master prompt claimed. Nearly all failures are `SQLSTATE[HY000] [1049] Unknown database 'noblenest_test'` — the MySQL test DB isn't provisioned. Phase 6 will switch to SQLite-in-memory per the prompt's "no DB mocks" rule.
- [x] **Icon migration: 75 → 127 Lucide icons in the registry**. Added 52 entries via `scripts/build-icon-registry.mjs` (canonical Lucide v0.511.0 paths). Documented `bi-*` → Lucide mapping in `docs/phase1-launch/icon-migration.md`. Ran `scripts/migrate-bi-icons.mjs` against 58 blade files: **270 mechanical swaps**. Then 7 hand-edits for dynamic `bi bi-{{ … }}` / Alpine `:class` / vanilla-JS-className cases. The classroom mic/cam controls now render two `<x-ui.icon>` elements per button, toggled by `.ctrl-btn.active`/`.inactive` parent class via app.css.
- [x] **Bootstrap class purge: 2 783 substitutions across 102 files**.
  - Tier 1 (mechanical 1:1 token swaps via `scripts/migrate-bootstrap-classes.mjs`): 1 508 changes — `d-flex → flex`, `fw-semibold → font-semibold`, `text-muted → text-[var(--color-text-muted)]`, `col-md-6 → md:w-6/12`, `rounded-pill → rounded-full`, `bg-primary-subtle → bg-violet-50`, full Bootstrap layout/typography/colour vocabulary.
  - Tier 2 (component-class token chains, same script extended): 1 264 changes — `btn btn-primary` → full Tailwind chain `inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition … bg-violet-600 text-white hover:bg-violet-700`. Same for `card`, `alert-*`, `badge`, `form-control/select/label/check`, `modal-*`, `table-*`, `breadcrumb`, `pagination`, `list-group`, `nav-tabs`, `dropdown-menu`, `progress`, `spinner-border`, `accordion-*`, `carousel-*`. 11 dynamic Blade ternaries (`{{ cond ? 'btn-primary' : 'btn-outline-secondary' }}`, `bg-{{ $color }} text-{{ $color }}`) hand-rewritten to emit Tailwind chains.
  - **Script bug discovered + fixed mid-flight.** First version of `migrate-bootstrap-classes.mjs` tokenised on raw whitespace, which split *inside* Blade `{{ }}` expressions and then de-duplicated their inner tokens, corrupting 5 files. Caught via Blade-class grep, hand-fixed those files, then patched the script's tokeniser to treat `{{ … }}` as opaque tokens and skip dedupe on them. Re-runs are now safe.
  - Final grep audit: 0 unambiguous Bootstrap utility tokens remain in `resources/views/`. The 5 lingering substring matches (`orch-card-header`, `nn-card-title`) are custom-class false positives that have no Bootstrap relationship.
- [x] **`classroom/room.blade.php` rebuilt for Tailwind**. Stripped the 3 Bootstrap CDN tags (CSS, Icons, JS bundle). Added `@vite(['resources/css/app.css', 'resources/js/app.js'])` so the standalone page picks up our design tokens + fonts. Converted the Chat/People tab UI from Bootstrap-JS `data-bs-toggle="tab"` to an Alpine.js `x-data="{ tab: 'chat' }"` component. Added CDN-load of Alpine.js (matches the layout pattern). The page is now fully self-contained on the Tailwind stack.
- [x] **`composer install`, `npm install`, `npm run build` all clean.** Build artefacts: `public/build/assets/app-*.css` 114 KB (20.81 KB gzipped) + `app-*.js` 37.78 KB (15.06 KB gzipped), 53 modules transformed in ≤ 440 ms.

## Additional Phase 1 work landed

- [x] **`/_styleguide` admin-only route + view** ([resources/views/_styleguide.blade.php](../../resources/views/_styleguide.blade.php), [routes/web.php](../../routes/web.php)). Renders every `<x-ui.*>` component variant (buttons, cards, badges, alerts, form controls, the full 127-icon Lucide registry, stats/progress/skeleton/tabs/empty-state) plus the design tokens (brand palette swatches, the three font samples). Used as the visual regression baseline.
- [x] **`vite-plugin-image-optimizer` enabled** in `vite.config.js`. PNG/JPG/JPEG quality 85, WebP quality 85 lossy, SVG multipass with default preset. Build still completes cleanly (430 ms).
- [x] **README + AGENTS.md reconciled.** Outer README badges/feature list/curriculum no longer claim Bootstrap 5.x — now Tailwind v4 + `x-ui.*`. Language count corrected from 6 → 8 (adds Urdu + Arabic with RTL note). AGENTS.md now has a header comment pointing to CLAUDE.md as canonical (it was a verbatim copy of the gitnexus block).
- [x] **Test env switched to SQLite-in-memory.** `phpunit.xml` and `.env.testing` updated. Used `force="true"` on the `<env>` overrides because Laravel's .env loader was winning otherwise and resolving to the unprovisioned `mysql/noblenest_test`.

## Known follow-ups landing on this branch before final PR

1. ~~**`playful.css` prune.**~~ ✓ Done — `scripts/prune-playful-css.mjs` walks the blades to build the used-`nn-*` set, parses `resources/css/playful.css` with PostCSS, and drops rules whose selectors reference only unused classes. **145 rules / 148 selectors removed (44.1% smaller)**. Final Vite bundle: 132.99 KB / 24.47 KB-gzip CSS (was 152.14 KB / 27.85 KB-gzip).
2. ~~**Stale `data-bs-*` attribute sweep.**~~ ✓ Done — 29 `data-bs-toggle`/`data-bs-target`/`data-bs-dismiss`/`data-bs-parent` attributes removed via sed across all blades. One leftover `new bootstrap.Modal(...)` call in `admin/orchestrator/index.blade.php` guarded behind `window.bootstrap && window.bootstrap.Modal` with a Phase-5 TODO for Alpine migration. The other `bootstrap.Modal` call in `partials/assistant.blade.php` was already guarded.
3. **MySQL-only migrations break SQLite-in-memory tests.** Test env config is correct, but `database/migrations/2026_04_11_060700_expand_milestones_domain_column.php` (and likely a handful of others) use `DB::statement("ALTER TABLE … MODIFY COLUMN …")` raw SQL. Test suite still reports 189 fail / 4 pass — same numbers as before, but the failure mode shifted from "DB doesn't exist" to "migration syntax error". Fix via `DB::getDriverName()` branching or `Schema::change()` — natural Phase 6 territory.

## Layout shim retirement — DONE

After deferring this earlier in the session, came back to it and finished cleanly:

- [x] **Extracted shared partials.** `resources/views/layouts/partials/head.blade.php` (meta tags + Open Graph + Twitter + favicons + manifest + theme-color + 5 font preloads + `@vite` + Alpine CDN + `@stack('head')`/`@stack('styles')`). `resources/views/layouts/partials/scripts.blade.php` (toast + AI bubble + assistant + PWA installer + flash messages + `@yield('scripts')`/`@stack('scripts')`). The AI bubble + assistant block is `auth()->check()`-gated.
- [x] **Rewrote all 10 role layouts** to use the partials. Each is now ~25 lines (down from 35–107 lines of inline chrome). They diverge only on body background, nav component, footer presence, and the `pb-24 lg:pb-6` mobile-nav inset on `layouts.parent`. `layouts.child` retains its age-tier-driven sticky nav (inline because the nav is dynamically themed by `$child->age_tier`).
- [x] **Migrated 84 views** from `@extends('layouts.app')` to their proper role layout. Per-directory mapping:
  - `admin/*` (33) → `layouts.admin`; `maternal/*` (17) → `layouts.maternal`; `teacher/*` (8) → `layouts.teacher`; `practitioner/*` (5) → `layouts.practitioner`; `parent/*` (2) → `layouts.parent`; `quizzes/*` (3) → `layouts.child`; `assessment/*` (1) → `layouts.student`; `share/*`, `pages/*`, `scholarship/*` → `layouts.marketing`; `privacy/`, `notifications/`, `milestones/`, `onboarding`, `profile`, `checkout` (authed flows) → `layouts.parent`; `activities/{tracing,puzzle,drawing}` → `layouts.child`; `activities/index` → `layouts.parent`; `terms`, `privacy.blade.php` → `layouts.marketing`.
- [x] **Deleted `resources/views/layouts/app.blade.php`.** Blade still compiles cleanly; `npm run build` finishes in 428 ms with the same 152.14 KB / 27.85 KB-gzip CSS output.

Final per-layout extender distribution: 36 admin, 19 maternal, 18 parent, 12 marketing, 9 teacher, 8 child, 5 student, 5 practitioner, 4 auth.

## What's still to do, in order

Each bullet below is its own atomic step. They are listed in the order I plan to execute them.

1. **Lucide registry expansion** — `app/View/Components/Ui/Icon.php` defines ~75 Lucide icon paths. 128 unique `bi-*` icons are used across blades. I need to:
   - Build a `bi-*` → Lucide-name mapping table (most are obvious: `bi-trash` → `trash`, `bi-pencil` → `pencil`; some need judgement: `bi-yin-yang`, `bi-egg-fried`, `bi-flower2` will fall back to nearest Lucide concept).
   - Add the ~50 missing icons to the registry.
   - Unit test that every `bi-*` referenced in blades has a registry hit.
2. **`bi-*` → `x-ui.icon` migration** — 58 blade files. Mechanical Edit per file. Keeps `class=""` for size where present.
3. **Bootstrap class purge** — 87 blade files. Class-by-class replacement using a documented mapping (`btn-primary` → `<x-ui.button variant="primary">`, `col-md-6` → `md:col-span-6`/grid, `card-body` → padding, `d-flex gap-2` → `flex gap-2`, `row g-3` → `grid grid-cols-... gap-3`, `navbar-*` → `<x-app.nav-*>` equivalents). Mapping table will go in `docs/phase1-launch/bootstrap-to-tailwind.md`.
4. **Vite `ViteImageOptimizer` decision** — currently commented out. Sharp/libvips need to be available in the build environment. Plan: enable, run `npm run build` to verify, document the requirement.
5. **CSS consolidation** — grep each `nn-*` class used in blades, move its rule (if still needed after Bootstrap purge) from `public/css/playful.css` (2079 lines) and `public/css/tier-*.css` (211 lines combined) into `resources/css/app.css` as either `@layer components` or a `<x-ui.*>` component-scoped utility. Then delete the five `public/css/*.css` files. **Order matters:** delete the `<link rel="stylesheet">` lines in all 10 layouts BEFORE deleting the files.
6. **Layout shim retirement** — 85 views `@extends('layouts.app')`. Migration plan:
   - Map each view to its owning role: admin/* (33), maternal/* (17), teacher/* (8), practitioner/* (5), activities/* (4), quizzes/* (3), parent/* (2), one-offs (15).
   - Per view: change the `@extends` target to the role layout. Verify the role layout supplies the chrome the view depends on (nav, footer, AI bubble, font preloads, RTL). Where the role layout is thinner than the shim, add the missing chrome.
   - After all views migrated: delete `resources/views/layouts/app.blade.php`.
7. **`/_styleguide` page** — admin-only route + view rendering every `x-ui.*` component variant (button, card, badge, alert, input, select, switch, etc.) and the design tokens. Used as the visual regression baseline.
8. **README + AGENTS.md reconciliation** — `README.md` (outer) still markets Bootstrap 5.x at lines 13, 43, 348, and lists 6 languages at line 312. Update to "Tailwind v4 + `x-ui.*`" and 8 languages (adds UR + AR). `AGENTS.md` is a near-duplicate of the gitnexus block in `CLAUDE.md` — note in PR description.
9. **Tests + verification** —
   - `tests/Feature/Phase1/*`: smoke test every route renders 200 and produces no `bi-*` / Bootstrap class strings in the response.
   - `npm run build`: verify Vite produces `public/build/` without warnings about missing CSS or fonts.
   - `npm run a11y` (pa11y-ci) passes.
   - `npm run lighthouse`: mobile score ≥ 90 on `/`, `/pricing`, `/activities`, `/login`, `/register`.
10. **PR description** — screenshots of every role layout in dev mode, Lighthouse + pa11y reports attached.

## Acceptance criteria (from master prompt)

- [ ] `npm run build` produces a built `public/build/`.
- [ ] Lighthouse mobile ≥ 90 on `/`, `/pricing`, `/activities`, `/login`, `/register`.
- [ ] `pa11y-ci` passes.
- [ ] No `bi-`, `btn-primary`, or `col-md-` strings remain in `resources/views`.

## Open questions surfaced during execution

- `playful.css` is 2079 lines and *every* role layout loads it, not just the `layouts/app.blade.php` shim. Confirming with reviewer in PR: the deletion path is "move needed rules to `app.css`, remove the `<link>` in all 10 layouts, then delete the file" — but in case any `nn-*` class is doing structural work I've missed, I will produce a `docs/phase1-launch/playful-css-audit.md` listing every selector and its disposition.
- The `<x-app.nav-*>` and `<x-app.footer>` components rendered inline in each role layout may themselves use Bootstrap classes — needs verification before declaring Phase 1 complete.
- The `<x-app.ai-bubble>` and `<x-app.pwa-installer>` components are out of scope for Phase 1 (they're not visual baseline); leaving them as-is.
