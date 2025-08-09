# Noble Nest Academy: GitHub Repo Fetcher

This project provides a minimal PHP CLI script to fetch (download) an entire GitHub repository and place it into a local directory while preserving the original structure.

By default, it fetches the public repository:

- Owner: `fahmadiqbal1`
- Repo: `noblenest-academy`
- Destination directory: `./noblenest-academy`

If you want to target a different branch, owner, repo or destination, you can use the script options described below.

## Requirements

- PHP 7.4+ (PHP 8.x recommended)
- PHP extensions:
  - `zip` (ZipArchive) for extracting the downloaded archive
  - `curl` (recommended). If not available, PHP streams will be used as a fallback
- Internet access

Note: If your PHP does not have the `zip` extension enabled, install/enable it (e.g., `sudo apt-get install php-zip` on Debian/Ubuntu, or enable via your PHP installation method).

## Usage

1) Optional: Set a GitHub token if you need higher rate limits or if the repository is private. Do NOT commit tokens into the repository.

macOS/Linux:

```bash
export GITHUB_TOKEN="<YOUR_GITHUB_PAT>"
```

Windows (PowerShell):

```powershell
$env:GITHUB_TOKEN="<YOUR_GITHUB_PAT>"
```

2) Run the fetch script:

```bash
php scripts/fetch_repo.php
```

This will:
- Detect the default branch of `fahmadiqbal1/noblenest-academy` via the GitHub API
- Download the zipball archive for that branch
- Extract it and place the contents into `./noblenest-academy` (overwriting that directory if it exists)

### Options

You can override defaults via command-line flags:

- `--owner=OWNER` (default: `fahmadiqbal1`)
- `--repo=REPO` (default: `noblenest-academy`)
- `--branch=BRANCH` (default: repo default branch)
- `--dest=DIR` (default: `noblenest-academy`)
- `--dry-run` (performs API checks without downloading/extracting)

Example:

```bash
php scripts/fetch_repo.php --owner=fahmadiqbal1 --repo=noblenest-academy --branch=main --dest=noblenest-academy
```

## Notes

- The destination directory will be deleted and re-created during the fetch to ensure a clean copy. Make sure you do not have uncommitted work in that directory.
- If you are fetching a private repository, a valid `GITHUB_TOKEN` with `repo` access is required.
- The script preserves the original directory structure from the GitHub repository archive, suitable for further Laravel/PHP/Bootstrap development.

## Troubleshooting

- "Required PHP extension 'zip' is not loaded": Install/enable the `zip` extension for PHP.
- HTTP 403 / rate limit errors: Provide `GITHUB_TOKEN` via environment variable.
- Connection/timeout errors: Re-run; ensure internet is available. You can also try again with a token.


---

# Noble Nest Academy LMS (Laravel + Bootstrap) — Product Blueprint and Build Plan

Executive summary
- Goal: Build an interactive, mobile‑friendly Learning Management System for parents and kids (0–10 years) with a heavy focus on early childhood development (0–6, 1200+ activities) and STEM for 7–10. Entire platform in Laravel PHP and Bootstrap, with multilingual UI/content (English, French, Russian, Mandarin, Spanish, Korean), AI‑generated curriculum and videos, and an AI onboarding assistant on first login. Incorporate parenting guidance (child psychology, etiquette, mannerism, chivalry, royal etiquette) and global pricing.
- Approach: Use Laravel + Bootstrap foundation, layer in content authoring and AI generation pipelines, localized content, assessments (IQ & personality), recommendation engine for career orientation, and accreditation mapping. Deploy using standard Laravel practices with Stripe/Paddle for billing.

Important constraint
- Must be built only in Laravel PHP and Bootstrap (front-end components use Bootstrap 5.x; optional Laravel Livewire/Alpine for interactivity without deviating from Bootstrap styles).

