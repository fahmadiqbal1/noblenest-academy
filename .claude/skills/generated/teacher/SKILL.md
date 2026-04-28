---
name: teacher
description: "Skill for the Teacher area of Noble Nest Academy. 11 symbols across 2 files."
---

# Teacher

11 symbols | 2 files | Cohesion: 91%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how sections, generateSlug, store work
- Modifying teacher-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | store, update, validateCourse, syncSections, show (+4) |
| `noblenest-academy/app/Models/TeacherCourse.php` | sections, generateSlug |

## Entry Points

Start here when exploring this area:

- **`sections`** (Method) — `noblenest-academy/app/Models/TeacherCourse.php:34`
- **`generateSlug`** (Method) — `noblenest-academy/app/Models/TeacherCourse.php:82`
- **`store`** (Method) — `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php:36`
- **`update`** (Method) — `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php:85`
- **`validateCourse`** (Method) — `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php:149`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `sections` | Method | `noblenest-academy/app/Models/TeacherCourse.php` | 34 |
| `generateSlug` | Method | `noblenest-academy/app/Models/TeacherCourse.php` | 82 |
| `store` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 36 |
| `update` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 85 |
| `validateCourse` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 149 |
| `syncSections` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 168 |
| `show` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 66 |
| `edit` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 78 |
| `destroy` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 116 |
| `togglePublish` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 128 |
| `authoriseCourse` | Method | `noblenest-academy/app/Http/Controllers/Teacher/CourseController.php` | 142 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Store → Sections` | intra_community | 3 |
| `Update → Sections` | intra_community | 3 |

## How to Explore

1. `gitnexus_context({name: "sections"})` — see callers and callees
2. `gitnexus_query({query: "teacher"})` — find related execution flows
3. Read key files listed above for implementation details
