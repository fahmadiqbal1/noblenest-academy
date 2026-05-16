<!-- markdownlint-disable MD013 -->
# UK Age-Appropriate Design Code — Self-Assessment

**Version:** 1.0 · **Date:** 2026-05-16 · **Standard:** ICO Children's Code (AADC), 15 Standards

Noble Nest Academy is an online educational service directed at children under 18. This self-assessment maps the platform against each of the 15 AADC standards.

## Self-Assessment Matrix

| # | Standard | Status | Evidence / Notes |
|---|---|---|---|
| 1 | **Best interests of the child** | Compliant | Privacy-by-default. No advertising. No dark patterns. Product decisions reviewed against child-wellbeing criteria. |
| 2 | **Data protection impact assessments** | In progress | DPIA drafted; pending legal sign-off (Q2 2026). |
| 3 | **Age-appropriate application** | Compliant | Age-band segmentation: Infant / Toddler / Pre-K / Early Primary / Upper Primary. UI adapts per band. |
| 4 | **Transparency** | Compliant | Child-friendly privacy notice on parent dashboard. Plain-language explanations on all consent flows. |
| 5 | **Detrimental use of data** | Compliant | No profiling for advertising. No selling of data. Analytics (Plausible) is aggregate-only, cookieless-capable. |
| 6 | **Policies and community standards** | In progress | Community guidelines being drafted for the practitioner portal (Q2 2026). |
| 7 | **Default settings** | Compliant | All optional features default OFF. Analytics cookie defaults OFF. Marketing email defaults OFF. |
| 8 | **Data minimisation** | Compliant | Only name, age-band, email (parent) collected. No DOB stored in full. No location data. |
| 9 | **Data sharing** | Compliant | No data shared with third parties for commercial purposes. Sub-processors listed in GDPR memo. |
| 10 | **Geolocation** | Compliant | Geolocation not used. Location not collected. |
| 11 | **Parental controls** | Compliant | Parent dashboard controls child profile. Parental consent flow: `RequireParentalConsent` middleware + `privacy/parental-consent` view. |
| 12 | **Profiling** | Partial | Assessment battery generates a learning profile. Displayed only to the parent. Not shared or used for targeting. Consent obtained at assessment start. |
| 13 | **Nudge techniques** | Compliant | No streaks, social pressure mechanics, or dark patterns. Progress is encouraging, not compulsive. |
| 14 | **Connected toys and devices** | N/A | No IoT or hardware component. |
| 15 | **Online tools** | Compliant | All interactive tools (canvas, code-blocks) are educational; no user-generated content is public. |

## Outstanding Actions

| Action | Owner | Target |
|---|---|---|
| DPIA legal sign-off | Legal | Q2 2026 |
| Community standards document | Content lead | Q2 2026 |
| Profiling consent review by legal | Legal | Q3 2026 |
| ICO notification (if required) | Legal | Before UK user intake |

## Contact

For AADC queries: hello@noblenest.example (mark subject "ICO / AADC")