1) High-level architecture (Laravel-centric)
- Backend: Laravel 11+ (PHP 8.2/8.3), MySQL or PostgreSQL, Redis (cache/queue), Laravel Horizon (queue), Laravel Sanctum (API tokens when needed).
- Frontend: Blade + Bootstrap 5.x, optional Alpine.js for micro‑interactions; avoid heavy SPA frameworks to honor the Bootstrap constraint.
- Services:
  - Object storage (S3-compatible) for media/HLS streaming.
  - Email (SES/Mailgun), SMS (Twilio) for parent notifications.
  - Payment: Stripe (Laravel Cashier), optionally Paddle for regional tax/VAT.
  - AI providers: LLM API for text/plans/assessments; TTS for narration; video generation API (provider-agnostic) to synthesize lessons; image generation for illustrations.
- Observability: Laravel Telescope for dev, Sentry/Bugsnag for prod errors, Log to CloudWatch/Stackdriver.
- Compliance/Safety: COPPA/GDPR alignment, data minimization, parental consent flows, content moderation for AI outputs.

2) Core modules and features
- Parent Academy (0–6 years):
  - Parenting curriculum covering child psychology, etiquette, chivalry, mannerism, royal etiquette, and culturally aware practices.
  - Daily/weekly plans with activities; progress tracking; reminders; parent‑child co‑play tasks.
- Early Years Child Curriculum (0–6): 1200+ activities
  - Domains: Social‑Emotional, Cognitive, Language & Literacy, Fine Motor, Gross Motor, Creative Arts, Practical Life, Cultural Studies, Numeracy, Early Science.
  - Pedagogical inspirations: Japanese (Kumon, lesson study, Shichida‑style memory/flashcards), Chinese (stroke order, abacus/mental math, calligraphy), Scandinavian (play‑based, forest school, independence, outdoor learning).
  - Interactive tools: tracing (letters, numbers, shapes), patterning, matching, memory games, storytime, songs with TTS.
- Language Training Module (EN/FR/RU/ZH/ES/KO):
  - Phonics/letters, syllables, vocabulary, dialogues; stroke‑order tracing for Mandarin; handwriting practice for Latin/Cyrillic; pronunciation with TTS; recording optional for parent review.
- Creative Arts: drawing pad (canvas), color‑by‑number, guided doodles; save to portfolio.
- STEM for 7–10: robotics & coding foundations
  - Block‑based coding (Blockly) tasks mapped to computer science fundamentals.
  - Robotics simulators (virtual sensors/actuators), algorithmic thinking, puzzles.
- Assessments: IQ & Personality (age‑appropriate)
  - Non‑clinical, child‑friendly puzzle packs; personality via simplified Big‑Five/TIPI‑C style questionnaire; adaptive item selection.
  - Guidance report → recommends learning paths and potential career clusters (analytical, creative, social, technical).
- AI Onboarding Assistant (first login)
  - Conversational guide that introduces navigation, sets preferences (child age, languages, learning goals), and suggests a starting plan.
- Accreditation pathway
  - Map curriculum to EYFS, national early years frameworks, or equivalent. Support SCORM/xAPI export for external review. Maintain learning outcomes, evidence artifacts, and QA reviews.

3) Multilingual implementation (EN, FR, RU, ZH, ES, KO)
- Laravel localization: resources/lang/{locale}/ for UI strings; use trans() and @lang() in Blade.
- Content localization:
  - For each Course/Lesson/Activity, store translation blobs keyed by locale; use a translatable pattern or a JSON column per locale.
  - AI translation pipeline with human‑in‑the‑loop approvals for sensitive content.
- Locale negotiation: detect from profile or Accept‑Language; allow manual switch via navbar; persist in session/profile.
- RTL not required for listed languages but keep CSS ready if adding Arabic/Hebrew in future.

4) Data model (entities overview)
- User (parent, child, staff), Roles/Permissions (Spatie Laravel Permission recommended).
- ChildProfile (belongs to User parent). Age, preferences, special needs flags, onboarding status.
- Course → Module → Lesson → Activity hierarchy; Activity types: tracing, quiz, game, video, audio, worksheet.
- MediaAsset (video/audio/image/doc), with transcoding status and captions.
- Assessment, AssessmentItem, Attempt, Score; PersonalitySurvey, SurveyItem, Response, ProfileReport.
- Recommendation (links assessments to courses/careers), CareerCluster.
- Localization tables or JSON fields for translatable columns.
- Subscription, Plan, Price, Invoice, PaymentMethod (via Cashier), Coupon.
- Progress, Badge, Certificate, Enrollment.
- AIJob (content generation), ModerationReport, Review.

