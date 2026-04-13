# Refactoring Guide — AIProviderGateway & OrchestratorController

## Status
- **AIProviderGateway**: Partially refactored (ChatProviderService complete, stubs for Image/Media)
- **OrchestratorController**: Identified for extraction, not yet implemented

## AIProviderGateway Refactoring

### Created Services
1. **ChatProviderService** (`app/Services/Providers/ChatProviderService.php`)
   - Handles: Claude, ChatGPT, Gemini chat completions
   - Methods: `verify()`, `chat()`
   - Status: ✅ COMPLETE (copy from original lines 13-312)

2. **ImageGenerationService** (`app/Services/Providers/ImageGenerationService.php`)
   - Handles: Stability, OpenAI/DALL-E, Gemini image generation
   - Methods: `verify()`, `generate()`
   - Status: 🟡 STUB (needs implementation from lines 391-537)
   - Lines to extract: 394-537

3. **MediaGenerationService** (`app/Services/Providers/MediaGenerationService.php`)
   - Handles: ElevenLabs (audio), Replicate (video), RunwayML (video)
   - Methods: `verify()`, `generateAudio()`, `generateVideo()`
   - Status: 🟡 STUB (needs implementation)
   - Lines to extract: 539-701

### Refactored AIProviderGateway
After extraction, AIProviderGateway should be a router/facade:

```php
class AIProviderGateway
{
    public function __construct(
        protected ChatProviderService $chatService,
        protected ImageGenerationService $imageService,
        protected MediaGenerationService $mediaService,
    ) {}

    public function verify(AIProviderConfig $provider): array
    {
        // Route to appropriate service based on provider type
    }

    public function chat(...) { return $this->chatService->chat(...); }
    public function generateImage(...) { return $this->imageService->generate(...); }
    public function generateAudio(...) { return $this->mediaService->generateAudio(...); }
    public function generateVideo(...) { return $this->mediaService->generateVideo(...); }
}
```

### Integration
Update callers (AIAssistantService, OrchestratorController, etc.) to continue using AIProviderGateway.

---

## OrchestratorController Refactoring

### Current Responsibilities (471 lines, 22 members)
1. **Provider Management** (lines 76-136)
   - `storeProvider()`, `destroyProvider()`, `toggleProvider()`, `verifyProvider()`
   - Extract to: `ProviderConfigController`

2. **Job Dispatch** (lines 142-205)
   - `dispatchJob()`, `runJob()`, `approve()`, `reject()`, `retryJob()`, `destroyJob()`
   - Extract to: `JobOrchestratorService` (business logic) + `JobController` (routes)

3. **Curriculum Analysis** (lines 210-225)
   - `scanCurriculum()` — already uses CurriculumHealthService
   - Extract to: `CurriculumAnalysisController`

4. **Media Generation** (lines 231-?)
   - `generateMedia()` — dispatch media jobs
   - Extract to: `MediaController` or keep in JobController

### Target Architecture

```
ProviderConfigController.php
├── index() — show available providers
├── store() — add new provider
├── update() — edit provider
├── destroy() — delete provider
└── verify() — test provider connectivity

JobController.php (or AIJobController.php)
├── index() — list all jobs
├── show() — job details
├── store() — dispatch new job
├── approve() — approve job result
├── reject() — reject job
├── retry() — retry failed job
└── destroy() — delete job

CurriculumAnalysisController.php
└── scan() — analyze curriculum health

Services/JobOrchestratorService.php
├── dispatch() — create + execute job
├── execute() — run job (sync or async)
├── approve() — publish approved result
└── retry() — requeue failed job
```

### Extraction Steps
1. Create `ProviderConfigController` with provider CRUD
2. Create `JobOrchestratorService` with dispatch/execute logic
3. Create `JobController` with job endpoints
4. Create `CurriculumAnalysisController` (or `CurriculumScanController`)
5. Update routes in `routes/web.php`
6. Delete or deprecate OrchestratorController

---

## OrchestratorController → JobOrchestratorService

Key methods to extract to service:
- `syncProviderStatus()` — provider verification logic
- `runJob()` — job execution (sync/async dispatch)
- `publishJobResult()` — convert job result to activity
- `extractGitHubRepository()` — GitHub content extraction
- Helper methods for media handling

---

## Timeline & Effort

| Task | Effort | Priority |
|------|--------|----------|
| Complete ImageGenerationService | 2h | HIGH |
| Complete MediaGenerationService | 3h | HIGH |
| Refactor OrchestratorController → services | 4h | HIGH |
| Update routes | 1h | MEDIUM |
| Integration testing | 2h | HIGH |
| **Total** | **~12h** | **Must complete before GA** |

---

## Regression Testing Checklist

After refactoring:
- [ ] Provider verification still works for all 7 providers
- [ ] Job dispatch succeeds for all job types
- [ ] Job approval/rejection works
- [ ] Curriculum scan produces accurate results
- [ ] Media generation (image/audio/video) completes end-to-end
- [ ] Admin routes protected with `role:Admin` middleware
- [ ] No regressions in existing activity generation

---

## Related Issues

- Code-review-graph reports: AIProviderGateway (28 members), OrchestratorController (22 members, 20 dead symbols)
- Monolithic classes violate Single Responsibility Principle
- Difficult to test individual providers in isolation
- Hard to add new providers without modifying existing classes (Open/Closed Principle)

---

**See CLAUDE.md for architecture decisions and design patterns.**
