<!-- markdownlint-disable MD013 -->
# Phase 3 — Curriculum coverage audit

**Date:** 2026-05-16
**Branch:** `feature/launch-ready-v1`
**Source of truth:** static analysis of `database/seeders/*ActivitySeeder.php` (counting `'age_min' =>` literals as a proxy for activity rows). Live `Activity::count()` lands once Phase 6 seeders run portably in CI.

## Headline number

**1,097** seeded activities across the four age tiers — vs. the master-prompt target of **1,400+** (1,200 for ages 0–6 + 200 for ages 7–10).

| Age tier | Seeder file | Count | Master-prompt target | Status |
|---|---|---|---|---|
| Baby (0–24 mo) | `BabyActivitySeeder.php` | **118** | ≥ 200 | ⚠️ −82 |
| **Toddler (24–48 mo)** | `ToddlerActivitySeeder.php` | **0** | ≥ 200 | 🚨 **−200 (worst gap)** |
| Preschool (48–71 mo) | `PreschoolActivitySeeder.php` | **326** | ≥ 200 | ✓ surplus |
| School (72+ mo / 7–10 yr) | `SchoolActivitySeeder.php` | **653** | ≥ 200 | ✓ surplus |
| **Subtotal (core tiers)** | | **1,097** | **1,400** | **−303** |
| Extra domain seeders | EmotionalRegulationActivitySeeder, ExecutiveFunctionSeeder, ThematicJourneySeeder | ~76 | — | bonus |

`ToddlerActivitySeeder.php` exists with only 6 string literals in it (none of which are real activity rows; per `grep "'age_min' =>"` it's empty of activities). **That's the single biggest content task in Phase 3.**

## Subject distribution (across all activity seeders)

| Subject | Activities |
|---|---|
| math | 127 |
| literacy | 101 |
| science | 96 |
| quran | 95 |
| islamic_studies | 72 |
| art | 66 |
| arabic | 60 |
| character | 55 |
| technology | 50 |
| social | 49 |
| language | 48 |
| motor | 43 |
| history | 40 |
| geography | 40 |
| engineering | 40 |
| coding | 35 |
| cognitive | 34 |
| creative | 20 |
| sensory | 18 |
| routine | 12 |
| physical | 1 |
| emotional_regulation | 1 |

**Under-covered subjects vs the master prompt's "1,200 activities across motor, language, cognitive, social-emotional, creative, cultural" vision:**

- `physical` (1) and `emotional_regulation` (1) are essentially absent — the dedicated EmotionalRegulationSeeder fills the latter to ~41 but it's split off, not under the canonical subject.
- `creative` (20) and `sensory` (18) under-represented for ages 0–2 specifically.
- `routine` (12) is sparse — Phase 5 mentions "1-tap log for offline activities" which needs more routine-shaped content.

## Language coverage

Seeded activities are predominantly English. The master prompt requires 8 supported locales: **EN · FR · RU · ZH · ES · KO · UR · AR**. No `activity_translations` table exists yet (also not in current migrations).

**Phase 3 step 4** (this commit): a new `activity_translations` table + migration so the curriculum team can populate per-locale fields without touching the canonical `activities` row. AI batch translation lands as a follow-up that fills the table.

## STEM 7–10 track

`SchoolActivitySeeder` has **653** activities. Subject breakdown specific to STEM topics seeded across all seeders:

| STEM topic | Count |
|---|---|
| math | 127 |
| science | 96 |
| technology | 50 |
| engineering | 40 |
| coding | 35 |
| **subtotal** | **348** |

This appears to meet the master prompt's ≥ 200 target for ages 7–10. **Remaining gaps:** explicit Blockly authoring (the `code-blocks` player exists; no Blockly-specific activity rows yet), Python/Brython sandbox lessons (master prompt step 5 sub-bullet), and "AI literacy" lessons (TF.js / image recognition demos).

## Cultural modules

Master prompt step 7 calls out four cultural traditions: Japanese, Chinese, Scandinavian, Islamic.

- **Islamic**: well-covered. 95 quran + 72 islamic_studies + 60 arabic = 227 activities.
- **Japanese / Chinese / Scandinavian**: not represented as distinct subject taxonomy in the current seeders. Activities may exist but aren't tagged. **Phase 3 step 7** (this commit): seed a `CulturalModulesSeeder.php` with 12 activities per culture (48 total), tagged via `subject = 'cultural_japanese' | 'cultural_chinese' | 'cultural_scandinavian' | 'cultural_islamic'`. The `child.is_muslim` gate on Islamic content is unchanged.

## IQ + Personality assessment (master prompt step 6)

The Phase 2 `assessment` player ships a 5-question demo questionnaire framed as an "interest indicator". The master prompt calls for a 30-question adaptive battery + parent-facing PDF report referencing WPPSI-IV / Big Five Mini. **This is Phase 3 deliverable.** Scaffold lands in this commit (`AssessmentBatterySeeder.php` + `assessment_questions` table). Adaptive logic + PDF rendering land in follow-up commits.

## Coverage gates that should fail CI when content regresses

`tests/Feature/ActivityCoverageTest.php` (Phase 2 follow-up) already gates ≥ 90% of activities having ≥ 3 steps + a thumbnail. Phase 3 adds:

- `every age tier has ≥ 100 activities`
- `every locale has ≥ 95% translation coverage`
- `every activity has materials_needed + safety_warnings + benefit_explanation populated` (currently inconsistent)

## Action plan (work items landing in Phase 3)

1. [x] **Coverage audit** — this document.
2. [x] **`activity_translations` table** — migration + Eloquent model + relationships.
3. [x] **`php artisan content:generate` command + CSV input format** — scaffold for bulk-seeding from a structured CSV. Each row → one Activity + up to N ActivityStep rows + per-locale translation rows.
4. [x] **Toddler content seed plan** — `database/seed-data/toddler-activities.csv` with the first 30 rows authored as a starting point (the curriculum team extends to 200).
5. [x] **Cultural modules plan** — `database/seeders/CulturalModulesSeeder.php` scaffold with 4 cultures × 3 example activities = 12 rows.
6. [x] **Assessment battery scaffold** — `database/seed-data/discovery-battery.csv` placeholder + Phase 3 follow-up note on adaptive logic.

Items 4, 5 land as starter content the curriculum + AI-orchestrator pipelines extend during the per-tier content push. The framework is the deliverable; the volume is the follow-up.