5) Routes and controllers (illustrative)
- GET / → HomeController@index (localized landing)
- Auth (Breeze/Jetstream or Fortify based) → first‑time onboarding redirect.
- GET /onboarding → OnboardingController@show; POST /onboarding → store preferences.
- Resource routes for courses, modules, lessons, activities.
- GET /ai/assistant → shows assistant; POST /ai/assistant/message → ChatController@message (server‑side calls AI API).
- GET /assessments/iq, /assessments/personality; POST to start/answer/finish endpoints.
- Billing: /subscribe, webhooks /stripe/webhook.

6) Curriculum blueprint (0–6): 1200+ activities
- Structure: 72 monthly units (0–71 months) × ~17 activities per month ≈ 1224 activities total.
- Domain rotation: each month includes 6–8 domains, balanced across the year.
- Activity generator templates (examples):
  - Tracing: letters (A–Z), numbers (0–20), shapes; different stroke complexities; multilingual letter formation.
  - Language: picture vocabulary, simple dialogues, phoneme recognition.
  - Cognitive: sorting, matching, pattern completion, memory card games.
  - Motor: finger painting, playdough sculpting, bead threading, hop paths.
  - Social‑emotional: gratitude circle, sharing games, emotion cards.
  - Cultural: Japanese bowing etiquette basics, Chinese calligraphy strokes, Scandinavian outdoor scavenger hunt.
- Safety/age gating: each activity tagged with min_age, max_age, supervision_required.
- Sample monthly set (Month 24):
  - Tracing numbers 1–5; Shape tracing: triangle, circle.
  - Vocabulary: family members in EN/FR/ZH; TTS playback.
  - Cognitive: ABAB pattern blocks.
  - Motor: sticker placement following path.
  - Social: taking turns board game.
  - Cultural: Japanese “itadakimasu/ gochisousama” mealtime manners.

7) STEM curriculum (7–10)
- Tracks: Algorithmic Puzzles, Robotics Simulation, Game Design Basics, Web Basics (HTML/CSS via Bootstrap), Junior Python (optional offline worksheets), Electronics foundations.
- Weekly challenges with progressive difficulty; final projects with sharable demos.

8) AI content generation and media pipeline
- Text generation: lesson plans, activity steps, translations; require moderation and staff approval before publish.
- Video generation: synthesize explainer videos (slides + narration + animated presenter), auto‑subtitle, store as HLS.
- TTS: multilingual voices for bedtime stories, instructions, dialogues.
- Image generation: illustrations, tracing sheets (vectors preferred; store SVG/PNG).
- Workflow:
  1) Staff author a seed brief → AIJob queued.
  2) Generation → Moderation → Editor review → Publish.
  3) Translation → Review → Publish per locale.
- Storage: original + renditions; captions per locale.

9) AI onboarding assistant (first‑time login)
- Data points: child/parent languages, child age(s), learning goals, time per day.
- Implementation:
  - On login, check user.onboarded_at; if null → show modal wizard/chat.
  - Backend ChatController proxies to AI API with system prompt enforcing safety/politeness and age‑appropriate tone.
  - Save a recommended weekly plan; enqueue content prefetch/generation jobs.

10) Pricing (global strategy)
- Plans (indicative USD, localized pricing/FX):
  - Free: limited preview, 1 child profile, few activities per domain/month.
  - Starter ($7–9/mo): full 0–6 curriculum access for 1 child, limited AI videos.
  - Family ($14–19/mo): up to 4 child profiles, all languages, STEM 7–10, AI videos included up to quota.
  - School/Center (custom): per‑seat pricing, admin dashboards, SSO, priority support.
- Annual discounts (15–25%), regional price tiers, introductory coupons, scholarships.
- Billing: Stripe + Cashier; dunning, proration, tax/VAT; invoices downloadable.

