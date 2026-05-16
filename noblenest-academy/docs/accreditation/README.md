<!-- markdownlint-disable MD013 -->
# Accreditation pack — Noble Nest Global Academy

This directory holds the artefacts submitted to accreditation bodies (Cognia, British Accreditation Council, regional education ministries) and the evidence that the platform meets COPPA / GDPR-K / UK Age-Appropriate-Design-Code requirements.

## Index

| Document | Purpose | Audience |
|---|---|---|
| [`syllabus-0-6.md`](syllabus-0-6.md) | Early-years curriculum map (motor, language, cognitive, social-emotional, creative, cultural) | Cognia / BAC |
| [`syllabus-7-10.md`](syllabus-7-10.md) | STEM track outline (Blockly, Python, AI literacy) + IQ + personality battery framing | Cognia / BAC |
| [`learning-outcomes.md`](learning-outcomes.md) | Per-age-tier outcomes mapped to EYFS + Reggio + Montessori frameworks | All |
| [`assessment-rubrics.md`](assessment-rubrics.md) | How activity completion + step coverage + the discovery battery are scored | Cognia / BAC |
| [`cultural-sensitivity-review.md`](cultural-sensitivity-review.md) | External advisor sign-off on Japanese / Chinese / Scandinavian / Islamic modules | Cognia / BAC |
| [`coppa-compliance-memo.md`](coppa-compliance-memo.md) | What we collect under 13, parental consent flow, retention policy | FTC / privacy auditor |
| [`gdpr-compliance-memo.md`](gdpr-compliance-memo.md) | DPO appointment, data-export + delete flow, lawful basis, retention | EU DPA |
| [`uk-aadc-compliance-memo.md`](uk-aadc-compliance-memo.md) | UK Age-Appropriate-Design-Code 15-standard self-assessment | UK ICO |

## Submission status

| Body | Status | Owner | Target |
|---|---|---|---|
| Cognia | not yet submitted | curriculum lead | Q3 2026 |
| British Accreditation Council | not yet submitted | curriculum lead | Q3 2026 |
| Pakistan Ministry of Federal Education | not yet contacted | regional partnerships | Q4 2026 |
| UK ICO (AADC self-assessment) | self-cert in progress | legal | before launch |
| EU DPA (lead supervisory authority TBD) | record of processing maintained | DPO | before launch |

## Drafting templates

Each compliance memo follows the same structure:

```
# <Framework name> — compliance memo

## 1. Scope + applicability
…

## 2. Data we collect (and don't)
…

## 3. Lawful basis / parental consent / record-keeping
…

## 4. Retention + deletion
…

## 5. User rights + how we honour them
…

## 6. Risk register + mitigations
…
```

The COPPA memo is the most-developed today (matches the under-13 Parental Consent gate shipped in Phase 5). The others are placeholders that the legal team fills in pre-launch.

## What lives elsewhere

- The actual consent capture lives in `database/migrations/2026_05_16_143455_add_parental_consent_to_child_profiles.php` + `App\Http\Middleware\RequireParentalConsent` + `App\Http\Controllers\PrivacyController::recordParentalConsent`.
- The data-export + delete flow lives in `App\Http\Controllers\PrivacyController::{exportData, deleteData}`.
- The curriculum coverage matrix lives at `docs/phase3-launch/curriculum-coverage.md`.
- The privacy-facing user view is `resources/views/privacy/dashboard.blade.php`.

## Next steps

1. Fill the empty memo files with the project's actual policy language.
2. Get cultural-sensitivity sign-off from one external reviewer per culture (Japanese, Chinese, Scandinavian, Islamic).
3. Engage Cognia + BAC for pre-submission consultation.
4. Print a board-ready 8-page executive summary (~10 % of this directory's word count).
