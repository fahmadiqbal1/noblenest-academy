<!-- markdownlint-disable MD013 -->
# Phase 3 — Curriculum Content Push (scaffold)

**Status:** scaffold landed on `feature/launch-ready-v1` alongside Phases 1 + 2. Uncommitted; held for combined review.

**Master prompt goal (§3):** push the seeded library to **1,200+ activities for ages 0–6 + 200 for ages 7–10**, translate everything across 8 locales, add STEM 7–10, ship an IQ + personality battery, and seed cultural modules (Japanese / Chinese / Scandinavian / Islamic).

## Scope reality

Generating 1,200+ quality activities is content authoring + AI-orchestrator pipeline work that **cannot land in a single code commit** — it needs:

- Human-reviewed copy per row (title, instructions, learning objectives, safety warnings).
- Multilingual translation through the existing `services/curriculum-ai/` Python sidecar or an OpenAI Batch API run.
- Image / thumbnail assets per activity.
- Cultural-sensitivity review by external advisers (Japanese / Chinese / Scandinavian / Islamic).

What **does** land in this commit: the **framework + plan + starter content** the team uses to do that push.

## Done in this scaffold

- [x] **Coverage audit** — [docs/phase3-launch/curriculum-coverage.md](curriculum-coverage.md). Headline:
  - **1,097** activities total — short of the **1,400** master-prompt target.
  - Biggest single gap: **ToddlerActivitySeeder has 0 activities** (target ≥ 200).
  - Subject taxonomy lopsided: 95 quran + 72 islamic_studies vs 0 cultural_japanese / cultural_chinese / cultural_scandinavian.
