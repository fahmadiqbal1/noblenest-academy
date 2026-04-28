---
name: jobs
description: "Skill for the Jobs area of Noble Nest Academy. 4 symbols across 2 files."
---

# Jobs

4 symbols | 2 files | Cohesion: 86%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how DailyDigestMail, handle, buildDigest work
- Modifying jobs-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Jobs/SendDailyDigestJob.php` | handle, buildDigest, buildMaternalDigest |
| `noblenest-academy/app/Mail/DailyDigestMail.php` | DailyDigestMail |

## Entry Points

Start here when exploring this area:

- **`DailyDigestMail`** (Class) — `noblenest-academy/app/Mail/DailyDigestMail.php:11`
- **`handle`** (Method) — `noblenest-academy/app/Jobs/SendDailyDigestJob.php:20`
- **`buildDigest`** (Method) — `noblenest-academy/app/Jobs/SendDailyDigestJob.php:35`
- **`buildMaternalDigest`** (Method) — `noblenest-academy/app/Jobs/SendDailyDigestJob.php:76`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `DailyDigestMail` | Class | `noblenest-academy/app/Mail/DailyDigestMail.php` | 11 |
| `handle` | Method | `noblenest-academy/app/Jobs/SendDailyDigestJob.php` | 20 |
| `buildDigest` | Method | `noblenest-academy/app/Jobs/SendDailyDigestJob.php` | 35 |
| `buildMaternalDigest` | Method | `noblenest-academy/app/Jobs/SendDailyDigestJob.php` | 76 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Handle → LoadFromFile` | cross_community | 4 |
| `Handle → CurrentLanguage` | cross_community | 3 |
| `Handle → BuildMaternalDigest` | intra_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Controllers | 1 calls |

## How to Explore

1. `gitnexus_context({name: "DailyDigestMail"})` — see callers and callees
2. `gitnexus_query({query: "jobs"})` — find related execution flows
3. Read key files listed above for implementation details
