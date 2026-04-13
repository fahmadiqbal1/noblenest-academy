# Noble Nest Academy — Next-Generation Upgrade Plan
**Author:** Architecture working note (Opus 4.6 with sequential-thinking)
**Branch baseline:** `feature/phase-0-foundation`
**Scope:** Precision Curriculum Engine, Thematic Cross-Curricular Architecture, Privacy-First Ecosystem
**Status:** PLAN ONLY — no code changes in this deliverable
**Date:** 2026-04-11

---

## 1. Next-Gen Vision Statement

Noble Nest Academy evolves from a catalog of age-tiered activities into a **living, emotionally-aware learning organism**. Every child receives a curriculum that is:

- **Precise** — each activity carries full parental, safety, cognitive, and developmental metadata so parents are never guessing and the system can reason about fit.
- **Adaptive** — a closed feedback loop (child performance + emotional signals) nudges difficulty, suggests regulation breaks, and predicts frustration before it happens.
- **Holistic** — learning is organized as **Journeys** (thematic, cross-curricular arcs like *"The Ocean Week"* or *"I Feel Big Feelings"*), not siloed subjects.
- **Private by design** — any external health/wellness data (sleep, activity, clinical notes) lives in an isolated, consented, HIPAA-inspired enclave that the curriculum can *query* but never *own*.

The north star: a parent opens the app on Monday and sees *"This week Ayaan is exploring the Ocean. We're gently raising executive-function challenges because he's been thriving, and we've scheduled two calm-down stories because his sleep tracker flagged short nights."* — with the parent in complete control of every data stream that informed that sentence.

---

## 2. Current-State Snapshot (verified against repo)

| Layer | Status | Evidence |
|---|---|---|
| Payment / Stripe webhooks | ✅ Hardened | `app/Http/Controllers/PaymentController.php`, signature + idempotency already in place |
| `activities` table schema | ✅ Phase 2 fields migrated | Database ready: `mess_level`, `safety_warnings`, `adaptations`, etc. |
| `Activity` model fillable/casts | ✅ Phase 2 fields wired | `app/Models/Activity.php` (lines 42-69) |
| Python sidecar `activity_chain.py` | ⚠️ **Basic** | Outputs only title/description/instructions/materials/duration/difficulty/tier/subject/language/is_free |
| PageIndex duplicate-avoidance | ✅ Present but shallow | Title strings only — missing cognitive_domain context |
| Journey / Theme model | ❌ **Does not exist** | No `app/Models/Journey.php`, no themes migration |
| Emotional-regulation track | ❌ **Missing** | No dedicated `cognitive_domain = 'emotional_regulation'` seeder |
| Toddler (2-3y) / School (6-8y) seeders | ❌ **Missing** | Content gap |
| Real-time feedback loop | ❌ **Missing** | `LearningPathService` does not consume `ChildActivityProgress` signals adaptively |
| External health data ingress | ❌ **Does not exist** | No consent model, no isolation enclave |

**Key insight:** The database is already *ahead* of the AI layer. The Python chain silently drops fields the DB is ready to store. Phase 1 is mostly a *catch-up* on the sidecar, not a migration exercise.

---

## 3. Prioritization Framework

Each workstream is scored on a 1–5 scale.

**Priority = Impact − 0.5 × Complexity** (higher = do sooner).

| Workstream | Impact | Complexity | Priority | Phase | Order |
|---|---|---|---|---|---|
| P1.A Enhance `activity_chain.py` to emit full Phase 2 metadata | 5 | 2 | **4.0** | 1 | 1️⃣ |
| P2.A Journey model + theme-week entity | 5 | 3 | **3.5** | 2 | 2️⃣ |
| P1.B Real-time difficulty feedback loop | 5 | 4 | **3.0** | 1 | 3️⃣ |
| P3.A Consent + data-isolation enclave schema | 5 | 4 | **3.0** | 3 | 6️⃣ |
| P1.C Emotional-intelligence track + predictive trigger | 4 | 3 | **2.5** | 1 | 4️⃣ |
| P2.C Content re-seeding around journeys (+ missing tiers) | 4 | 3 | **2.5** | 2 | 4️⃣ (parallel with P1.C) |
| P2.B Cross-curricular orchestration service | 4 | 4 | **2.0** | 2 | 5️⃣ |
| P3.C Parent privacy dashboard | 4 | 2 | **3.0** | 3 | 6️⃣ (parallel with P3.A) |
| P3.B Health-informed recommendation adapter | 3 | 4 | **1.0** | 3 | 7️⃣ |

