<!-- markdownlint-disable MD013 -->
# COPPA — compliance memo

**Last reviewed:** 2026-05-16 · **Owner:** Privacy lead · **Status:** active

## 1. Scope + applicability

Noble Nest Academy is directed at children aged 0–10. The Children's Online Privacy Protection Act (COPPA, 15 U.S.C. §§ 6501–6506) and its FTC rule (16 CFR Part 312) apply to every US-resident user under 13 — that's most of our addressable market.

This memo describes how the platform satisfies COPPA's six operator obligations:

1. Post a clear, comprehensive privacy notice
2. Obtain verifiable parental consent before collecting personal information from a child under 13
3. Give parents a way to review their child's data
4. Give parents a way to delete their child's data
5. Maintain reasonable procedures to protect children's data
6. Retain children's data only as long as necessary

## 2. Data we collect (and don't)

**Collected for under-13 child profiles:**

- First name (NOT full date of birth — age band only, derived from `age_min` / `age_max`)
- Activity completions, time on task, badges earned
- Drawing / tracing artwork the parent or child saves to their account
- Quiz answers and discovery-battery responses

**NOT collected:**

- Photos, voice recordings, or video unless the parent uploads them.
- Geolocation more precise than country (derived from IP for pricing-region only; never stored).
- Targeted advertising profiles.
- Behavioural data sold to third parties.

## 3. Lawful basis / parental consent / record-keeping

**Consent mechanism:** the parent's authenticated session on Noble Nest Academy. Before the under-13 child can use any age-gated route (see "Routes protected" below), the parent must complete the consent form at `/privacy/parental-consent/{child}` — a real-time consent flow with three required checkboxes (Terms, Privacy, COPPA acknowledgement).

**Record-keeping:** on each consent, the platform records:

- `parental_consent_at` — UTC timestamp
- `parental_consent_ip` — the parent's IPv4/IPv6 address (max 45 chars; partial truncation for privacy via Sentry config)
- `parental_consent_user_agent` — the parent's browser UA (max 255 chars)

All three columns live on `child_profiles` and are immutable via the public API. They can be revoked by the parent at any time via `PrivacyController::deleteData` (which clears them and removes the child's data per §4).

**Verifiable parental consent (FTC §312.5):** the current flow uses the parent's authenticated session + checkbox + audit record. The FTC explicitly recognises this as a permissible method when the operator's service is restricted to logged-in parents (which Noble Nest is — child profiles can only be created from a parent account).

**Routes protected** (via `parental.consent` middleware, registered in `bootstrap/app.php`):

- `/child/{child}/activities`
- `/child/{child}/activities/{activity}/complete`
- `/child/{child}/dashboard`
- `/child/{child}/assessment`

## 4. Retention + deletion

**Active accounts:** child data is retained as long as the parent maintains the account.

**Inactive accounts:** 24 months of inactivity triggers an email warning; 36 months triggers automatic deletion.

**Parent-requested deletion:** immediate via `/privacy` dashboard → "Delete my child's data". Hard-delete within 24 hours; backups purged within 60 days per backup-rotation schedule.

**Data-export:** parents can download a JSON dump of every row referencing their child via `/privacy/export`.

## 5. Reasonable procedures (security)

- TLS 1.3 in production via Let's Encrypt (`certbot --nginx` per `docs/deploy/hostinger-kvm4.md`).
- Encryption at rest via MySQL's tablespace encryption + ZFS-encrypted backup volumes.
- Application secrets in `.env` (not committed) + rotated via `SecureCredentialsManager::rotate()`.
- `ufw` + `fail2ban` on the VPS (Phase 7 runbook §1).
- Webhook signatures verified (`PaymentController::stripeWebhook` — Phase 4 + the 3 negative-path tests in `tests/Feature/StripeWebhookTest.php`).
- No PII in Sentry breadcrumbs (`config/sentry.php`: `send_default_pii = false`).

## 6. Risk register + mitigations

| Risk | Mitigation |
|---|---|
| Child registers without parental knowledge | Child profiles can only be created via an authenticated parent account; the parental.consent middleware blocks all child-facing routes pre-consent. |
| Consent revoked but data lingers | `PrivacyController::deleteData` hard-deletes child rows + propagates to backup-rotation cron within 60 days. |
| Webhook spoofing / fake payment events | Webhook signature verification mandatory; `STRIPE_WEBHOOK_SECRET` required; unsigned payloads rejected with HTTP 400. |
| AI-generated activity contains inappropriate content for a 4-year-old | `OrchestratorController` requires moderation approval before content goes live; `ContentBatchController::publish` is admin-gated. |
| EU child accidentally treated under US-only COPPA | GDPR-K parallel memo applies; both gates run for EU child profiles (under 16 default; 13 if local DPA permits). |

## 7. Outstanding work before submission

- Engage a COPPA-safe-harbour provider (iKeepSafe, kidSAFE, PRIVO) for periodic audit (~$5–15 k/yr).
- Publish a public Children's Privacy Notice page (currently consolidated into `/privacy`).
- Add an "Inactive account" purge job to Horizon (cron-driven; lands with Phase 7 follow-up CI/CD).
- Translate the parental-consent flow into the 7 non-English supported locales (Phase 3 translation pipeline can handle this once `activity:translate` is generalised).
