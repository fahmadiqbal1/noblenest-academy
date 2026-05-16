<!-- markdownlint-disable MD013 -->
# Assessment Rubrics — Noble Nest Academy

How activity completion, step coverage, and the discovery battery are scored. This document is the authoritative reference for Cognia / BAC reviewers.

## 1. Activity Completion Score

Each activity in the `activities` table has a `type` that maps to a player. Completion is binary (pass/fail) unless the player supports partial credit.

| Activity type | Pass criterion | Partial credit |
|---|---|---|
| `quiz` | ≥ 70 % of questions correct | No — must retry |
| `assessment` | All questions answered | Yes — scored by `AssessmentScoringService` |
| `drag-and-match` | All pairs matched correctly | No |
| `code-blocks` | Program runs without error + output matches expected | Yes — test cases |
| `tracing-canvas` | Trace covers ≥ 85 % of template path | Yes |
| `drawing-canvas` | Submission received | Peer/teacher review |
| `guided-steps` | All steps marked complete | Yes — per-step |
| `video-lesson` | Watched ≥ 80 % of video | Yes |
| `song-and-movement` | Participation flag set | No |

## 2. Step Coverage (guided-steps)

The `guided-steps` player renders steps from the activity's JSON content. Each step has a `completed_at` timestamp in the `activity_completions` pivot.

```
step_coverage = completed_steps / total_steps
```

Minimum for activity pass: **step_coverage ≥ 0.75**.

## 3. Quiz Scoring

Handled by `AssessmentScoringService::scoreQuiz()`:

```
raw_score = correct_answers / total_questions
weighted_score = raw_score * difficulty_multiplier   # difficulty: 1.0 / 1.2 / 1.5
```

Reported to parent dashboard as percentage. Stored in `assessment_responses.score`.

## 4. Discovery Battery (Assessment Player)

The battery has four sections:

### 4a. Spatial Reasoning
- Format: 12 matrix-reasoning items (3×3 grids, select the missing piece).
- Scoring: IRT-based (Item Response Theory, 1-parameter Rasch model approximation).
- Output: Ability estimate θ (−3 to +3); converted to percentile using age-normed table.

### 4b. Verbal Analogies
- Format: 10 items ("Cat : Kitten :: Dog : ___").
- Scoring: 1 point per correct answer.
- Output: Raw score → age-percentile lookup.

### 4c. Working Memory
- Format: Forward and backward digit span (starts at 3 digits).
- Scoring: Longest span achieved in both directions.
- Output: Combined span score; clinical reference range noted.

### 4d. Big-5 Personality (Lite)
- Format: 15 Likert-scale statements (1–5).
- Scoring: Factor-scored per trait (O, C, E, A, N).
- Output: Trait profile displayed as radar chart on parent dashboard.

## 5. Teacher / Practitioner Rubric (open-ended tasks)

For `drawing-canvas` and written reflections, practitioners use a 4-point holistic rubric:

| Score | Descriptor |
|---|---|
| 4 — Exceeds | Evidence beyond age-level expectation; creative extension |
| 3 — Meets | Demonstrates age-level understanding; task complete |
| 2 — Approaching | Partial understanding; key elements missing |
| 1 — Beginning | Minimal engagement; significant support needed |

## 6. Progress Milestones

A child earns a milestone badge when:
- **Domain milestone**: 80 % of activities in a domain at their tier are passed.
- **Tier completion**: All required activities across all domains are passed.
- **Cultural explorer**: At least one activity from 3 distinct cultural modules is passed.

Milestone data is stored in `milestones` and displayed on the `milestones/wall` view.