11) UX/UI (Bootstrap only, mobile‑first)
- Components: Navbar with locale switcher; cards for courses; progress bars; badges; responsive grids; modals for assistant; toasts.
- Accessibility: color contrast, keyboard nav, alt text, captions.
- Gamification: stars/coins, streaks, badge cabinet, certificates.

12) Security, privacy, and compliance
- Parental consent, age gating, minimal data on children.
- Data retention policy; account deletion; export data (GDPR).
- Content moderation queue for AI outputs; profanity/violence filters.

13) Accreditation pathway
- Maintain outcome mappings to EYFS/competency standards, link lessons/assessments to outcomes.
- Evidence collection: portfolios, attempt logs, teacher/parent notes.
- Export SCORM/xAPI packages for review bodies.

14) Implementation roadmap (phased)
- Phase 0: Bootstrap Laravel project skeleton, auth, locales, home, pricing page.
- Phase 1: Content model, CMS for courses/lessons/activities, media storage, basic tracing tool.
- Phase 2: AI assistant + generation pipeline (text first), moderation UI, translation workflow.
- Phase 3: Video/TTS generation + delivery; assessments engine; recommendations.
- Phase 4: Billing, subscriptions; STEM 7–10; gamification; accreditation exports.
- Phase 5: QA, security review, pilot launch, analytics, iteration.

15) Example Laravel scaffolding (commands and snippets)
- Commands
  - composer create-project laravel/laravel noble-nest
  - php artisan breeze:install blade
  - php artisan make:model Course -mcr
  - php artisan make:model Lesson -mcr
  - php artisan make:model Activity -mcr
  - php artisan make:model MediaAsset -m
  - php artisan make:model Assessment -mcr
  - php artisan make:model PersonalitySurvey -mcr
  - php artisan make:model Recommendation -m
  - php artisan make:controller OnboardingController
  - php artisan make:controller ChatController
  - php artisan make:middleware EnsureOnboarded
  - php artisan make:notification WeeklyPlanReady
  - php artisan make:test HomePageLoadsTest
- Routes (routes/web.php)
  - Route::get('/', [HomeController::class, 'index']);
  - Route::get('/onboarding', [OnboardingController::class, 'show'])->middleware('auth');
  - Route::post('/onboarding', [OnboardingController::class, 'store'])->middleware('auth');
  - Route::post('/ai/assistant/message', [ChatController::class, 'message'])->middleware('auth');
- Example Blade (resources/views/home.blade.php)
  - Bootstrap jumbotron introducing the academy, language switcher dropdown, CTA to onboarding for new users.

16) Assessment design detail (IQ & personality)
- IQ: pattern recognition, spatial rotation, analogies; time‑boxed; adaptive difficulty; avoid clinical claims; provide entertainment/educational insights only.
- Personality: kid‑safe statements (Likert 3–5 scales), map to Big‑Five traits; produce strengths profile and learning style suggestions.
- Reporting: printable PDF with recommendations and course links.

17) Content authoring and governance
- Roles: Author, Reviewer, Publisher.
- Versioning with drafts; change logs; rollback.
- Templates for activity prompts to drive consistent AI outputs.

18) DevOps and deployment
- Environments: local, staging, prod. DB migrations/seeders, queues, cache warmers.
- CI/CD: GitHub Actions running phpunit, pint, phpstan; deploy via Forge/Envoy.
- Media: process via queues; serve via CDN; signed URLs; HLS adaptive bitrate.

19) Open questions for the Product Owner
- Accreditation targets (which bodies/countries)?
- Preferred video AI provider(s) and TTS vendors?
- Any restrictions on data residency (EU/US/other)?
- Target launch regions/pricing anchors (USD/EUR/local FX)?
- Do we include a teacher/coach dashboard at MVP or post‑MVP?

20) Next steps
- Confirm open questions → finalize detailed backlog.
- Initialize Laravel repo (as per commands), wire locales, add basic home page and onboarding wizard.
- Start content schema and seed with generator templates; connect AI pipelines.

