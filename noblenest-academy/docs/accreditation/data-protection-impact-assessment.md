# Data Protection Impact Assessment (DPIA)

**Subject:** Noble Nest Academy v1 — children's LMS, ages 0–10.
**Date:** 2026-05-17 (Phase 10).
**Applicable frameworks:** GDPR (EU), GDPR-K (children's data), COPPA (USA), UK AADC.

## 1. Processing description

| | Detail |
|---|---|
| Personal data collected | Parent: name, email, hashed password, country, preferred language, ip, user-agent (for consent receipts only). Child: name, nickname, date of birth, gender (optional), preferred language, parent-recorded learning preferences, activity completion records, mastery scores. |
| Special-category data | None. No biometrics (PronunciationPlayer scores are computed client-side and discarded). No health data. |
| Lawful basis | GDPR Art 6(1)(a) Consent (parent on behalf of child, verified per COPPA Art 312.5). |
| Children's data basis | GDPR Art 8 — verifiable parental consent recorded in `consent_receipts` (document version, IP, UA, timestamp). |
| Data subjects | Parents, children, optional school administrators. |
| Retention | Active subscriptions: indefinite. Cancelled accounts: soft-delete + scheduled hard-delete after 30 days (`HardDeleteParentDataJob`). |
| Cross-border transfers | Stripe (US), PayPal (US), Groq (US), HeyGen / Synthesia (US), OpenAI Whisper (US), Hostinger (EU). All processors covered by Standard Contractual Clauses. |

## 2. Necessity & proportionality

- **Data minimisation:** parent provides only `name + email` to register. Child's DOB is required only to compute age tier; nickname optional.
- **Storage limitation:** soft-delete + 30-day hard delete on account closure; daily DB backups retained 30 days.
- **Purpose limitation:** child data is used exclusively for in-platform personalisation (activity recommendation, skill state). Never sold, never used for advertising. AI assistant requests are PII-scrubbed before transmission to Groq.
- **Accuracy:** parents can edit / delete child profiles at any time via `/parent/dashboard`.

## 3. Risk assessment

| Risk | Severity | Likelihood | Mitigation |
|---|---|---|---|
| Unauthorised access to child PII via stolen parent credentials | High | Medium | bcrypt + Argon2id hashed passwords, rate-limited login (5/min), 4-digit parent PIN gating settings/billing/export, session 2h idle timeout, security headers (HSTS preload, CSP, X-Frame-Options DENY). |
| PII leakage to AI provider in assistant suggestions | High | Low | Static + recursive `scrubPII()` in `App\Services\AIAssistantService` strips name/email/phone/address/ip BEFORE the HTTP call. Unit-tested. |
| Cross-site script injection bypassing CSP | Medium | Low | Per-request nonce CSP with `strict-dynamic`; X-Frame-Options DENY; Permissions-Policy locks camera/geolocation. |
| Sub-processor breach (Stripe, Groq, HeyGen) | Medium | Low | SCC clauses; minimum-required-data principle; no child PII shared with payments processor. |
| Data subject request abuse | Low | Low | Parent PIN gate on `/privacy/export` and `/privacy/delete`; rate limit 3/hour; signed URLs with 1-hour TTL on export downloads. |
| Backup tape loss | Medium | Low | DB backups gzipped, stored on the same VPS in v1 (Phase 12 will replicate to Hostinger Object Storage with 30-day retention + weekly off-site copy). |

## 4. Compliance controls (mapping)

| Requirement | Implementation |
|---|---|
| COPPA Art 312.5 verifiable parental consent | `consent_receipts` table records parent_user_id, child_profile_id, document_version, ip, user_agent, signed_at, withdrawn_at. Append-only via `ConsentReceiptPolicy` (delete always denied). |
| GDPR Art 15 right of access | `PrivacyController::exportData` → queued zip job → signed URL email. |
| GDPR Art 17 right to erasure | `PrivacyController::deleteData` → soft delete + 30-day hard delete + audit log. |
| GDPR Art 20 data portability | Export is machine-readable JSON+CSV bundle. |
| GDPR Art 8 children's consent | 13+ year cut-off enforced; under-13 uses parent consent (always — v1 audience is 0–10). |
| UK AADC standards 1–15 | High-default privacy settings, no profiling-for-advertising, no nudge techniques, dark-pattern audit checklist in `uk-aadc-compliance-memo.md`. |
| Breach notification (72h) | `AuditLogEntry` action='security_incident' triggers a Sentry alert (Phase 9.1). On-call playbook in `docs/RUNBOOK.md`. |

## 5. Consultation

- **Internal:** lead engineer, content safety lead.
- **External (TODO before launch):** DPO appointment, ICO consultation if processing >25 000 EU children.

## 6. Sign-off

| Role | Name | Date | Signature |
|---|---|---|---|
| Operator / Data Controller | TBC | | |
| DPO (when appointed) | TBC | | |
| Lead Engineer | TBC | | |

## 7. Review

Next review: 6 months after launch, or immediately upon any
sub-processor change, schema change touching child data, or
regulatory update.
