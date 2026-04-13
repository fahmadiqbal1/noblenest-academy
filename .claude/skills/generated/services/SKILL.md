---
name: services
description: "Skill for the Services area of Noble Nest Academy. 92 symbols across 26 files."
---

# Services

92 symbols | 26 files | Cohesion: 81%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how AIProviderGateway, AIAssistantService, verify work
- Modifying services-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Services/AIProviderGateway.php` | verify, verifyViaChatCompletion, verifyViaAnthropic, verifyViaGemini, defaultModelFor (+16) |
| `noblenest-academy/app/Services/AIAssistantService.php` | chat, buildPrompt, filterContent, generateSuggestions, mockResponse (+6) |
| `noblenest-academy/app/Services/ShareCardService.php` | generateActivityCard, generateBadgeCard, generateStreakCard, createCanvas, drawBrandStrip (+4) |
| `noblenest-academy/app/Services/AnimationPipelineService.php` | generateStepIllustration, generateStepNarration, processStep, processActivityStep, buildImagePrompt (+1) |
| `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | storeProvider, verifyProvider, syncProviderStatus, scanCurriculum |
| `noblenest-academy/app/Services/DailyCoService.php` | isConfigured, createRoom, deleteRoom, createMeetingToken |
| `noblenest-academy/app/Http/Controllers/Teacher/SessionController.php` | cancel, start, end, authoriseSession |
| `noblenest-academy/app/Services/VideoGenerationService.php` | generateHeyGenVideo, generateSpeech, ttsViaOpenAI, ttsViaElevenLabs |
| `noblenest-academy/app/Services/LearningPathService.php` | buildDailyPath, subjectGaps, progressSummary, ageTier |
| `noblenest-academy/app/Services/MilestoneService.php` | evaluate, hasMet, checkBadges |

## Entry Points

Start here when exploring this area:

- **`AIProviderGateway`** (Class) — `noblenest-academy/app/Services/AIProviderGateway.php:10`
- **`AIAssistantService`** (Class) — `noblenest-academy/app/Services/AIAssistantService.php:14`
- **`verify`** (Method) — `noblenest-academy/app/Services/AIProviderGateway.php:12`
- **`verifyViaChatCompletion`** (Method) — `noblenest-academy/app/Services/AIProviderGateway.php:141`
- **`verifyViaAnthropic`** (Method) — `noblenest-academy/app/Services/AIProviderGateway.php:169`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `AIProviderGateway` | Class | `noblenest-academy/app/Services/AIProviderGateway.php` | 10 |
| `AIAssistantService` | Class | `noblenest-academy/app/Services/AIAssistantService.php` | 14 |
| `verify` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 12 |
| `verifyViaChatCompletion` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 141 |
| `verifyViaAnthropic` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 169 |
| `verifyViaGemini` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 199 |
| `defaultModelFor` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 375 |
| `verifyViaStability` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 393 |
| `verifyViaElevenLabs` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 542 |
| `verifyViaReplicate` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 585 |
| `verifyViaRunway` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 645 |
| `formatHttpError` | Method | `noblenest-academy/app/Services/AIProviderGateway.php` | 702 |
| `storeProvider` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 75 |
| `verifyProvider` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 122 |
| `syncProviderStatus` | Method | `noblenest-academy/app/Http/Controllers/Admin/OrchestratorController.php` | 455 |
| `generateActivityCard` | Method | `noblenest-academy/app/Services/ShareCardService.php` | 40 |
| `generateBadgeCard` | Method | `noblenest-academy/app/Services/ShareCardService.php` | 59 |
| `generateStreakCard` | Method | `noblenest-academy/app/Services/ShareCardService.php` | 77 |
| `createCanvas` | Method | `noblenest-academy/app/Services/ShareCardService.php` | 96 |
| `drawBrandStrip` | Method | `noblenest-academy/app/Services/ShareCardService.php` | 114 |

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
| Security | 14 calls |
| Feature | 7 calls |
| Models | 4 calls |
| Admin | 1 calls |

## How to Explore

1. `gitnexus_context({name: "AIProviderGateway"})` — see callers and callees
2. `gitnexus_query({query: "services"})` — find related execution flows
3. Read key files listed above for implementation details