Notes
- All content (curriculum, videos, activities) is designed to be AI‑generated with human review before publish. The platform remains Laravel + Bootstrap throughout. This blueprint is implementation‑ready and can be used to create the actual Laravel application with the commands and structures above.



21) Course catalog (initial seed; translatable)
- Parent Academy (for parents of 0–6):
  - P1: Foundations of Early Childhood Psychology (12 modules)
  - P2: Etiquette Essentials for Toddlers and Preschoolers (10 modules)
  - P3: Mannerism & Chivalry for Kids (8 modules)
  - P4: Royal Etiquette for Children (8 modules)
  - P5: Cultural Parenting Styles: Japanese, Chinese, Scandinavian (9 modules)
  - P6: Safety, Hygiene & Building Independence (10 modules)
- Early Years Child Curriculum (0–6):
  - 72 monthly units (M00–M71). Each unit includes modules:
    - Language & Literacy (3–4 activities)
    - Numeracy (3)
    - Cognitive (3)
    - Fine Motor (2)
    - Gross Motor (2)
    - Social‑Emotional (2)
    - Cultural Focus (1–2)
  - Example M24 (2 years):
    - Language: Family words EN/FR/ZH; TTS story “At the Park”.
    - Numeracy: Count 1–5 with manipulatives; number tracing 1–5.
    - Cognitive: ABAB patterns; 4‑piece jigsaw; memory 6 cards.
    - Fine Motor: sticker path; crayon grip practice.
    - Gross Motor: hopscotch path; bean bag toss.
    - Social‑Emotional: taking turns game; feelings faces.
    - Cultural: Japanese mealtime phrases; Scandinavian outdoor scavenger.
- Language Training (per language: EN, FR, RU, ZH, ES, KO)
  - Pre‑A1 YLE: 12 lessons; A1 Starter: 12; A1.1: 12
  - Tracing pack: 52 sheets (letters/characters/strokes)
  - Phonics/Pronunciation pack with TTS and recording
- STEM (7–10)
  - Robotics Simulation L1 (10 lessons), L2 (10)
  - Coding Blocks L1 (10), Game Design Basics (8)
  - Web Basics with Bootstrap (8)
- Assessments
  - IQ: 5 subtests (patterns, matrices, spatial, analogies, sequences)
  - Personality: 30 items kid‑safe; report with learning style suggestions

22) Seeder scaffolding (example)
- Example InitialCatalogSeeder structure (PHP array pseudocode for Laravel seeder):

```php
$catalog = [
  [
    'slug' => 'parents-foundations',
    'title' => [
      'en' => 'Foundations of Early Childhood Psychology',
      'fr' => 'Fondements de la psychologie de la petite enfance',
      'ru' => 'Основы детской психологии',
      'zh' => '幼儿心理学基础',
      'es' => 'Fundamentos de la psicología infantil',
      'ko' => '영유아 심리학 기초',
    ],
    'modules' => [
      [
        'title' => ['en' => 'Attachment & Bonding'],
        'lessons' => [
          [
            'title' => ['en' => 'Secure Attachment Basics'],
            'activities' => [
              [
                'type' => 'video',
                'ai_prompt' => 'Create a 4‑minute explainer for parents on secure attachment with actionable tips.',
                'duration' => 240,
              ],
              [
                'type' => 'checklist',
                'ai_prompt' => 'Generate a daily bonding checklist for parents of 0–12 months.',
              ],
            ],
          ],
        ],
      ],
    ],
  ],
  [
    'slug' => 'early-years-M24',
    'title' => ['en' => 'Month 24 Unit'],
    'modules' => [
      [
        'title' => ['en' => 'Numeracy'],
        'lessons' => [[
          'title' => ['en' => 'Trace Numbers 1–5'],
          'activities' => [[
            'type' => 'tracing',
            'config' => ['numbers' => [1,2,3,4,5], 'strokes' => 'guided'],
            'ai_prompt' => 'Generate SVG tracing paths for numbers 1–5, include stroke arrows and dotted guides.',
          ]],
        ]],
      ],
    ],
  ],
];
```

