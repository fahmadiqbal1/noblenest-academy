# Accreditation Submission Checklist

**Targets:** Cognia · BAC (British Accreditation Council)
**Status:** v1-ready scaffolding; named-individual placeholders need operator sign-off before submission.

## Documents in this folder

- [x] `README.md` — folder overview.
- [x] `syllabus-0-6.md` — six-stage early-years curriculum.
- [x] `syllabus-7-10.md` — STEM track (block coding → Python → robotics → AI/data literacy) + IQ/personality assessment.
- [x] `learning-outcomes.md` — written outcomes per age tier.
- [x] `assessment-rubrics.md` — rubric per assessment battery.
- [x] `instructor-credentials.md` — author/reviewer roles + credential requirements (named individuals: TODO).
- [x] `coppa-compliance-memo.md` — Children's Online Privacy Protection Act.
- [x] `gdpr-compliance-memo.md` — General Data Protection Regulation.
- [x] `uk-aadc-compliance-memo.md` — UK Age-Appropriate Design Code (Children's Code).
- [x] `cultural-sensitivity-review.md` — Japanese / Chinese / Scandinavian pedagogical blending review.
- [x] `data-protection-impact-assessment.md` — DPIA covering processing, risks, controls, sign-off table.

## Generated artifacts

- [x] `php artisan accreditation:build` produces a zip under `storage/app/accreditation/noblenest-accreditation-{ts}.zip` containing every doc above + `MANIFEST.json` + `learning-outcomes.generated.csv`.

## Operator action items before submission

1. Populate `instructor-credentials.md` with named individuals + credentials.
2. Sign the DPIA sign-off table.
3. Appoint a Data Protection Officer and add their details to the DPIA + GDPR memo.
4. Provide a brief from each named curriculum author affirming oversight of AI-generated content in their domain.
5. (Optional) PDF-convert the markdowns via pandoc: `pandoc -o syllabus-0-6.pdf syllabus-0-6.md` (Phase 10.1 will integrate dompdf into the artisan command).
6. Run `php artisan accreditation:build` once more after step 1–4 to refresh the zip.
7. Upload via Cognia / BAC submission portal; cite this checklist in the cover letter.

## Compliance evidence (auto-collected at audit time)

- `consent_receipts` table — every parent's recorded COPPA consent with timestamp, IP, UA, document version.
- `audit_log_entries` table — every export, deletion, content_safety_block.
- `lang/_meta/{locale}.json` — machine-translation provenance + `needs_human_review` flag (auditors care about ur+ar).
- `php artisan content:backfill-media` cost-cap log — auditable evidence of AI spend control.

## Reviewer notes

- Children aged 0–10 only; no adult or maternal content (Phase 1 scope reduction removed those verticals — see `docs/AUDIT.md` for the deletion record).
- 8 locales with Urdu + Arabic full RTL; locales beyond the spec's 8 are out of scope.
- Real-time chat with humans is not implemented; the AI assistant operates with a PII-scrubbed prompt and is reviewed via the content-safety pipeline.
