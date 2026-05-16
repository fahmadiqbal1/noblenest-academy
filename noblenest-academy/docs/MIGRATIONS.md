# Migrations — v1 launch notes

## What Phase 1 changed

Before Phase 1: **79 migrations**, half belonging to deleted verticals (Maternal, Practitioner, Teacher marketplace, Payouts, Scholarships, ShareCards, Daily.co, Referrals, ContraindicationMatrix, ContentReviews, ClassSessions, etc.).

After Phase 1: **53 migrations**, all creating or altering tables that exist in the v1 scope.

### Deleted migration files (26)

```
2025_08_13_000000_create_teacher_courses_table.php
2025_08_13_100000_create_teacher_enrollments_table.php
2025_08_13_200000_create_class_sessions_table.php
2026_04_02_000002_create_teacher_profiles_table.php
2026_04_02_000005_create_referrals_table.php
2026_04_02_000006_create_scholarships_table.php
2026_04_02_000009_create_payout_requests_table.php
2026_04_02_000010_create_school_inquiries_table.php
2026_04_02_000011_create_course_reviews_table.php
2026_04_02_000013_extend_class_sessions_table.php
2026_04_02_000016_create_share_cards_table.php
2026_04_05_000002_create_maternal_profiles_table.php
2026_04_05_000003_create_maternal_contents_table.php
2026_04_05_000004_create_maternal_content_steps_table.php
2026_04_05_000005_create_maternal_meal_plans_table.php
2026_04_05_000006_create_maternal_exercise_plans_table.php
2026_04_05_000007_create_maternal_progress_table.php
2026_04_05_000008_create_maternal_journals_table.php
2026_04_05_000009_create_maternal_emergency_signs_table.php
2026_04_05_000010_create_contraindication_matrix_table.php
2026_04_05_000014_create_practitioner_profiles_table.php
2026_04_05_000015_create_content_reviews_table.php
2026_04_05_063418_make_maternal_content_enrichment_fields_nullable.php
2026_04_13_0005_create_share_card_shares_table.php
2026_04_13_0006_create_referral_rewards_table.php
2026_04_13_0012_extend_referrals_table.php
```

### Patched migration files (1)

- `2026_04_02_000012_extend_child_profiles_table.php` — removed the `share_card_url` column (the rest of the file — `age_tier`, `streak_days`, `last_activity_date` — is kept).

## Migration-squash decision: DEFERRED to Phase 2

The original Phase 1 plan called for collapsing the remaining migrations into a single
`2026_05_16_000000_consolidated_v1_schema.php` and moving the 53 historical files into
`database/migrations/_archived_pre_v1/`.

This step is **deferred to Phase 2** for the following reasons:

1. **Phase 1 already achieved the core compliance goal** — "do not leave deleted-table migrations in history." Every remaining migration creates or alters a v1 table.
2. **Phase 2 will edit the schema anyway** — re-adding foreign keys that were previously dropped for MariaDB compatibility (Hostinger MySQL 8 supports them), adding the `ConsentReceipt` table, and adding factories for every model. Squashing now and then patching again is churn.
3. **The 53 remaining migrations apply cleanly** — `php artisan migrate:fresh --seed` is green on both SQLite (Phase 1 verification) and MySQL (verified locally via the Docker compose stack on port 3306).
4. **Squash semantics are easier post-Phase 2** — once FKs are added and `ConsentReceipt` exists, the consolidated schema represents the *final* v1 surface, not an intermediate state.

**Phase 2 deliverable:** Generate `2026_06_XX_000000_consolidated_v1_schema.php` via `php artisan schema:dump`, wrap the resulting SQL in a single migration class using `DB::unprepared()`, archive the 53 historical files into `database/migrations/_archived_pre_v1/` (subdirectory; Laravel's migrator only scans the top level of `database/migrations/` so the archive is excluded automatically), and verify `migrate:fresh --seed` on a fresh MySQL 8 container produces an identical schema diff against the pre-squash state.

## Local dev DB

`docker-compose.yml` provisions MySQL 8 on `localhost:3306`. `.env` defaults:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=noblenest
DB_USERNAME=noblenest
DB_PASSWORD=noblenest_dev_2026
```

`phpunit.xml` overrides to SQLite in-memory (`:memory:`) for fast tests.

To run migrations against MySQL locally:

```bash
docker compose up -d db
php artisan migrate:fresh --seed
```

## Production schema management

Production runs `php artisan migrate --force` during deploy (Phase 13). No schema drift between
local and production is tolerated — CI's `migrate:fresh --seed` must be green on every PR
(Phase 11 gate).
