---
name: controllers
description: "Skill for the Controllers area of Noble Nest Academy. 95 symbols across 72 files."
---

# Controllers

95 symbols | 72 files | Cohesion: 96%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how ShareCardController, SettingsController, SchoolInquiryController work
- Modifying controllers-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Http/Controllers/PaymentController.php` | PaymentController, paymentSuccess, resolvePlan, stripeCheckout, stripeWebhook (+2) |
| `noblenest-academy/app/Helpers/I18n.php` | currentLanguage, isRtl, direction, loadTranslations, loadFromFile (+2) |
| `noblenest-academy/app/Http/Controllers/ActivityController.php` | ActivityController, saveTrace, saveDrawing, savePuzzleComplete, decodeVerifiedPng (+1) |
| `noblenest-academy/app/Http/Controllers/AuthController.php` | AuthController, register, login, logout |
| `noblenest-academy/app/Http/Controllers/OnboardingController.php` | OnboardingController, storeStep1, store |
| `noblenest-academy/app/Http/Controllers/PrivacyController.php` | PrivacyController, deleteData |
| `noblenest-academy/app/Http/Controllers/ShareCardController.php` | ShareCardController |
| `noblenest-academy/app/Http/Controllers/SettingsController.php` | SettingsController |
| `noblenest-academy/app/Http/Controllers/SchoolInquiryController.php` | SchoolInquiryController |
| `noblenest-academy/app/Http/Controllers/ReferralController.php` | ReferralController |

## Entry Points

Start here when exploring this area:

- **`ShareCardController`** (Class) — `noblenest-academy/app/Http/Controllers/ShareCardController.php:9`
- **`SettingsController`** (Class) — `noblenest-academy/app/Http/Controllers/SettingsController.php:6`
- **`SchoolInquiryController`** (Class) — `noblenest-academy/app/Http/Controllers/SchoolInquiryController.php:8`
- **`ReferralController`** (Class) — `noblenest-academy/app/Http/Controllers/ReferralController.php:9`
- **`QuizController`** (Class) — `noblenest-academy/app/Http/Controllers/QuizController.php:10`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `ShareCardController` | Class | `noblenest-academy/app/Http/Controllers/ShareCardController.php` | 9 |
| `SettingsController` | Class | `noblenest-academy/app/Http/Controllers/SettingsController.php` | 6 |
| `SchoolInquiryController` | Class | `noblenest-academy/app/Http/Controllers/SchoolInquiryController.php` | 8 |
| `ReferralController` | Class | `noblenest-academy/app/Http/Controllers/ReferralController.php` | 9 |
| `QuizController` | Class | `noblenest-academy/app/Http/Controllers/QuizController.php` | 10 |
| `PrivacyController` | Class | `noblenest-academy/app/Http/Controllers/PrivacyController.php` | 12 |
| `PricingController` | Class | `noblenest-academy/app/Http/Controllers/PricingController.php` | 7 |
| `PaymentController` | Class | `noblenest-academy/app/Http/Controllers/PaymentController.php` | 11 |
| `OnboardingController` | Class | `noblenest-academy/app/Http/Controllers/OnboardingController.php` | 8 |
| `NotificationController` | Class | `noblenest-academy/app/Http/Controllers/NotificationController.php` | 8 |
| `MilestoneWallController` | Class | `noblenest-academy/app/Http/Controllers/MilestoneWallController.php` | 6 |
| `HomeController` | Class | `noblenest-academy/app/Http/Controllers/HomeController.php` | 6 |
| `Controller` | Class | `noblenest-academy/app/Http/Controllers/Controller.php` | 7 |
| `ClassroomController` | Class | `noblenest-academy/app/Http/Controllers/ClassroomController.php` | 23 |
| `ChildActivityController` | Class | `noblenest-academy/app/Http/Controllers/ChildActivityController.php` | 12 |
| `ChatController` | Class | `noblenest-academy/app/Http/Controllers/ChatController.php` | 9 |
| `AuthController` | Class | `noblenest-academy/app/Http/Controllers/AuthController.php` | 11 |
| `AssessmentController` | Class | `noblenest-academy/app/Http/Controllers/AssessmentController.php` | 9 |
| `ActivityController` | Class | `noblenest-academy/app/Http/Controllers/ActivityController.php` | 10 |
| `SessionController` | Class | `noblenest-academy/app/Http/Controllers/Teacher/SessionController.php` | 12 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `DispatchJob → LoadFromFile` | cross_community | 7 |
| `RetryJob → LoadFromFile` | cross_community | 7 |
| `GenerateVideo → LoadFromFile` | cross_community | 7 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `DispatchJob → CurrentLanguage` | cross_community | 6 |

## How to Explore

1. `gitnexus_context({name: "ShareCardController"})` — see callers and callees
2. `gitnexus_query({query: "controllers"})` — find related execution flows
3. Read key files listed above for implementation details
