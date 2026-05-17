# Instructor & Curriculum Author Credentials

**Status:** Placeholder for Cognia / BAC submission.
**Owner:** Operator (founder) to populate before submission.

Noble Nest Academy is a self-paced, AI-supported learning platform for
parents and children aged 0–10. There are no live human instructors in
v1; activities and assessments are authored by curriculum specialists
and reviewed by domain experts before publication.

## Curriculum authors

| Role | Responsibility | Credentials required | Status (v1) |
|---|---|---|---|
| Lead Curriculum Author | Activity design, age-tier sequencing | M.Ed. or equivalent + 3+ yrs early-childhood teaching | TODO — populate before submission |
| STEM Track Author (7–10) | Block-coding → Python → robotics pathway | B.Sc. CS + teaching certification | TODO |
| Language Specialist (×8 locales) | Native-speaker review of translated content | Native speaker + linguistics or TESOL credential | TODO — currently machine-translated via Groq (`lang/_meta/{locale}.json` flags `needs_human_review: true` for ur + ar) |
| Pedagogy Reviewer | Japanese / Chinese / Scandinavian blending | M.Ed. with cross-cultural emphasis | TODO |
| IQ + personality assessment author | Age 9–10 career-guidance battery | M.A. or Ph.D. in educational / developmental psychology | TODO |

## Content moderation

- Every AI-generated script passes `App\Services\ContentSafetyService`
  (static blocklist + Groq classifier fallback) before publication.
- Admin moderation queue at `/admin/content-review` requires `Admin`
  role; every decision logged to `audit_log_entries`.
- COPPA-relevant content additionally reviewed against the COPPA
  compliance memo (`coppa-compliance-memo.md`).

## Action items before Cognia / BAC submission

1. Identify and contract named individuals for each role above.
2. Collect CVs / credential certifications into this folder
   (`docs/accreditation/credentials/`).
3. Update this table with names, qualifications, and date of contract.
4. Provide a short statement from each named author affirming oversight
   of the AI-generated content in their domain.