---

## 4. Dependency Graph

```
P1.A (chain emits metadata)  ──┐
                               ├──► P1.B (feedback loop consumes richer signals)
                               │
P2.A (Journey model)  ─────────┼──► P2.B (cross-curricular orchestration)
                               │         │
                               │         ▼
                               └──► P2.C (re-seed around journeys, new tiers)
                                         │
                                         ▼
                                    P1.C (EI track — needs journey scaffold to bind "calm" activities to journeys)

P3.A (enclave schema + consent)  ──► P3.C (dashboard reads enclave)
                                ──► P3.B (adapter queries enclave, feeds P1.B)
```

**Hard prerequisites:**
- P1.B **requires** P1.A (no point adapting on missing metadata).
- P2.B **requires** P2.A (need a Journey to orchestrate around).
- P1.C **benefits from** P2.A (EI exercises attach to journeys as *regulation beats*).
- P3.B **requires** P3.A (cannot consume data before the enclave exists).
- Everything downstream assumes the **stale GitNexus index is refreshed** (`npx gitnexus analyze`) before P1.A starts.

---

## 5. Phase 1 — Precision Curriculum Engine

### P1.A — Full Metadata Generation (Python sidecar catch-up)

**Goal:** Every AI-generated activity emits all Phase 2 DB fields; no silent drops.

**Files to modify:**
- `services/curriculum-ai/chains/activity_chain.py` — expand system prompt; introduce Pydantic `ActivityPayload` model for structured output.
- `services/curriculum-ai/retrieval/pageindex_retriever.py` — enrich context summary with `cognitive_domain` + `developmental_domains` of prior activities.
- `services/curriculum-ai/main.py` — extend request/response contract.
- `app/Services/AIAssistantService.php` — update HTTP contract; send new knobs (target `cognitive_domain`, target `developmental_domains`); accept expanded response.
- `app/Http/Controllers/Admin/OrchestratorController.php` and `app/Http/Controllers/Admin/ContentBatchController.php` — surface new generation knobs in admin UI.

**Fields the chain must now emit:**
```json
{
  "mess_level": "low|medium|high",
  "safety_warnings": ["choking_hazard", "heat_source"],
  "adaptations": {
    "easier": "Use bigger blocks to reduce fine motor demand",
    "harder": "Add pattern prediction challenge"
  },
  "cognitive_domain": "math|language|science|art|executive_function|emotional_regulation",
  "developmental_domains": ["fine_motor", "social_emotional", "language"],
  "materials_cost": 5,
  "parent_involvement": "minimal|moderate|high",
  "instructions_for_parent": "..."
}
```

**Effort:** **M (5–8 dev-days)**

**Pre-work (mandatory):**
1. Run `gitnexus_impact({target: "AIAssistantService", direction: "upstream"})` to see all callers.
2. Run `gitnexus_impact({target: "Activity", direction: "upstream"})` — the model touches 20+ controllers/services.
3. Freeze prompt version (store in DB or config) for A/B comparison of curriculum quality.

---

### P1.B — Real-Time Feedback Loop

**Goal:** Child performance on activities deterministically nudges the *next* activity selection.

**New concept:** `ChildSkillState` rolling projection (per child × cognitive_domain × developmental_domain) updated on every activity completion.

**New components:**
- **Migration:** `child_skill_states` — (child_profile_id, cognitive_domain, developmental_domain, ema_score, ema_confidence, streak_struggle, streak_success, last_updated).
- **Model:** `app/Models/ChildSkillState.php`
- **Listener:** `app/Listeners/UpdateChildSkillStateListener.php` — fired by `ActivityCompleted` event from `ChildActivityController`.
- **Job:** `app/Jobs/RecomputeLearningPathJob.php` — queued to Horizon after skill-state changes.
- **Service:** Modify `app/Services/LearningPathService.php` — read `child_skill_states`, apply adaptation rules, pick next activity.

