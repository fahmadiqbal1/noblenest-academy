---
name: admin
description: "Skill for the Admin area of Noble Nest Academy. 32 symbols across 13 files."
---

# Admin

32 symbols | 13 files | Cohesion: 83%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how applySafetyFilter, getUnsafeContentIds, all work
- Modifying admin-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | dispatchJob, retryJob, runJob, callProvider, extractFromGithub (+3) |
| `noblenest-academy/app/Http/Controllers/Admin/AnalyticsController.php` | index, reportEmail, monthlyCompletions, buildData, queryMonthlyCompletions |
| `noblenest-academy/app/Http/Controllers/Admin/ModuleController.php` | create, edit, update, destroy |
| `noblenest-academy/app/Http/Controllers/Admin/QuestionController.php` | create, store, update |
| `noblenest-academy/scripts/full-smoke-test.mjs` | main, test |
| `noblenest-academy/app/Services/MaternalContentFilterService.php` | applySafetyFilter, getUnsafeContentIds |
| `noblenest-academy/app/Http/Controllers/Admin/QuizController.php` | create, edit |
| `noblenest-academy/app/Helpers/I18n.php` | all |
| `noblenest-academy/app/Services/AIProviderGateway.php` | generateAudio |
| `noblenest-academy/app/Jobs/GenerateActivityMediaJob.php` | generateAudio |

## Entry Points

Start here when exploring this area:

- **`applySafetyFilter`** (Method) — `noblenest-academy/app/Services/MaternalContentFilterService.php:28`
- **`getUnsafeContentIds`** (Method) — `noblenest-academy/app/Services/MaternalContentFilterService.php:54`
- **`all`** (Method) — `noblenest-academy/app/Helpers/I18n.php:206`
- **`create`** (Method) — `noblenest-academy/app/Http/Controllers/Admin/QuizController.php:16`
- **`edit`** (Method) — `noblenest-academy/app/Http/Controllers/Admin/QuizController.php:33`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `applySafetyFilter` | Method | `noblenest-academy/app/Services/MaternalContentFilterService.php` | 28 |
| `getUnsafeContentIds` | Method | `noblenest-academy/app/Services/MaternalContentFilterService.php` | 54 |
| `all` | Method | `noblenest-academy/app/Helpers/I18n.php` | 206 |
| `create` | Method | `noblenest-academy/app/Http/Controllers/Admin/QuizController.php` | 16 |
| `edit` | Method | `noblenest-academy/app/Http/Controllers/Admin/QuizController.php` | 33 |
| `create` | Method | `noblenest-academy/app/Http/Controllers/Admin/ModuleController.php` | 17 |
| `edit` | Method | `noblenest-academy/app/Http/Controllers/Admin/ModuleController.php` | 41 |
| `generateAudio` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 554 |
| `generateAudio` | Method | `noblenest-academy/app/Jobs/GenerateActivityMediaJob.php` | 117 |
| `dispatchJob` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 141 |
| `retryJob` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 192 |
| `runJob` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 282 |
| `callProvider` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 303 |
| `extractFromGithub` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 350 |
| `mockGenerate` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 376 |
| `questions` | Method | `noblenest-academy/app/Models/Quiz.php` | 13 |
| `options` | Method | `noblenest-academy/app/Models/Question.php` | 13 |
| `create` | Method | `noblenest-academy/app/Http/Controllers/Admin/QuestionController.php` | 11 |
| `store` | Method | `noblenest-academy/app/Http/Controllers/Admin/QuestionController.php` | 16 |
| `update` | Method | `noblenest-academy/app/Http/Controllers/Admin/QuestionController.php` | 44 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `DispatchJob → GetProvider` | cross_community | 8 |
| `RetryJob → GetProvider` | cross_community | 8 |
| `DispatchJob → LoadFromFile` | cross_community | 7 |
| `DispatchJob → FormatHttpError` | cross_community | 7 |
| `RetryJob → LoadFromFile` | cross_community | 7 |
| `RetryJob → FormatHttpError` | cross_community | 7 |
| `CallProvider → GetProvider` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Security | 3 calls |
| Services | 3 calls |
| Controllers | 3 calls |
| Feature | 1 calls |

## How to Explore

1. `gitnexus_context({name: "applySafetyFilter"})` — see callers and callees
2. `gitnexus_query({query: "admin"})` — find related execution flows
3. Read key files listed above for implementation details
