<!-- markdownlint-disable MD013 -->
# GDPR Compliance Memo — Noble Nest Academy

**Version:** 1.0 · **Date:** 2026-05-16 · **Audience:** EU DPA / privacy auditor

## 1. Controller Details

| Field | Value |
|---|---|
| Controller name | Noble Nest Global Academy Ltd (to be incorporated) |
| Registered address | TBD — UK / Pakistan dual presence |
| DPO | Not yet formally appointed; privacy queries → hello@noblenest.example |
| Lead supervisory authority | TBD — pending formal establishment |

## 2. Lawful Basis

| Processing activity | Lawful basis | Notes |
|---|---|---|
| Account registration (parent) | Art. 6(1)(b) — contract | Necessary to provide the service |
| Child profile creation | Art. 6(1)(b) + Art. 8 (parental consent) | Parent must consent on behalf of child <16 |
| Activity completion logging | Art. 6(1)(b) | Core service delivery |
| Payment processing (Stripe) | Art. 6(1)(b) | Stripe is a sub-processor; DPA in place |
| Email communications (Resend) | Art. 6(1)(a) — consent or Art. 6(1)(b) | Transactional email = contract; marketing = consent |
| Analytics (Plausible) | Art. 6(1)(f) — legitimate interest | Cookie-banner opt-in gate; cookieless Plausible configured |
| Sentry error tracking | Art. 6(1)(f) — legitimate interest | PII scrubbing configured; no user content in payloads |

## 3. Data Collected

### 3a. Parents / Guardians
- Name, email, hashed password.
- Subscription tier and Stripe customer ID (no card data — Stripe holds it).
- Consent timestamps (`parental_consent_given_at` column on `child_profiles`).

### 3b. Children
- Display name and age-band (no full DOB stored — only year).
- Activity completion records and assessment scores.
- Milestone badges.
- **No location data. No device identifiers. No behavioural profiling for advertising.**

## 4. Retention Policy

| Data category | Retention period | Deletion mechanism |
|---|---|---|
| Active account data | Duration of subscription + 30 days | `php artisan user:purge --days=30` (to be built) |
| Cancelled subscriptions | 90 days | Scheduled Horizon job |
| Payment records | 7 years | Legal obligation (tax records) |
| Error logs (Sentry) | 30 days | Sentry project retention setting |
| Analytics (Plausible) | Rolling 24 months | Plausible dashboard setting |

## 5. Data Subject Rights

| Right | Mechanism | Status |
|---|---|---|
| Access | `GET /privacy/dashboard` — shows all stored data | Implemented |
| Rectification | Profile edit pages | Implemented |
| Erasure | `DELETE /privacy/account` — queues full delete | To be implemented (Q2 2026) |
| Portability | JSON export from privacy dashboard | To be implemented (Q2 2026) |
| Restriction / Objection | Email request → manual process | Interim only |

## 6. Sub-Processors

| Sub-processor | Purpose | Location | DPA in place |
|---|---|---|---|
| Stripe | Payment processing | USA (SCCs apply) | Yes |
| Resend | Transactional email | USA (SCCs apply) | Yes |
| Sentry | Error monitoring | USA (SCCs apply) | Yes |
| Plausible | Analytics | EU (Germany/Lithuania) | Yes |
| Hostinger KVM4 | Hosting | EU | Yes (DPA in Terms) |

## 7. International Transfers

Stripe, Resend, and Sentry are US-based. Transfers rely on Standard Contractual Clauses (SCCs, 2021 EU Commission version). DPA supplements are in place for all three.

## 8. Cookie Policy

EU-compliant cookie banner implemented (Phase 8 — `components/app/cookie-banner.blade.php`):
- **Essential cookies only** on first load; no analytics or marketing cookies set without consent.
- Consent stored in `nn-cookie-consent` cookie (365-day expiry, user-controlled).
- Plausible analytics only loads after `analytics` opt-in is recorded in the consent cookie.

## 9. Open Actions

| Action | Owner | Target |
|---|---|---|
| Formally appoint DPO | Legal | Before launch |
| Complete Art. 30 Record of Processing Activities | Legal | Before launch |
| Implement account-deletion endpoint | Engineering | Q2 2026 |
| Implement data-export endpoint | Engineering | Q2 2026 |
| Confirm lead SA with EU DPA | Legal | Post-EU user intake |
