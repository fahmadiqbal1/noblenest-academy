---
name: feature
description: "Skill for the Feature area of Noble Nest Academy. 111 symbols across 35 files."
---

# Feature

111 symbols | 35 files | Cohesion: 95%

## When to Use

- Working with code in `noblenest-academy/`
- Understanding how generate_activity_endpoint, build_index, get_existing_activity_summaries work
- Modifying feature-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `noblenest-academy/tests/Feature/MaternalWellnessTest.php` | maternal_routes_return_404_when_feature_disabled, parent_can_view_onboarding_form, dashboard_redirects_to_onboarding_without_profile, admin_can_view_content_management, non_admin_cannot_access_admin_content (+10) |
| `noblenest-academy/tests/Feature/TeacherStudentMarketplaceTest.php` | teacher_dashboard_requires_teacher_role, teacher_dashboard_loads, teacher_cannot_edit_another_teachers_course, marketplace_is_publicly_accessible, marketplace_shows_only_published_courses (+9) |
| `noblenest-academy/tests/Feature/ParentChildFlowTest.php` | parent_can_view_children_list, parent_can_view_create_child_form, non_parent_cannot_access_children_routes, guest_cannot_access_children_routes, parent_dashboard_loads (+5) |
| `noblenest-academy/tests/Feature/LmsDiscrepanciesTest.php` | home_page_loads_for_guest, onboarding_page_requires_auth, onboarding_page_loads_for_authenticated_user, admin_analytics_requires_admin_role, admin_analytics_loads_for_admin (+2) |
| `noblenest-academy/tests/Feature/AdminActivityCrudTest.php` | admin_can_list_activities, admin_can_search_activities, admin_can_filter_activities_by_subject, admin_can_view_create_form, non_admin_cannot_access_activity_crud (+1) |
| `noblenest-academy/tests/Feature/GenerateActivityMediaJobTest.php` | GenerateActivityMediaJobTest, test_job_dispatches_to_media_generation_queue, test_thumbnail_generation_updates_activity, test_audio_generation_updates_activity, test_budget_guard_prevents_over_limit (+1) |
| `noblenest-academy/app/Jobs/GenerateActivityMediaJob.php` | GenerateActivityMediaJob, handle, generateThumbnail, resolveProviderForType, failed |
| `noblenest-academy/tests/Feature/PublicMetadataTest.php` | home_page_exposes_route_specific_metadata, marketplace_page_exposes_route_specific_metadata, auth_pages_expose_route_specific_metadata, PublicMetadataTest |
| `noblenest-academy/tests/Feature/AdminCurriculumTest.php` | admin_can_view_curriculum_explorer, curriculum_explorer_requires_admin, admin_can_list_courses, AdminCurriculumTest |
| `noblenest-academy/tests/Feature/AIProviderGatewayTest.php` | AIProviderGatewayTest, anthropic_provider_can_be_verified, gemini_provider_can_generate_chat_content, github_driver_is_marked_as_configured_without_api_calls |

## Entry Points

Start here when exploring this area:

- **`generate_activity_endpoint`** (Function) — `noblenest-academy/services/curriculum-ai/main.py:80`
- **`build_index`** (Function) — `noblenest-academy/services/curriculum-ai/scripts/index_curriculum.py:30`
- **`get_existing_activity_summaries`** (Function) — `noblenest-academy/services/curriculum-ai/retrieval/pageindex_retriever.py:22`
- **`generate_batch`** (Function) — `noblenest-academy/services/curriculum-ai/chains/activity_chain.py:79`
- **`TestCase`** (Class) — `noblenest-academy/tests/TestCase.php:6`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `TestCase` | Class | `noblenest-academy/tests/TestCase.php` | 6 |
| `ExampleTest` | Class | `noblenest-academy/tests/Unit/ExampleTest.php` | 6 |
| `UserManagementTest` | Class | `noblenest-academy/tests/Feature/UserManagementTest.php` | 11 |
| `TeacherStudentMarketplaceTest` | Class | `noblenest-academy/tests/Feature/TeacherStudentMarketplaceTest.php` | 14 |
| `PublicMetadataTest` | Class | `noblenest-academy/tests/Feature/PublicMetadataTest.php` | 9 |
| `PasswordResetTest` | Class | `noblenest-academy/tests/Feature/PasswordResetTest.php` | 12 |
| `ParentChildFlowTest` | Class | `noblenest-academy/tests/Feature/ParentChildFlowTest.php` | 12 |
| `MaternalWellnessTest` | Class | `noblenest-academy/tests/Feature/MaternalWellnessTest.php` | 15 |
| `LmsDiscrepanciesTest` | Class | `noblenest-academy/tests/Feature/LmsDiscrepanciesTest.php` | 16 |
| `GenerateActivityMediaJobTest` | Class | `noblenest-academy/tests/Feature/GenerateActivityMediaJobTest.php` | 14 |
| `ExampleTest` | Class | `noblenest-academy/tests/Feature/ExampleTest.php` | 7 |
| `CurriculumStructureTest` | Class | `noblenest-academy/tests/Feature/CurriculumStructureTest.php` | 12 |
| `ChildProfilePolicyTest` | Class | `noblenest-academy/tests/Feature/ChildProfilePolicyTest.php` | 9 |
| `AIProviderGatewayTest` | Class | `noblenest-academy/tests/Feature/AIProviderGatewayTest.php` | 12 |
| `AIAssistantTest` | Class | `noblenest-academy/tests/Feature/AIAssistantTest.php` | 13 |
| `AdminCurriculumTest` | Class | `noblenest-academy/tests/Feature/AdminCurriculumTest.php` | 12 |
| `AdminActivityCrudTest` | Class | `noblenest-academy/tests/Feature/AdminActivityCrudTest.php` | 13 |
| `ActivityManagementTest` | Class | `noblenest-academy/tests/Feature/ActivityManagementTest.php` | 12 |
| `ActivityAgeFilteringTest` | Class | `noblenest-academy/tests/Feature/ActivityAgeFilteringTest.php` | 15 |
| `RegistrationSecurityTest` | Class | `noblenest-academy/tests/Feature/Security/RegistrationSecurityTest.php` | 12 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `DispatchJob → LoadFromFile` | cross_community | 7 |
| `Handle → GetProvider` | cross_community | 7 |
| `RetryJob → LoadFromFile` | cross_community | 7 |
| `GenerateThumbnail → GetProvider` | cross_community | 7 |
| `GenerateVideo → LoadFromFile` | cross_community | 7 |
| `DispatchJob → CurrentLanguage` | cross_community | 6 |
| `Handle → FormatHttpError` | cross_community | 6 |
| `RetryJob → CurrentLanguage` | cross_community | 6 |
| `GenerateVideo → CurrentLanguage` | cross_community | 6 |
| `Generate_activity_endpoint → LoadFromFile` | cross_community | 6 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Services | 4 calls |
| Controllers | 2 calls |
| Admin | 1 calls |
| Security | 1 calls |

## How to Explore

1. `gitnexus_context({name: "generate_activity_endpoint"})` — see callers and callees
2. `gitnexus_query({query: "feature"})` — find related execution flows
3. Read key files listed above for implementation details
