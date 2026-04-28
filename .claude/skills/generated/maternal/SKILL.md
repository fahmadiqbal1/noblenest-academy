---
name: maternal
description: "Skill for the Maternal area of Noble Nest Academy. 23 symbols across 13 files."
---

# Maternal

23 symbols | 13 files | Cohesion: 89%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how content_filter_service_excludes_contraindicated_items, safeContentQuery, index work
- Modifying maternal-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Http/Controllers/Maternal/ProfileController.php` | edit, update, pause, resume, markLoss |
| `noblenest-academy/app/Http/Controllers/Maternal/ContentController.php` | index, show, start, complete |
| `noblenest-academy/tests/Feature/MaternalWellnessTest.php` | content_filter_service_excludes_contraindicated_items, is_safe_returns_false_for_contraindicated_content |
| `noblenest-academy/app/Services/MaternalContentFilterService.php` | safeContentQuery, isSafe |
| `noblenest-academy/app/Models/MaternalProgress.php` | markStarted, markCompleted |
| `noblenest-academy/app/Http/Controllers/Maternal/TechniqueController.php` | index |
| `noblenest-academy/app/Http/Controllers/Maternal/NewbornController.php` | index |
| `noblenest-academy/app/Http/Controllers/Maternal/JourneyController.php` | week |
| `noblenest-academy/app/Http/Controllers/Maternal/HerbController.php` | index |
| `noblenest-academy/app/Http/Controllers/Maternal/DashboardController.php` | index |

## Entry Points

Start here when exploring this area:

- **`content_filter_service_excludes_contraindicated_items`** (Method) — `noblenest-academy/tests/Feature/MaternalWellnessTest.php:171`
- **`safeContentQuery`** (Method) — `noblenest-academy/app/Services/MaternalContentFilterService.php:16`
- **`index`** (Method) — `noblenest-academy/app/Http/Controllers/Maternal/TechniqueController.php:14`
- **`index`** (Method) — `noblenest-academy/app/Http/Controllers/Maternal/NewbornController.php:13`
- **`week`** (Method) — `noblenest-academy/app/Http/Controllers/Maternal/JourneyController.php:33`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `content_filter_service_excludes_contraindicated_items` | Method | `noblenest-academy/tests/Feature/MaternalWellnessTest.php` | 171 |
| `safeContentQuery` | Method | `noblenest-academy/app/Services/MaternalContentFilterService.php` | 16 |
| `index` | Method | `noblenest-academy/app/Http/Controllers/Maternal/TechniqueController.php` | 14 |
| `index` | Method | `noblenest-academy/app/Http/Controllers/Maternal/NewbornController.php` | 13 |
| `week` | Method | `noblenest-academy/app/Http/Controllers/Maternal/JourneyController.php` | 33 |
| `index` | Method | `noblenest-academy/app/Http/Controllers/Maternal/HerbController.php` | 13 |
| `index` | Method | `noblenest-academy/app/Http/Controllers/Maternal/DashboardController.php` | 16 |
| `index` | Method | `noblenest-academy/app/Http/Controllers/Maternal/ContentController.php` | 15 |
| `index` | Method | `noblenest-academy/app/Http/Controllers/Maternal/BreastfeedingController.php` | 13 |
| `is_safe_returns_false_for_contraindicated_content` | Method | `noblenest-academy/tests/Feature/MaternalWellnessTest.php` | 207 |
| `isSafe` | Method | `noblenest-academy/app/Services/MaternalContentFilterService.php` | 38 |
| `markStarted` | Method | `noblenest-academy/app/Models/MaternalProgress.php` | 63 |
| `markCompleted` | Method | `noblenest-academy/app/Models/MaternalProgress.php` | 70 |
| `show` | Method | `noblenest-academy/app/Http/Controllers/Maternal/ContentController.php` | 41 |
| `start` | Method | `noblenest-academy/app/Http/Controllers/Maternal/ContentController.php` | 64 |
| `complete` | Method | `noblenest-academy/app/Http/Controllers/Maternal/ContentController.php` | 80 |
| `user` | Method | `noblenest-academy/app/Models/MaternalProfile.php` | 147 |
| `handle` | Method | `noblenest-academy/app/Http/Middleware/EnsureMaternalConsent.php` | 11 |
| `edit` | Method | `noblenest-academy/app/Http/Controllers/Maternal/ProfileController.php` | 11 |
| `update` | Method | `noblenest-academy/app/Http/Controllers/Maternal/ProfileController.php` | 19 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Index → LoadFromFile` | cross_community | 6 |
| `Week → LoadFromFile` | cross_community | 6 |
| `Index → CurrentLanguage` | cross_community | 5 |
| `Index → CurrentLanguage` | cross_community | 5 |
| `Index → CurrentLanguage` | cross_community | 5 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 2 calls |
| Admin | 1 calls |

## How to Explore

1. `gitnexus_context({name: "content_filter_service_excludes_contraindicated_items"})` — see callers and callees
2. `gitnexus_query({query: "maternal"})` — find related execution flows
3. Read key files listed above for implementation details
