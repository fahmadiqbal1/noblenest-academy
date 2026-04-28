---
name: models
description: "Skill for the Models area of Noble Nest Academy. 71 symbols across 42 files."
---

# Models

71 symbols | 42 files | Cohesion: 88%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how MaternalJournal, Course, Activity work
- Modifying models-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/app/Models/TeacherCourse.php` | isPublished, enrollments, activeEnrollments, classSessions, hasCapacity |
| `noblenest-academy/app/Models/User.php` | subscriptions, hasActiveSubscription, maternalProfile, hasMaternalProfile, enrolledCourses |
| `noblenest-academy/app/Models/InviteLink.php` | isExpired, isExhausted, isValid, generateToken |
| `noblenest-academy/app/Models/PractitionerProfile.php` | isSuspended, isActive, canReview, formattedLicenseType |
| `noblenest-academy/app/Http/Controllers/Student/EnrollmentController.php` | checkout, joinViaInvite, enroll |
| `noblenest-academy/app/Models/SessionToken.php` | user, isExpired, generate |
| `noblenest-academy/app/Models/MaternalContent.php` | reviews, approvedReviews, sideNotes |
| `noblenest-academy/app/Models/ChildProfile.php` | appropriateActivities, milestones |
| `noblenest-academy/app/Http/Controllers/Parent/MilestoneController.php` | index, toggle |
| `noblenest-academy/app/Models/Subscription.php` | currentWeek, maxActivityOrder |

## Entry Points

Start here when exploring this area:

- **`MaternalJournal`** (Class) — `noblenest-academy/app/Models/MaternalJournal.php:7`
- **`Course`** (Class) — `noblenest-academy/app/Models/Course.php:7`
- **`Activity`** (Class) — `noblenest-academy/app/Models/Activity.php:7`
- **`test_appropriate_activities_for_child`** (Method) — `noblenest-academy/tests/Feature/ActivityAgeFilteringTest.php:138`
- **`nextTargets`** (Method) — `noblenest-academy/app/Services/MilestoneService.php:122`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `MaternalJournal` | Class | `noblenest-academy/app/Models/MaternalJournal.php` | 7 |
| `Course` | Class | `noblenest-academy/app/Models/Course.php` | 7 |
| `Activity` | Class | `noblenest-academy/app/Models/Activity.php` | 7 |
| `test_appropriate_activities_for_child` | Method | `noblenest-academy/tests/Feature/ActivityAgeFilteringTest.php` | 138 |
| `nextTargets` | Method | `noblenest-academy/app/Services/MilestoneService.php` | 122 |
| `appropriateActivities` | Method | `noblenest-academy/app/Models/ChildProfile.php` | 123 |
| `milestones` | Method | `noblenest-academy/app/Models/ChildProfile.php` | 154 |
| `index` | Method | `noblenest-academy/app/Http/Controllers/Parent/MilestoneController.php` | 11 |
| `toggle` | Method | `noblenest-academy/app/Http/Controllers/Parent/MilestoneController.php` | 31 |
| `show` | Method | `noblenest-academy/app/Http/Controllers/Child/DashboardController.php` | 16 |
| `isActive` | Method | `noblenest-academy/app/Models/TeacherEnrollment.php` | 32 |
| `isPublished` | Method | `noblenest-academy/app/Models/TeacherCourse.php` | 63 |
| `isExpired` | Method | `noblenest-academy/app/Models/InviteLink.php` | 27 |
| `isExhausted` | Method | `noblenest-academy/app/Models/InviteLink.php` | 32 |
| `isValid` | Method | `noblenest-academy/app/Models/InviteLink.php` | 37 |
| `checkout` | Method | `noblenest-academy/app/Http/Controllers/Student/EnrollmentController.php` | 17 |
| `joinViaInvite` | Method | `noblenest-academy/app/Http/Controllers/Student/EnrollmentController.php` | 92 |
| `subscriptions` | Method | `noblenest-academy/app/Models/User.php` | 142 |
| `hasActiveSubscription` | Method | `noblenest-academy/app/Models/User.php` | 150 |
| `currentWeek` | Method | `noblenest-academy/app/Models/Subscription.php` | 25 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Show → LoadFromFile` | cross_community | 4 |
| `Index → CurrentWeek` | intra_community | 3 |
| `Index → Milestones` | cross_community | 3 |
| `Show → CurrentLanguage` | cross_community | 3 |
| `Show → Milestones` | intra_community | 3 |
| `Enroll → ActiveEnrollments` | intra_community | 3 |
| `JoinViaInvite → IsExpired` | intra_community | 3 |
| `JoinViaInvite → IsExhausted` | intra_community | 3 |
| `Checkout → ActiveEnrollments` | cross_community | 3 |
| `Create → MaternalProfile` | intra_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Maternal | 2 calls |
| Feature | 1 calls |

## How to Explore

1. `gitnexus_context({name: "MaternalJournal"})` — see callers and callees
2. `gitnexus_query({query: "models"})` — find related execution flows
3. Read key files listed above for implementation details
