---
name: seeders
description: "Skill for the Seeders area of Noble Nest Academy. 13 symbols across 7 files."
---

# Seeders

13 symbols | 7 files | Cohesion: 100%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how run, steps, generateAnimations work
- Modifying seeders-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/database/seeders/CurriculumSeeder.php` | guessSubject, guessTypes, run |
| `noblenest-academy/database/seeders/ActivityStepSeeder.php` | getSubjectSteps, parseInstructions, run |
| `noblenest-academy/database/seeders/PreschoolActivitySeeder.php` | run, activities |
| `noblenest-academy/database/seeders/BabyActivitySeeder.php` | run, activities |
| `noblenest-academy/database/seeders/MaternalContentSeeder.php` | run |
| `noblenest-academy/app/Models/MaternalContent.php` | steps |
| `noblenest-academy/app/Http/Controllers/Admin/MaternalContentController.php` | generateAnimations |

## Entry Points

Start here when exploring this area:

- **`run`** (Method) — `noblenest-academy/database/seeders/MaternalContentSeeder.php:15`
- **`steps`** (Method) — `noblenest-academy/app/Models/MaternalContent.php:74`
- **`generateAnimations`** (Method) — `noblenest-academy/app/Http/Controllers/Admin/MaternalContentController.php:137`
- **`guessSubject`** (Method) — `noblenest-academy/database/seeders/CurriculumSeeder.php:672`
- **`guessTypes`** (Method) — `noblenest-academy/database/seeders/CurriculumSeeder.php:725`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `run` | Method | `noblenest-academy/database/seeders/MaternalContentSeeder.php` | 15 |
| `steps` | Method | `noblenest-academy/app/Models/MaternalContent.php` | 74 |
| `generateAnimations` | Method | `noblenest-academy/app/Http/Controllers/Admin/MaternalContentController.php` | 137 |
| `guessSubject` | Method | `noblenest-academy/database/seeders/CurriculumSeeder.php` | 672 |
| `guessTypes` | Method | `noblenest-academy/database/seeders/CurriculumSeeder.php` | 725 |
| `run` | Method | `noblenest-academy/database/seeders/CurriculumSeeder.php` | 736 |
| `getSubjectSteps` | Method | `noblenest-academy/database/seeders/ActivityStepSeeder.php` | 40 |
| `parseInstructions` | Method | `noblenest-academy/database/seeders/ActivityStepSeeder.php` | 311 |
| `run` | Method | `noblenest-academy/database/seeders/ActivityStepSeeder.php` | 361 |
| `run` | Method | `noblenest-academy/database/seeders/PreschoolActivitySeeder.php` | 16 |
| `activities` | Method | `noblenest-academy/database/seeders/PreschoolActivitySeeder.php` | 28 |
| `run` | Method | `noblenest-academy/database/seeders/BabyActivitySeeder.php` | 24 |
| `activities` | Method | `noblenest-academy/database/seeders/BabyActivitySeeder.php` | 36 |

## How to Explore

1. `gitnexus_context({name: "run"})` — see callers and callees
2. `gitnexus_query({query: "seeders"})` — find related execution flows
3. Read key files listed above for implementation details
