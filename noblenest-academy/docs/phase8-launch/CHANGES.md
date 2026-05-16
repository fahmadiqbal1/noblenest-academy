<!-- markdownlint-disable MD013 -->
# Phase 8 — Final Polish, Accreditation Pack, Soft Launch (scaffold)

**Status:** cookie banner landed; accreditation pack, analytics integration, soak test are follow-ups.

## Done in this scaffold

- [x] **EU cookie consent banner** — [components/app/cookie-banner.blade.php](../../resources/views/components/app/cookie-banner.blade.php). Three opt-ins (performance / analytics / marketing), each defaulting OFF. Persists choices to a 365-day `nn-cookie-consent` cookie. "Essential only" + "Customise" + "Accept all" buttons. Rendered globally via `layouts/partials/scripts.blade.php` so every public page surfaces it on first visit.

## Phase 8 follow-ups (for the next commit on this branch)

| # | Work | Notes |
|---|---|---|
| 1 | `docs/accreditation/` pack | Syllabi per age tier, learning outcomes, assessment rubrics, cultural-sensitivity review notes, COPPA + GDPR + UK AADC compliance memos. Required for Cognia / BAC submission. |
| 2 | Plausible / Posthog integration | Gated by the cookie banner's `analytics` opt-in. |
| 3 | `docs/soft-launch-playbook.md` | Who watches what, on-call rotation, rollback procedure. |
| 4 | Statuspage / healthchecks.io heartbeat in the daily Horizon job. |
| 5 | 24-hour synthetic soak test (parent + child loops every 5 min). |
| 6 | Marketing pages `/about`, `/contact`, `/blog` (Markdown-rendered). |