- Seeder usage:
  - php artisan make:seeder InitialCatalogSeeder
  - Implement insertion of Course/Module/Lesson/Activity using the structure above and translatable fields.
  - php artisan db:seed --class=InitialCatalogSeeder

23) AI job scaffolding (example)
- Migration idea: ai_jobs table (id, type, status, payload_json, result_json, locale, user_id, moderation_status, created_at, updated_at)
- Example job dispatch from seeder or CMS:

```php
AIJob::create([
  'type' => 'video_lesson',
  'locale' => 'en',
  'payload_json' => json_encode([
    'topic' => 'Secure Attachment Basics',
    'duration_sec' => 240,
    'style' => 'warm, empathetic, practical',
    'audience' => 'parents',
  ]),
  'status' => 'queued',
]);
```

24) AI onboarding assistant — prompt template (system message excerpt)
- “You are Noble Nest Academy’s friendly guide. Safety first. Address parents and kids separately with age‑appropriate tone. Gather: child age(s), preferred languages (EN/FR/RU/ZH/ES/KO), daily time budget, interests. Output: recommended weekly plan (7 days) mapped to activities, and reminders for parents. Do not provide medical or diagnostic claims.”

25) Career clusters mapping (examples)
- Analytical: puzzle logic, math challenges → suggest STEM/coding tracks.
- Creative: storytelling, drawing, music → suggest arts, language projects.
- Social/Leader: cooperation games, presentations → suggest teamwork modules.
- Practical/Builder: maker tasks, robotics → suggest robotics/electronics.

26) Accreditation mapping
- Tag each lesson with learning_outcome_codes referencing frameworks (e.g., EYFS CL, PSED, PD, L, M, UW, EAD). Maintain mapping tables for export (CSV/JSON) and accreditation packs.

27) Mobile and performance tips (Bootstrap + Laravel)
- Use responsive images (srcset), lazy‑load media, collapse heavy carousels on mobile.
- Keep JS minimal; use Alpine for small interactions; defer scripts.
- Prefer Blade components for cards, modals, badges; centralize i18n strings.

28) Safety and moderation guardrails for AI content
- Blocklists for violence/adult themes; age checks; human review required before publish.
- Store moderation reports and reviewer approvals; enable rollback.



## Quick Start — Fetch Laravel app and install Noble Nest LMS scaffold

This repository ships a fetcher and an installer to inject a minimal, mobile‑friendly Laravel + Bootstrap LMS with an AI assistant and an admin portal.

1) Fetch the Laravel application repo (defaults already set):

```bash
php scripts/fetch_repo.php
```

- You can override: `--owner`, `--repo`, `--branch`, `--dest`. Destination defaults to `./noblenest-academy`.

2) Install the Noble Nest LMS scaffold into the fetched app:

```bash
php scripts/install_lms_scaffold.php --dest=noblenest-academy
```

This copies controllers, models, a migration, Blade views (Bootstrap 5), seeders, and appends routes in a safe, idempotent way.

3) Install dependencies, configure, migrate, seed (optional), and run:

```bash
cd noblenest-academy
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=BasicCourseSeeder   # optional sample courses
php artisan serve
```

4) Explore the LMS
- Home (Noble Nest): http://127.0.0.1:8000/noble
  - Mobile‑friendly Bootstrap UI, hero section, and an AI Assistant modal.
  - Click “AI Assistant” to open a chat; POST /ai/assistant/message returns a friendly mock reply.
- Admin portal (Courses CRUD): http://127.0.0.1:8000/admin/courses
  - Create/edit/delete simple courses; seeded samples available via BasicCourseSeeder.

Notes
- The assistant uses a mock by default. To integrate a real provider, set env vars in the fetched app (e.g., `AI_ASSIST_PROVIDER` and `AI_ASSIST_API_KEY`) and expand `App\Http\Controllers\ChatController`.
- The installer is idempotent: existing files are not overwritten, and route blocks are guarded by markers.
- All UI is built with Laravel Blade + Bootstrap 5, keeping the stack within the requested constraints.
