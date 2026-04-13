---
name: commands
description: "Skill for the Commands area of Noble Nest Academy. 19 symbols across 2 files."
---

# Commands

19 symbols | 2 files | Cohesion: 57%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how handle, checkOPcache, checkSessionDriver work
- Modifying commands-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | handle, checkOPcache, checkSessionDriver, checkQueueDriver, checkRouteCache (+10) |
| `noblenest-academy/app/Console/Commands/GenerateOGImagesCommand.php` | handle, resolveProvider, resolvePages, buildPrompt |

## Entry Points

Start here when exploring this area:

- **`handle`** (Method) — `noblenest-academy/app/Console/Commands/StressAuditCommand.php:16`
- **`checkOPcache`** (Method) — `noblenest-academy/app/Console/Commands/StressAuditCommand.php:59`
- **`checkSessionDriver`** (Method) — `noblenest-academy/app/Console/Commands/StressAuditCommand.php:95`
- **`checkQueueDriver`** (Method) — `noblenest-academy/app/Console/Commands/StressAuditCommand.php:119`
- **`checkRouteCache`** (Method) — `noblenest-academy/app/Console/Commands/StressAuditCommand.php:144`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `handle` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 16 |
| `checkOPcache` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 59 |
| `checkSessionDriver` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 95 |
| `checkQueueDriver` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 119 |
| `checkRouteCache` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 144 |
| `checkViewCache` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 166 |
| `checkDBPooling` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 207 |
| `printSummary` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 244 |
| `checkPHP` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 47 |
| `checkDatabase` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 73 |
| `checkCacheDriver` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 107 |
| `checkAppEnv` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 131 |
| `checkConfigCache` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 155 |
| `checkWebServer` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 179 |
| `record` | Method | `noblenest-academy/app/Console/Commands/StressAuditCommand.php` | 227 |
| `handle` | Method | `noblenest-academy/app/Console/Commands/GenerateOGImagesCommand.php` | 20 |
| `resolveProvider` | Method | `noblenest-academy/app/Console/Commands/GenerateOGImagesCommand.php` | 63 |
| `resolvePages` | Method | `noblenest-academy/app/Console/Commands/GenerateOGImagesCommand.php` | 76 |
| `buildPrompt` | Method | `noblenest-academy/app/Console/Commands/GenerateOGImagesCommand.php` | 111 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Handle → GetProvider` | cross_community | 7 |
| `Handle → LoadFromFile` | cross_community | 5 |
| `Handle → FormatHttpError` | cross_community | 5 |
| `Handle → CurrentLanguage` | cross_community | 4 |
| `Handle → Record` | cross_community | 3 |
| `Handle → ResolveDriver` | cross_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Security | 1 calls |
| Admin | 1 calls |

## How to Explore

1. `gitnexus_context({name: "handle"})` — see callers and callees
2. `gitnexus_query({query: "commands"})` — find related execution flows
3. Read key files listed above for implementation details