**Adaptation rules (first pass):**
- `streak_struggle >= 2` on a cognitive domain → next activity drops one difficulty tier AND schedules one `emotional_regulation` activity.
- `streak_success >= 3` → raise difficulty OR widen developmental_domain coverage.
- Cold-start: weight by age-appropriate averages.

**Effort:** **L (8–12 dev-days)**

**Depends on:** P1.A (needs `cognitive_domain` / `developmental_domains` populated).

---

### P1.C — Emotional-Intelligence Track & Predictive Trigger

**Goal:** A first-class `emotional_regulation` cognitive domain with curated activities and a predictive "call for calm" trigger.

**Components:**
- **Seeder:** `database/seeders/EmotionalRegulationActivitySeeder.php` — ~40 activities covering breathing, naming feelings, co-regulation, transitions, frustration tolerance.
- **Service:** `app/Services/EmotionalRegulationService.php` — pure function deciding whether to inject an EI beat.
- **Modify:** `LearningPathService` — consult `EmotionalRegulationService` before returning next activity.
- **UI:** Parent dashboard banner explaining the EI beat (parent trust > silent intervention).

**Predictive trigger heuristic (v1):**
```
score = w1 * recent_abandonment_rate + w2 * struggle_streak + w3 * time_since_last_success
→ if score > threshold: inject EI beat
```

**Effort:** **M (5–8 dev-days)**

---

## 6. Phase 2 — Thematic, Cross-Curricular Architecture

### P2.A — Journey Model

**Goal:** `Journey` = an ordered, themed arc pulling activities from multiple subjects. Example: *"The Ocean"* pulls Science, Art, Language, and Emotional Regulation activities into a 5-day flow.

