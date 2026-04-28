---
name: policies
description: "Skill for the Policies area of Noble Nest Academy. 6 symbols across 2 files."
---

# Policies

6 symbols | 2 files | Cohesion: 91%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how viewAny, view, create work
- Modifying policies-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Policies/MaternalContentPolicy.php` | viewAny, view, create, update, delete |
| `noblenest-academy/app/Models/User.php` | isAdmin |

## Entry Points

Start here when exploring this area:

- **`viewAny`** (Method) — `noblenest-academy/app/Policies/MaternalContentPolicy.php:9`
- **`view`** (Method) — `noblenest-academy/app/Policies/MaternalContentPolicy.php:14`
- **`create`** (Method) — `noblenest-academy/app/Policies/MaternalContentPolicy.php:23`
- **`update`** (Method) — `noblenest-academy/app/Policies/MaternalContentPolicy.php:28`
- **`delete`** (Method) — `noblenest-academy/app/Policies/MaternalContentPolicy.php:33`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `viewAny` | Method | `noblenest-academy/app/Policies/MaternalContentPolicy.php` | 9 |
| `view` | Method | `noblenest-academy/app/Policies/MaternalContentPolicy.php` | 14 |
| `create` | Method | `noblenest-academy/app/Policies/MaternalContentPolicy.php` | 23 |
| `update` | Method | `noblenest-academy/app/Policies/MaternalContentPolicy.php` | 28 |
| `delete` | Method | `noblenest-academy/app/Policies/MaternalContentPolicy.php` | 33 |
| `isAdmin` | Method | `noblenest-academy/app/Models/User.php` | 114 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `ViewAny → MaternalProfile` | cross_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Models | 1 calls |

## How to Explore

1. `gitnexus_context({name: "viewAny"})` — see callers and callees
2. `gitnexus_query({query: "policies"})` — find related execution flows
3. Read key files listed above for implementation details