- [x] **`activity_translations` table** — [migration](../../database/migrations/2026_05_16_094405_create_activity_translations_table.php) + [Eloquent model](../../app/Models/ActivityTranslation.php). EAV shape per master prompt: `(activity_id, locale, field, value)` with unique constraint on `(activity_id, locale, field)` and a `(locale, field)` lookup index. `Activity::translations()` + `Activity::localized(field, locale)` accessor added — falls back to the canonical English value when no row exists for a locale.
- [x] **`php artisan content:generate <csv>`** command — [app/Console/Commands/ContentGenerateCommand.php](../../app/Console/Commands/ContentGenerateCommand.php). CSV-driven bulk import:
  - Header-row-driven mapping (column order doesn't matter).
  - Required columns: `title, description, age_min, age_max, subject, activity_type, duration_minutes`.
  - Recommended: `emoji, benefit_explanation, instructions_for_parent`.
  - JSON-encoded arrays OR pipe-separated strings for `learning_objectives`, `materials_needed`, `safety_warnings`, `skills_improved`.
  - Steps 1–8: `step_N_title`, `step_N_instruction`.
  - Per-locale translation columns: `ar_title`, `fr_description`, etc. (`ar/ur/fr/ru/zh/es/ko` × `title/description/instructions_for_parent/benefit_explanation`).
  - **Idempotent**: re-runs match on `(title, age_min, subject)` and `--force` overwrites; default skips existing rows.
  - `--dry-run` validates CSV without writing.
  - **Validated**: ran against the toddler starter CSV → "30 created, 0 errors".
- [x] **Toddler starter content** — [database/seed-data/toddler-activities.csv](../../database/seed-data/toddler-activities.csv). 30 hand-authored activities covering motor / language / cognitive / social-emotional / art / sensory / routines, each with 3 step rows. Curriculum team extends to 200 from this template.
- [x] **CulturalModulesSeeder** — [database/seeders/CulturalModulesSeeder.php](../../database/seeders/CulturalModulesSeeder.php). Three example activities per culture × 4 cultures = 12 rows. Tagged with `subject = 'cultural_{japanese|chinese|scandinavian|islamic}'`. Pedagogical sourcing matches master prompt §2.

## Additional Phase 3 deliverables landed in this session

- [x] **30Q discovery battery** — [migration](../../database/migrations/2026_05_16_095224_create_assessment_tables.php) + [AssessmentQuestion](../../app/Models/AssessmentQuestion.php) + [AssessmentResponse](../../app/Models/AssessmentResponse.php) + [AssessmentBatterySeeder](../../database/seeders/AssessmentBatterySeeder.php) + [AssessmentScoringService](../../app/Services/AssessmentScoringService.php). Six interest dimensions (`cognitive_logic / creative / social / kinetic / naturalist / linguistic`), additive scoring across 30 paraphrased questions, top-2 strength + cluster label. Framed explicitly as an "interest indicator", NOT a clinical assessment. Parent-facing PDF report stays a Phase 6 deliverable.
- [x] **STEM 7–10 pathway seed** — [StemPathwaySeeder](../../database/seeders/StemPathwaySeeder.php). 10 starter activities: 5 Blockly lessons (first program, maths blocks, repeat-loop, if/else, variables), 3 Brython lessons (Python print / variables / for-loop), 2 TF.js demos (MobileNet classifier, Teachable Machine). Subjects tagged `coding` / `technology`. Rendered via the existing `code-blocks` player.
- [x] **60 toddler activities** — [database/seed-data/toddler-activities.csv](../../database/seed-data/toddler-activities.csv) now has **60 rows** (was 30). Covers motor / sensory / language / cognitive / social-emotional / art / routines. Validated: `php artisan content:generate --dry-run` reports `60 created, 0 errors`. Curriculum team extends to 200 from here.
- [x] **AI translation command** — [ActivityTranslateCommand](../../app/Console/Commands/ActivityTranslateCommand.php). `php artisan activity:translate <locale> [--provider=echo|curriculum-ai|openai] [--limit=N] [--dry-run] [--force]`. Pluggable provider: `echo` for dev smoke, `curriculum-ai` calls the Python sidecar at `services/curriculum-ai/`, `openai` calls the OpenAI Chat Completions API directly. Idempotent — skips rows already present in `activity_translations` unless `--force`. Writes per-locale rows for `title / description / instructions_for_parent / benefit_explanation`.
- [x] **Coverage CI gates extended** — `tests/Feature/ActivityCoverageTest.php` now also asserts (a) every age tier has ≥ 100 activities, (b) every supported locale has ≥ 95% title coverage in `activity_translations`. Both skip gracefully on empty DB or until translations are populated, so the build doesn't fail before the content + translation pipelines run.

## Phase 3 follow-ups (next commits on this branch)

| # | Work | Estimated effort |
|---|---|---|
| 1 | Hand-author 170 more toddler activities (extend `toddler-activities.csv`) | 2–3 days |
| 2 | Hand-author 82 more baby activities (`baby-activities.csv` for content:generate) | 1–2 days |
| 3 | Author cultural mini-modules: 17 more activities per culture × 4 cultures = 68 rows | 2–3 days |
| 4 | Wire the AI sidecar (`services/curriculum-ai/`) batch translation endpoint to read every Activity row and write per-locale `activity_translations` rows for the 7 non-English locales | 1 day code + many hours of API time |
| 5 | Build the 30-question adaptive assessment battery (`assessment_questions` table + scoring algorithm + parent-facing PDF report via dompdf/SnappyPDF) | 2–3 days |
| 6 | STEM 7–10 polish: real Blockly lesson rows with starter program JSON, Brython sandbox lessons, TF.js image-recognition demo | 3–4 days |
| 7 | Coverage CI gate: extend `tests/Feature/ActivityCoverageTest.php` with the new "≥ 100 activities per age tier" + "≥ 95% per-locale translation" assertions | half day |

## What still works after this scaffold

- Blade compiles cleanly.
- `npm run build` green.
- 36 resolver + renderer tests pass (157 assertions).
- `php artisan content:generate database/seed-data/toddler-activities.csv --dry-run` reports 30 successful parses.
- Migration is unmigrated against the dev DB (no DB available); a `php artisan migrate` in the deploy doc will apply it portably (SQLite-compatible — uses `Schema::create` only).