**Migrations:**
```sql
CREATE TABLE journeys (
  id BIGINT PRIMARY KEY,
  slug VARCHAR(255) UNIQUE,
  title VARCHAR(255),
  description TEXT,
  theme VARCHAR(255),
  age_tier VARCHAR(50),
  duration_days INT,
  cover_media_url VARCHAR(255),
  is_published BOOLEAN,
  is_premium BOOLEAN,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE journey_activities (
  journey_id BIGINT,
  activity_id BIGINT,
  day_number INT,
  order_in_day INT,
  role ENUM('intro', 'core', 'extension', 'regulation_beat'),
  PRIMARY KEY (journey_id, activity_id, day_number, order_in_day),
  FOREIGN KEY (journey_id) REFERENCES journeys(id) ON DELETE CASCADE,
  FOREIGN KEY (activity_id) REFERENCES activities(id)
);

CREATE TABLE child_journey_enrollments (
  id BIGINT PRIMARY KEY,
  child_profile_id BIGINT,
  journey_id BIGINT,
  progress_percent DECIMAL(5,2),
  started_at TIMESTAMP,
  completed_at NULLABLE TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

**Models:**
- `app/Models/Journey.php`
- `app/Models/ChildJourneyEnrollment.php`

**Services:**
- `app/Services/JourneyOrchestrationService.php` — picks next journey, handles hand-offs, respects subscription tiers.

**Controllers:**
- `app/Http/Controllers/Admin/JourneyController.php` (new) — CRUD
- Modify `app/Http/Controllers/Parent/DashboardController.php` — show current journey banner
- Modify `app/Http/Controllers/Child/DashboardController.php` — pivot to journey-flow UI

**Effort:** **L (10–14 dev-days)**

**⚠️ Risk:** Child dashboard is a high-risk surface. Feature-flag the journey UI until confidence is high.

---

### P2.B — Cross-Curricular Orchestration

**Goal:** Within a journey, automatically sequence activities to maintain variety and developmental balance.

**Components:**
- **Extend:** `JourneyOrchestrationService` with a sequencer applying constraints: subject diversity, cognitive_domain rotation, mess_level budget per day, materials_cost budget per week.
- **Job:** `app/Jobs/PrecomputeJourneyScheduleJob.php` — nightly pre-computation for every enrolled child.
- **Cache:** Redis key per child journey: `child:{id}:journey:{id}:schedule` with TTL aligned to day boundaries.

**Effort:** **L (8–10 dev-days)**

---

### P2.C — Content Re-seeding + Missing Tiers

**Goal:** Fill Toddler (2–3y) and School (6–8y) seeders, group ~70% of new content into journeys.

**Seeders:**
- `database/seeders/ToddlerActivitySeeder.php` (new)
- `database/seeders/SchoolAgeActivitySeeder.php` (new)
- `database/seeders/JourneySeeder.php` (new) — ~10 launch journeys:
  - The Ocean
  - Space
  - My Body
  - Big Feelings
  - The Market
  - Seeds & Plants
  - Shapes All Around
  - Family & Home
  - Night Sky
  - Weather

**QA gate:** Every published journey must have ≥1 `emotional_regulation` beat per 3 days.

**Effort:** **L (content-heavy, 10+ days, parallelizable with writers)**

---

## 7. Phase 3 — Privacy-First Ecosystem Integration

### P3.A — Consent + Isolation Enclave

**Goal:** External health/wellness data (sleep tracker, pediatrician note, activity band) is stored in a logically isolated enclave with strict access rules.

**Architectural guardrails:**
- **Separate schema/namespace:** enclave tables live in their own migration group and ideally a separate MySQL *schema* (e.g., `nnacademy_health`) with a distinct DB user.
- **No foreign keys from curriculum tables into the enclave.** Curriculum references enclave data only via signed opaque tokens.
- **Every read is audited.** Every write requires an active consent record.
- **Right-to-erasure is cheap:** dropping one `parent_consent` row cascades a job that purges all enclave rows + audit trail within N hours.

**Migrations (enclave schema):**
```sql
CREATE TABLE parent_consents (
  id BIGINT PRIMARY KEY,
  parent_user_id BIGINT,
  child_profile_id BIGINT,
  data_source VARCHAR(255),
  scope JSON,
  granted_at TIMESTAMP,
  revoked_at NULLABLE TIMESTAMP,
  version INT,
  signed_hash VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE health_data_ingestions (
  id BIGINT PRIMARY KEY,
  raw_payload JSON,
  source VARCHAR(255),
  received_at TIMESTAMP,
  integrity_hash VARCHAR(255),
  consent_id BIGINT,
  FOREIGN KEY (consent_id) REFERENCES parent_consents(id)
);

CREATE TABLE health_data_facts (
  id BIGINT PRIMARY KEY,
  fact_type VARCHAR(255),
  fact_value VARCHAR(255),
  source VARCHAR(255),
  child_profile_id BIGINT,
  consent_id BIGINT,
  FOREIGN KEY (consent_id) REFERENCES parent_consents(id)
);

CREATE TABLE health_enclave_access_logs (
  id BIGINT PRIMARY KEY,
  actor_user_id BIGINT,
  purpose VARCHAR(255),
  consent_id BIGINT,
  accessed_at TIMESTAMP
);
```

**Services:**
- `app/Services/HealthEnclaveService.php` — sole authorized reader/writer; every method takes a `Purpose` enum and emits an access-log row.
- `app/Providers/HealthEnclaveServiceProvider.php` — binds second DB connection.

**Config:**
- `config/database.php` — add `health_enclave` connection.

**Effort:** **L (10–14 dev-days)**

**🔴 Review gate:** Mandatory security review before merge.

---

### P3.B — Health-Informed Recommendation Adapter

**Goal:** Let `LearningPathService` *ask* "is this child under-slept?" without ever touching raw data.

**Components:**
- **Service:** `app/Services/HealthContextAdapter.php` — thin wrapper around `HealthEnclaveService` exposing only derived, coarse-grained booleans/buckets.
  - Example: `sleepBucket(): 'short'|'normal'|'unknown'` — never exact hours.
- **Modify:** `LearningPathService` — optional dependency; behaves identically if adapter returns `unknown`.

**Rules example:**
```php
if ($adapter->sleepBucket() === 'short') {
    // Bias next activity toward duration_minutes <= 15
    // Schedule one regulation beat
}
```

**Effort:** **M (4–6 dev-days)**

---

### P3.C — Parent Privacy Dashboard

**Goal:** A single page showing every data stream, consent state, last access, and a big "revoke" button.

**Components:**
- Modify `app/Http/Controllers/PrivacyController.php` — extend with consent UI.
- `resources/views/parent/privacy/dashboard.blade.php` (new) — consent toggles, access log view, export button, revoke button.
- `app/Http/Controllers/Parent/ConsentController.php` (new) — grant/revoke endpoints (CSRF-protected, password re-confirm for revocation).
- `app/Jobs/PurgeHealthEnclaveOnRevokeJob.php` (new).

**Effort:** **M (5–7 dev-days)**

---

## 8. Risks & Mitigations

| # | Risk | Phase | Severity | Mitigation |
|---|---|---|---|---|
| R1 | LLM output drift — enriched prompt returns malformed JSON, breaking ingestion | P1.A | 🔴 High | Pydantic structured output + JSON-schema validation + dead-letter queue for failed generations; version the prompt and A/B against current catalog |
| R2 | Adaptive loop creates "rabbit holes" (child stuck on too-easy content) | P1.B | 🟡 Medium | Hard caps: no more than N consecutive activities in the same domain; weekly variety floor enforced by sequencer |
| R3 | EI trigger feels intrusive / pathologizing to parents | P1.C | 🟡 Medium | Parent copy focuses on "variety + calm," never "your child struggled"; feature-flag + opt-out in settings |
| R4 | Journey pivot breaks existing child dashboard UX | P2.A | 🔴 High | Feature flag `journeys.enabled`, roll out to 5% of accounts, keep subject-tab view as fallback for 2 sprints |
| R5 | Cross-curricular sequencer is slow at request time | P2.B | 🟡 Medium | Pre-compute in `PrecomputeJourneyScheduleJob` on Horizon, Redis-cache per child/day |
| R6 | Content re-seeding collides with live data | P2.C | 🟡 Medium | All new seeders idempotent + gated by env flag; staging-first; use database transactions |
| R7 | Health enclave leak via ORM relationship traversal | P3.A | 🔴 **CRITICAL** | Separate DB connection + separate DB user + no FKs across the boundary + static analysis rule forbidding `Eloquent` models in curriculum namespace from referencing enclave models |
| R8 | Consent UX confusing → accidental over-sharing | P3.C | 🔴 High | Default-deny, explicit per-scope toggles, plain-language copy, require re-auth for grants |
| R9 | GDPR / COPPA exposure from health data | P3.A | 🔴 **CRITICAL** | Legal review before P3 kicks off; DPIA document; data minimization (store facts, not payloads); automated retention job |
| R10 | Stale GitNexus index gives wrong impact analysis | All | 🟡 Medium | Run `npx gitnexus analyze --embeddings` immediately before starting each phase; block merges if `detect_changes` mismatch |
| R11 | Python sidecar contract drift between PHP and Python teams | P1.A | 🟡 Medium | Shared JSON-schema file checked into repo at `services/curriculum-ai/contracts/activity_payload.schema.json`; CI verifies both sides |
| R12 | Horizon queue backlog from new jobs (RecomputeLearningPath + PrecomputeJourneySchedule) | P1.B + P2.B | 🟡 Medium | Dedicated Horizon supervisor + queue with rate limit; monitor via `HorizonServiceProvider`; de-dup identical jobs within 60s window |

---

## 9. Cross-Cutting Pre-requisites (do these once, before Phase 1)

1. **Refresh GitNexus index** — `npx gitnexus analyze --embeddings`. The working tree has 40+ modified files; impact analysis is only as good as a fresh index.

2. **Define shared Activity JSON schema** (checked into repo) — source of truth for PHP validation and Python output validation.
   - File: `services/curriculum-ai/contracts/activity_payload.schema.json`

3. **Stand up contract tests** between `AIAssistantService.php` and the Python sidecar using the shared schema.

4. **Feature flag framework** — confirm the codebase already has one; if not, add a tiny `FeatureFlagService` (Redis-backed).

5. **Observability baseline** — add metrics:
   - Activities generated per day
   - Activities with full metadata %
   - EI-beats scheduled per child per week
   - Adaptive-rule firings per cognitive domain
   - Enclave reads per purpose
   - Without this, we cannot measure whether the upgrade works.

---

## 10. Suggested Sprint Layout (two-week sprints, one full-stack team)

| Sprint | Workstream | Exit criteria |
|---|---|---|
| **S1** | Pre-requisites + P1.A | Shared schema live, sidecar emits full metadata, admin UI shows new fields, regression seed of 50 activities passes |
| **S2** | P1.A polish + P2.A scaffold | Journey migrations merged, admin CRUD working, 3 seeded journeys, feature flag wired |
| **S3** | P1.B | `ChildSkillState` live, feedback loop firing in staging, adaptation rules unit-tested, metrics dashboard showing activity |
| **S4** | P2.C (content, parallel writers) + P1.C service | Toddler + School-age seeds in staging, EI service injecting beats in staging, integration tests passing |
| **S5** | P2.B | Sequencer + Redis cache + pre-compute job, feature-flagged journey UI at 5% in production |
| **S6** | P3.A | Enclave schema merged, HealthEnclaveService with full audit, security review passed ✅ |
| **S7** | P3.B + P3.C | Adapter wired into LearningPathService behind flag, privacy dashboard live for all parents |
| **S8** | Hardening + GA | Remove feature flags, load test, DPIA sign-off, GA announcement, retrospective |

**Total:** ~16 weeks for a single full-stack team; shorter if Python sidecar and Laravel work are parallelized across two people.

---

## 11. Definition of Done (for the whole upgrade)

- [ ] 100% of AI-generated activities carry every Phase 2 metadata field, validated against shared JSON schema.
- [ ] `LearningPathService` demonstrably changes next-activity selection based on `ChildSkillState` (verified by integration test).
- [ ] `emotional_regulation` activities exist across toddler → school tiers and are auto-injected under documented conditions.
- [ ] At least 10 published Journeys, each with balanced subject coverage and ≥1 EI beat per 3 days.
- [ ] Child and parent dashboards pivot to journey-first UX (with old subject view still accessible).
- [ ] Health enclave lives on a separate DB connection, enforces consent on every read, logs every access, and passes a security review.
- [ ] Parent privacy dashboard allows one-click revocation that purges enclave data within documented SLA.
- [ ] All new code paths have `gitnexus_impact` and `gitnexus_detect_changes` audit trails in their PR descriptions.
- [ ] DPIA filed, COPPA/GDPR copy reviewed by legal.

---

## 12. Key Artifacts & File Paths to Watch

**Python AI Sidecar:**
- `services/curriculum-ai/chains/activity_chain.py`
- `services/curriculum-ai/retrieval/pageindex_retriever.py`
- `services/curriculum-ai/main.py`

**PHP Services:**
- `app/Services/AIAssistantService.php`
- `app/Services/LearningPathService.php`
- `app/Services/MilestoneService.php`
- `app/Services/VideoGenerationService.php` (GitNexus flagged — natural fit for Journey intro videos in P2.A)

**Models & Controllers:**
- `app/Models/Activity.php`
- `app/Models/ChildProfile.php`
- `app/Http/Controllers/Parent/DashboardController.php`
- `app/Http/Controllers/Child/DashboardController.php`
- `app/Http/Controllers/PrivacyController.php`
- `app/Http/Controllers/Admin/OrchestratorController.php`
- `app/Http/Controllers/Admin/ContentBatchController.php`

**Migrations:**
- `database/migrations/2026_04_11_000002_add_enhanced_fields_to_activities.php`

**Infrastructure:**
- `app/Providers/HorizonServiceProvider.php`

---

## Next Steps

1. **Executive alignment** — review the next-gen vision and sprint layout with stakeholders.
2. **Refresh GitNexus index** — `npx gitnexus analyze --embeddings` in terminal.
3. **Kick off S1** — pre-requisites + P1.A; target completion 2 weeks.

---

*This plan is the authoritative specification for Noble Nest Academy's next evolution. All code changes should reference this document and include impact analysis via `gitnexus_impact` before each PR.*
