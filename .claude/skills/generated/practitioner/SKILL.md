---
name: practitioner
description: "Skill for the Practitioner area of Noble Nest Academy. 7 symbols across 3 files."
---

# Practitioner

7 symbols | 3 files | Cohesion: 100%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how practitionerProfile, hasPractitionerProfile, user work
- Modifying practitioner-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Http/Controllers/Practitioner/ProfileController.php` | setup, storeSetup, edit, update |
| `noblenest-academy/app/Models/User.php` | practitionerProfile, hasPractitionerProfile |
| `noblenest-academy/app/Models/PractitionerProfile.php` | user |

## Entry Points

Start here when exploring this area:

- **`practitionerProfile`** (Method) — `noblenest-academy/app/Models/User.php:176`
- **`hasPractitionerProfile`** (Method) — `noblenest-academy/app/Models/User.php:181`
- **`user`** (Method) — `noblenest-academy/app/Models/PractitionerProfile.php:54`
- **`setup`** (Method) — `noblenest-academy/app/Http/Controllers/Practitioner/ProfileController.php:11`
- **`storeSetup`** (Method) — `noblenest-academy/app/Http/Controllers/Practitioner/ProfileController.php:20`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `practitionerProfile` | Method | `noblenest-academy/app/Models/User.php` | 176 |
| `hasPractitionerProfile` | Method | `noblenest-academy/app/Models/User.php` | 181 |
| `user` | Method | `noblenest-academy/app/Models/PractitionerProfile.php` | 54 |
| `setup` | Method | `noblenest-academy/app/Http/Controllers/Practitioner/ProfileController.php` | 11 |
| `storeSetup` | Method | `noblenest-academy/app/Http/Controllers/Practitioner/ProfileController.php` | 20 |
| `edit` | Method | `noblenest-academy/app/Http/Controllers/Practitioner/ProfileController.php` | 53 |
| `update` | Method | `noblenest-academy/app/Http/Controllers/Practitioner/ProfileController.php` | 60 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Setup → PractitionerProfile` | intra_community | 3 |

## How to Explore

1. `gitnexus_context({name: "practitionerProfile"})` — see callers and callees
2. `gitnexus_query({query: "practitioner"})` — find related execution flows
3. Read key files listed above for implementation details
