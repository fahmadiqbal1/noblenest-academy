---
name: security
description: "Skill for the Security area of Noble Nest Academy. 24 symbols across 8 files."
---

# Security

24 symbols | 8 files | Cohesion: 57%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how parent_can_view_child_dashboard, test_assistant_rate_limited, generateImage work
- Modifying security-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | test_admin_routes_require_admin_role, test_admin_can_access_admin_routes, test_teacher_routes_require_teacher_role, test_teacher_can_access_teacher_routes, test_subscription_routes_require_active_subscription (+4) |
| `noblenest-academy/app/Services/AIProviderGateway.php` | generateImage, generateImageViaGemini, callGeminiImageModel, generateImageViaStability, generateImageViaOpenAI |
| `noblenest-academy/tests/Feature/Security/PaymentWebhookSecurityTest.php` | test_webhook_without_signature_rejected, test_webhook_with_invalid_signature_rejected, test_malformed_payload_rejected, test_old_timestamp_signature_handling |
| `noblenest-academy/tests/Feature/Security/LanguageSwitchSecurityTest.php` | test_invalid_language_codes_rejected, test_xss_in_language_rejected |
| `noblenest-academy/scripts/visual-smoke.mjs` | main |
| `noblenest-academy/tests/Feature/ParentChildFlowTest.php` | parent_can_view_child_dashboard |
| `noblenest-academy/tests/Feature/AIAssistantTest.php` | test_assistant_rate_limited |
| `noblenest-academy/app/Http/Controllers/ChatController.php` | status |

## Entry Points

Start here when exploring this area:

- **`parent_can_view_child_dashboard`** (Method) — `noblenest-academy/tests/Feature/ParentChildFlowTest.php:176`
- **`test_assistant_rate_limited`** (Method) — `noblenest-academy/tests/Feature/AIAssistantTest.php:73`
- **`generateImage`** (Method) — `noblenest-academy/app/Services/AIProviderGateway.php:404`
- **`generateImageViaGemini`** (Method) — `noblenest-academy/app/Services/AIProviderGateway.php:417`
- **`callGeminiImageModel`** (Method) — `noblenest-academy/app/Services/AIProviderGateway.php:437`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `parent_can_view_child_dashboard` | Method | `noblenest-academy/tests/Feature/ParentChildFlowTest.php` | 176 |
| `test_assistant_rate_limited` | Method | `noblenest-academy/tests/Feature/AIAssistantTest.php` | 73 |
| `generateImage` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 404 |
| `generateImageViaGemini` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 417 |
| `callGeminiImageModel` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 437 |
| `generateImageViaStability` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 502 |
| `generateImageViaOpenAI` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 522 |
| `test_webhook_without_signature_rejected` | Method | `noblenest-academy/tests/Feature/Security/PaymentWebhookSecurityTest.php` | 19 |
| `test_webhook_with_invalid_signature_rejected` | Method | `noblenest-academy/tests/Feature/Security/PaymentWebhookSecurityTest.php` | 42 |
| `test_malformed_payload_rejected` | Method | `noblenest-academy/tests/Feature/Security/PaymentWebhookSecurityTest.php` | 67 |
| `test_old_timestamp_signature_handling` | Method | `noblenest-academy/tests/Feature/Security/PaymentWebhookSecurityTest.php` | 82 |
| `test_invalid_language_codes_rejected` | Method | `noblenest-academy/tests/Feature/Security/LanguageSwitchSecurityTest.php` | 39 |
| `test_xss_in_language_rejected` | Method | `noblenest-academy/tests/Feature/Security/LanguageSwitchSecurityTest.php` | 55 |
| `test_admin_routes_require_admin_role` | Method | `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | 19 |
| `test_admin_can_access_admin_routes` | Method | `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | 43 |
| `test_teacher_routes_require_teacher_role` | Method | `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | 56 |
| `test_teacher_can_access_teacher_routes` | Method | `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | 69 |
| `test_subscription_routes_require_active_subscription` | Method | `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | 81 |
| `test_active_subscription_grants_access` | Method | `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | 94 |
| `test_expired_subscription_denied_access` | Method | `noblenest-academy/tests/Feature/Security/AccessControlTest.php` | 118 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `DispatchJob → GetProvider` | cross_community | 8 |
| `Handle → GetProvider` | cross_community | 8 |
| `Handle → GetProvider` | cross_community | 8 |
| `RetryJob → GetProvider` | cross_community | 8 |
| `Handle → GetProvider` | cross_community | 7 |
| `Message → GetProvider` | cross_community | 7 |
| `DispatchJob → FormatHttpError` | cross_community | 7 |
| `Handle → GetProvider` | cross_community | 7 |
| `StoreProvider → GetProvider` | cross_community | 7 |
| `VerifyProvider → GetProvider` | cross_community | 7 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 12 calls |
| Services | 6 calls |

## How to Explore

1. `gitnexus_context({name: "parent_can_view_child_dashboard"})` — see callers and callees
2. `gitnexus_query({query: "security"})` — find related execution flows
3. Read key files listed above for implementation details
