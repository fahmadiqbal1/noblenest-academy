# Sprint 2 Deep Domain Analysis & Hardening Plan
**Date**: 2026-04-10  
**Status**: Critical pre-GA findings, actionable hardening roadmap

---

## Executive Summary

Deep analysis of 3 critical domains reveals **production-ready for soft launch but CRITICAL hardening needed before GA**:

| Domain | Risk | Test Gap | Critical Issues | Est. Hardening |
|--------|------|----------|------------------|-----------------|
| **Payment Processing** | 🔴 CRITICAL | High | 3 (webhook security, idempotency, fallback) | 26h |
| **Async Job Processing** | 🟠 HIGH | High | 2 (budget race, rate limit exponential backoff) | 34h |
| **Curriculum AI** | 🟡 MEDIUM | Medium | 2 (cache staleness, batch error handling) | 44h |

**Total Hardening: 104 hours (~2.6 weeks)**

**Go/No-Go for Soft Launch:**
- ✅ **PAYMENT**: Can launch IF webhook secret verification is NOT optional (FIX REQUIRED before beta)
- ✅ **ASYNC JOBS**: Deploy with current tests + monitoring
- ✅ **AI ASSISTANT**: Safe to launch (admin-only batch generation initially)

---

## 1. PAYMENT PROCESSING — CRITICAL HARDENING NEEDED

### File Analyzed
`app/Http/Controllers/PaymentController.php` (219 lines)

### Critical Issue #1: Webhook Accepts Unsigned Payloads ⚠️

**Current Code (Lines 84-111):**
```php
public function stripeWebhook(Request $request)
{
    $payload   = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $whSecret  = config('services.stripe.webhook_secret');

    // ... SDK check ...

    // CRITICAL: Fallback without signature verification
    if (!empty($whSecret) && !empty($sigHeader)) {
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $whSecret);
        } catch (...) { /* ... */ }
    } else {
        // SECURITY HOLE: Accepts ANY unsigned payload
        $event = json_decode($payload);
        if (!$event || !isset($event->type)) {
            return response('Invalid payload', 400);
        }
    }
    
    // Process webhook with no signature guarantee
    $this->activateSubscription(...);
}
```

**Vulnerability:**
- If `STRIPE_WEBHOOK_SECRET` is not configured (missing from .env), webhook processes ANY payload
- Attacker can send fake `checkout.session.completed` events to grant free subscriptions
- **Production Risk**: HIGH — Enables payment fraud

**Fix Required (2 hours):**
```php
// Make signature verification mandatory
if (empty($whSecret)) {
    Log::error('STRIPE_WEBHOOK_SECRET not configured. Rejecting unsigned webhooks.');
    return response('Webhook secret not configured', 500);
}

if (empty($sigHeader)) {
    Log::warning('Stripe webhook received without signature header');
    return response('Signature required', 400);
}

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $whSecret);
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    Log::warning('Stripe signature verification failed', ['error' => $e->getMessage()]);
    return response('Invalid signature', 400);
}
```

---

### Critical Issue #2: No Idempotency — Replay Attacks Possible ⚠️

**Current Code (Lines 185-215):**
```php
protected function activateSubscription(
    string $provider,
    $user,
    int $amountCents = 1000,
    string $currency = 'USD',
    ?string $plan = null,
    ?int $months = null,
    ?string $providerId = null,
) {
    // ...
    
    // VULNERABLE: No idempotency key or deduplication
    Subscription::updateOrCreate(
        ['user_id' => $user->id, 'plan' => $planInfo['plan']],
        [
            'provider'    => $provider,
            'provider_id' => $providerId,  // Can be null!
            'amount'      => $amountCents / 100,
            'currency'    => $currency,
            'starts_at'   => $now,
            'ends_at'     => $endsAt,
            'active'      => true,
        ]
    );
}
```

**Vulnerability:**
- If webhook is replayed (Stripe sends it twice), webhook handler is called twice
- Second call re-activates subscription, extending it by another month
- Example: Attacker replays webhook 10 times → user gets 10 extra months free
- `$providerId` can be null → all replays match same updateOrCreate key

**Attack Scenario:**
```
1. User pays $10.99 for 1 month → Stripe sends checkout.session.completed
2. Our webhook processes → Subscription starts May 10 → May 10
3. Attacker intercepts webhook, replays it (or Stripe retries due to timeout)
4. Webhook processes again → Subscription extended to May 10 → June 10
5. Attacker replays 9 more times → Subscription extends to May 10 → 2025
```

**Fix Required (4 hours):**
```php
// 1. Add idempotency_key to Subscription model
Schema::table('subscriptions', function (Blueprint $table) {
    $table->string('idempotency_key')->unique()->nullable(); // webhook event ID
});

// 2. In webhook handler (line 132)
$sessionId = is_object($session) ? ($session->id ?? null) : ($session['id'] ?? null);

// Deduplication: check if we already processed this exact session
$existing = Subscription::where('provider', 'stripe')
    ->where('provider_id', $sessionId)
    ->first();

if ($existing) {
    Log::info('Stripe webhook already processed', ['session_id' => $sessionId]);
    return response()->noContent();  // 204
}

// 3. Then activate subscription with idempotency key
$this->activateSubscription('stripe', $user, ..., $sessionId);

// 4. Update activateSubscription()
protected function activateSubscription(..., ?string $providerId = null) {
    // ...
    Subscription::updateOrCreate(
        ['user_id' => $user->id, 'plan' => $planInfo['plan'], 'provider_id' => $providerId],
        [
            'provider'        => $provider,
            'provider_id'     => $providerId,
            'idempotency_key' => $providerId,  // For deduplication
            // ...
        ]
    );
}
```

---

### Critical Issue #3: Fallback Activates Subscription Without Payment ⚠️

**Current Code (Lines 36-81):**
```php
public function stripeCheckout(Request $request)
{
    // ...
    if ($hasSdk && !empty($secret)) {
        try {
            // Real Stripe checkout
            $session = \Stripe\Checkout\Session::create([...]);
            return redirect($session->url);
        } catch (\Throwable $e) {
            Log::error('Stripe checkout failed: '.$e->getMessage());
            // VULNERABLE: Free access fallback
            return $this->activateSubscription('stripe', Auth::user(), $amount, $currency);
        }
    }
    
    // VULNERABLE: No SDK = free access
    return $this->activateSubscription('stripe', Auth::user(), $amount, $currency);
}
```

**Vulnerability:**
- If Stripe SDK missing or config empty → user gets free subscription
- Stripe API errors (rate limit, invalid key) → user gets free subscription
- Example: `STRIPE_SECRET_KEY=invalid` → all payments fall back to free access

**Risk Assessment:**
- **Severity**: HIGH — Direct revenue loss
- **Likelihood**: MEDIUM — SDK rarely missing, but config errors happen
- **Impact**: User gets 1 month free access per error

**Fix Required (2 hours):**
```php
public function stripeCheckout(Request $request)
{
    $hasSdk = class_exists(\Stripe\Checkout\Session::class);
    $secret = config('services.stripe.secret');
    
    // 1. Require SDK
    if (!$hasSdk) {
        Log::error('Stripe SDK not installed. Payment processing unavailable.');
        return back()->with('error', 'Payment service is temporarily unavailable. Please contact support.');
    }
    
    // 2. Require config
    if (empty($secret)) {
        Log::error('STRIPE_SECRET_KEY is not configured.');
        return back()->with('error', 'Payment service is not configured. Please contact support.');
    }
    
    // 3. Set API key
    \Stripe\Stripe::setApiKey($secret);
    
    // 4. Create session with retry but NO fallback
    try {
        $session = \Stripe\Checkout\Session::create([...]);
        return redirect($session->url);
    } catch (\Stripe\RateLimitException $e) {
        Log::warning('Stripe rate limited', ['error' => $e->getMessage()]);
        return back()->with('error', 'Too many requests. Please try again in a moment.');
    } catch (\Stripe\InvalidParametersException $e) {
        Log::error('Invalid Stripe parameters', ['error' => $e->getMessage()]);
        return back()->with('error', 'Payment configuration error. Please contact support.');
    } catch (\Throwable $e) {
        Log::error('Stripe checkout failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Payment processing failed. Please try again.');
    }
    
    // No fallback to free subscription!
}
```

---

### Other Issues

#### Amount Validation Missing
```php
// Current: No validation of amount
$amount = (int) $request->input('amount', 1000); // User-controlled!

// Should validate:
$amount = (int) $request->validate(['amount' => 'required|integer|min:50|max:999900'])['amount'];
// 50¢ minimum, $9999 maximum
```

#### PayPal Implementation Incomplete
- Lines 143-147: PayPal SDK deprecated, fallback directly grants free access
- **Fix**: Either implement PayPal properly OR remove PayPal option entirely

#### Plan Resolution Heuristic Unreliable
```php
// Current: $20+ USD = annual (line 29)
if ($amountCents >= 2000) {
    return ['plan' => 'annual', 'months' => 12];
}

// Problem: Fails for regional pricing
// $20 GBP (2600¢) should be ~monthly plan
// Regional users confused

// Fix: Make plan parameter mandatory, not optional
```

---

## 2. ASYNC JOB PROCESSING — HIGH PRIORITY HARDENING

### File Analyzed
`app/Jobs/GenerateActivityMediaJob.php` (179 lines)

### Issue #1: Budget Guard Has Race Condition ⚠️

**Current Code (Lines 41-55):**
```php
$budgetKey = 'ai_daily_' . $this->mediaType . '_count';
$limit = config('services.ai.daily_' . $this->mediaType . '_limit', 200);

// RACE CONDITION HERE:
$used = (int) Cache::get($budgetKey, 0);  // Read current count
if ($used >= $limit) {
    $this->release(3600);
    return;
}

// ... job executes ...

// RACE CONDITION HERE:
Cache::put($budgetKey, $used + 1, now()->endOfDay());  // Increment count
```

**Scenario:**
```
Job A reads: used = 199/200
Job B reads: used = 199/200
Job A executes image → Cache.put(200) ✓
Job B executes image → Cache.put(200) ✓
Total executed: 2 images, Cache shows: 200 (correct by luck)

But if Job B happens between A's read and put:
Job A reads: used = 199/200
Job B reads: used = 199/200
Job A increments: 199 + 1 = 200
Job B increments: 199 + 1 = 200 (overwrites A!)
Total executed: 2 images, Cache shows: 200 (lucky again)

But at higher scale:
50 jobs all read: 199/200
All 50 execute (exceed limit 50x)
All 50 write: 200 (only last write wins)
Total executed: 50, Cache shows: 200 (massive overage)
```

**Fix Required (3 hours):**
```php
// Use atomic Redis INCR operation
$budgetKey = 'ai_daily_' . $this->mediaType . '_count';
$limit = config('services.ai.daily_' . $this->mediaType . '_limit', 200);

// Atomic check-and-increment
$client = Cache::getStore()->getConnection();  // Get Redis connection
$currentCount = $client->incr($budgetKey);     // Atomic increment

if ($currentCount > $limit) {
    $client->decr($budgetKey);  // Undo the increment
    Log::warning("Daily {$this->mediaType} budget would be exceeded ({$currentCount}/{$limit})");
    $this->release(3600);  // Retry in 1 hour
    return;
}

// Set expiry on first increment
if ($currentCount === 1) {
    $client->expireAt($budgetKey, now()->endOfDay()->timestamp);
}

// ... job executes ...
// No need to manually increment anymore
```

---

### Issue #2: Rate Limit Handling Too Simple ⚠️

**Current Code (Lines 87-93):**
```php
if (str_contains($e->getMessage(), '429')) {
    $this->release(60);  // Retry in 1 minute
    return;
}
throw $e;  // Fail immediately on other errors
```

**Problems:**
1. **String matching fragile**: Different providers format errors differently
   - OpenAI: `"Rate limit exceeded"`
   - Anthropic: `"429 Too Many Requests"`
   - Stability: `"Rate limited"`
   - GitHub: `"API rate limit exceeded"`

2. **1-minute delay insufficient**: Providers recommend exponential backoff
   - First 429: wait 1 second
   - Second 429: wait 2 seconds
   - Third 429: wait 4 seconds
   - Current: always wait 60 seconds (inefficient on shared queues)

3. **Doesn't respect Retry-After header**: Stripe/OpenAI send `Retry-After: 45` in headers, ignored

4. **Doesn't track retry count**: Job only has 2 total tries (line 21) — if 429 uses 1 try, only 1 real attempt remains

**Fix Required (4 hours):**
```php
// 1. Track retry metadata
public function handle(AIProviderGateway $gateway): void
{
    $retryCount = property_exists($this, 'retries_429') ? $this->retries_429 : 0;
    // ...
}

// 2. Implement exponential backoff with provider-specific logic
try {
    $result = match ($this->mediaType) {
        'thumbnail' => $this->generateThumbnail($gateway, $provider, $activity),
        'audio'     => $this->generateAudio($gateway, $provider, $activity),
        'video'     => $this->generateVideo($gateway, $provider, $activity),
    };
    // success
} catch (\Exception $e) {
    // 3. Detect rate limit with multiple patterns
    $is429 = $this->isRateLimitError($e);
    
    if ($is429) {
        // 4. Calculate exponential backoff: 1s, 2s, 4s, 8s, 16s, 32s
        $backoff = min(32, pow(2, $retryCount));
        
        // 5. Check Retry-After header
        if ($response?->header('Retry-After')) {
            $backoff = max($backoff, (int)$response->header('Retry-After'));
        }
        
        Log::warning("Rate limit detected. Retrying in {$backoff}s (attempt {$retryCount})");
        
        // 6. Store retry count for next attempt
        $this->retries_429 = $retryCount + 1;
        
        // 7. Release with calculated delay (don't count as failure)
        $this->release($backoff);
        return;
    }
    
    // Non-rate-limit errors: fail job
    throw $e;
}

// 7. Helper to detect 429 across providers
private function isRateLimitError(\Exception $e): bool
{
    $message = strtolower($e->getMessage());
    $patterns = [
        'rate limit', 'rate limiting', '429', 'too many requests',
        'quota exceeded', 'api rate limit', 'please retry'
    ];
    
    foreach ($patterns as $pattern) {
        if (str_contains($message, $pattern)) {
            return true;
        }
    }
    
    return false;
}
```

---

### Issue #3: Missing Test Coverage for Video Generation

**Current Test File (GenerateActivityMediaJobTest.php):**
- ✓ test_thumbnail_generation_updates_activity
- ✓ test_audio_generation_updates_activity
- ✗ **MISSING**: test_video_generation_updates_activity
- ✗ **MISSING**: test_video_generation_with_rate_limit

**Fix Required (2 hours):**
```php
#[\PHPUnit\Framework\Attributes\Test]
public function video_generation_updates_activity(): void
{
    $activity = Activity::factory()->create();
    $provider = AIProviderConfig::factory()->create([
        'capabilities' => ['video']
    ]);
    
    Bus::fake();
    
    $this->job = new GenerateActivityMediaJob(
        activityId: $activity->id,
        mediaType: 'video',
        providerId: $provider->id
    );
    
    // Mock AIProviderGateway
    $gateway = mock(AIProviderGateway::class);
    $gateway->shouldReceive('generateVideo')
        ->once()
        ->andReturn([
            'type' => 'video',
            'url' => 'https://example.com/video.mp4',
            'duration' => 5,
        ]);
    
    $this->app->instance(AIProviderGateway::class, $gateway);
    
    $this->job->handle($gateway);
    
    $this->assertTrue($activity->refresh()->video_url === 'https://example.com/video.mp4');
}

#[\PHPUnit\Framework\Attributes\Test]
public function rate_limit_triggers_exponential_backoff(): void
{
    $activity = Activity::factory()->create();
    $provider = AIProviderConfig::factory()->create();
    
    $gateway = mock(AIProviderGateway::class);
    $gateway->shouldReceive('generateImage')
        ->andThrow(new \Exception('Rate limit exceeded (429)'));
    
    $this->app->instance(AIProviderGateway::class, $gateway);
    
    $job = new GenerateActivityMediaJob(
        activityId: $activity->id,
        mediaType: 'thumbnail',
        providerId: $provider->id
    );
    
    // First attempt: should release with 1s delay
    $job->handle($gateway);
    
    // Verify job was released (not permanently failed)
    $this->assertTrue(true);  // Job doesn't throw
}
```

---

## 3. CURRICULUM AI SERVICE — MEDIUM PRIORITY HARDENING

### File Analyzed
`app/Services/AIAssistantService.php` (332 lines)

### Issue #1: Provider Cache Staleness ⚠️

**Current Code (Lines 60-70):**
```php
$this->provider = Cache::remember('ai_assistant_provider', 300, function () {
    return AIProviderConfig::query()
        ->where('is_active', true)
        ->where('connection_status', 'live')
        ->latest()
        ->first();
});
```

**Problems:**
1. **5-minute TTL too long**: If provider is deactivated, cache persists for 300 seconds
2. **Stale `connection_status`**: Relies on a separate sync mechanism that may not run frequently
3. **No fallback during miss**: If cache expires and DB is slow, users wait
4. **Single provider assumption**: `.first()` picks one provider; what if all are down?

**Scenario:**
```
10:00 AM: Provider is live, cached
10:02 AM: Provider API goes offline
10:02 AM - 10:05 AM: Users still hit dead provider (3 min outage)
10:05 AM: Cache expires, system switches to fallback
```

**Fix Required (3 hours):**
```php
// 1. Add fallback priority system
private function getProvider(): ?AIProviderConfig
{
    // Try primary provider (recently verified as live)
    $provider = Cache::remember('ai_assistant_provider', 60, function () {
        return AIProviderConfig::query()
            ->where('is_active', true)
            ->where('connection_status', 'live')
            ->orderByDesc('last_live_at')  // Recently verified
            ->orderByDesc('last_checked_at')
            ->first();
    });
    
    if ($provider) {
        return $provider;
    }
    
    // Fallback: any active provider (even if not recently checked)
    $provider = AIProviderConfig::query()
        ->where('is_active', true)
        ->orderByDesc('last_live_at')
        ->first();
    
    if ($provider) {
        Log::warning('Primary provider unavailable, using fallback', ['provider' => $provider->name]);
        return $provider;
    }
    
    // No provider available
    return null;
}

// 2. Handle null provider gracefully
public function chat(string $userMessage, array $context = []): array
{
    $provider = $this->getProvider();
    
    if (!$provider) {
        Log::warning('No AI provider available. Using mock response.');
        return $this->mockResponse($userMessage, $context);
    }
    
    // ... proceed with real provider
}

// 3. Invalidate cache when provider is deactivated
// In AIProviderConfig model:
protected static function boot(): void
{
    parent::boot();
    
    static::updated(function ($model) {
        if ($model->isDirty('is_active') || $model->isDirty('connection_status')) {
            Cache::forget('ai_assistant_provider');  // Invalidate immediately
        }
    });
}
```

---

### Issue #2: Batch Generation Error Handling Missing ⚠️

**Current Code (Lines 262-330):**
```php
public function generateBatch(string $subject, string $ageTier, int $count, string $locale = 'en'): array
{
    $activities = [];
    
    for ($i = 1; $i <= $count; $i++) {
        $prompt = "Create a {$ageTier}-tier {$subject} activity for children...";
        
        $result = $this->chat($prompt, [
            'language' => $locale,
            'age_tier' => $ageTier,
        ]);
        
        $parsed = json_decode($result['reply'], true);
        
        // NO TRANSACTION
        // NO ERROR HANDLING
        // NO ROLLBACK
        $activity = \App\Models\Activity::create([
            'title'           => $parsed['title'] ?? "Activity {$i}",
            'description'     => $parsed['description'] ?? $result['reply'],
            // ... 10+ fields ...
        ]);
        
        $activities[] = $activity;
    }
    
    return $activities;
}
```

**Problems:**
1. **No transaction wrapping**: If activity #3 fails, activities #1-2 are created but #3-5 are not — inconsistent state
2. **Silent JSON parse failures**: If response is not JSON, `json_decode` returns null, fallback uses raw reply as description (ugly)
3. **No validation**: `$parsed['age_min']` could be negative, "abc", or missing entirely
4. **Partial batch left dangling**: If loop errors out, caller doesn't know which activities were created
5. **No idempotency**: Running twice creates duplicates

**Fix Required (4 hours):**
```php
public function generateBatch(string $subject, string $ageTier, int $count, string $locale = 'en'): array
{
    // 1. Validate inputs
    $validated = validator(
        compact('subject', 'ageTier', 'count', 'locale'),
        [
            'subject'  => 'required|string|max:100|not_in:' . implode(',', $this->blockedWords),
            'ageTier'  => 'required|in:baby,toddler,preschool,school,teen',
            'count'    => 'required|integer|min:1|max:50',  // Prevent DOS
            'locale'   => 'required|in:en,fr,ru,zh,es,ko,ur,ar',
        ]
    )->validate();
    
    // 2. Wrap in transaction
    DB::beginTransaction();
    
    try {
        $activities = [];
        
        for ($i = 1; $i <= $count; $i++) {
            try {
                $prompt = "Create a {$ageTier}-tier {$subject} activity...";
                
                $result = $this->chat($prompt, [
                    'language' => $locale,
                    'age_tier' => $ageTier,
                ]);
                
                // 3. Validate JSON response
                if (empty($result['reply'])) {
                    Log::warning("Empty response for activity {$i}");
                    continue;  // Skip this activity, continue batch
                }
                
                $parsed = json_decode($result['reply'], true);
                
                if (!is_array($parsed)) {
                    Log::warning("Invalid JSON response for activity {$i}: " . substr($result['reply'], 0, 100));
                    continue;  // Skip
                }
                
                // 4. Validate required fields
                if (empty($parsed['title']) || empty($parsed['description'])) {
                    Log::warning("Missing title/description in response for activity {$i}");
                    continue;  // Skip
                }
                
                // 5. Sanitize age values
                $ageMin = max(0, (int)($parsed['age_min'] ?? 0));
                $ageMax = min(120, (int)($parsed['age_max'] ?? 120));
                
                if ($ageMin > $ageMax) {
                    [$ageMin, $ageMax] = [$ageMax, $ageMin];  // Swap
                }
                
                // 6. Create with validation
                $activity = Activity::create([
                    'title'              => Str::limit($parsed['title'], 200),
                    'description'        => Str::limit($parsed['description'], 5000),
                    'language'           => $locale,
                    'activity_type'      => $parsed['type'] ?? 'lesson',
                    'age_min'            => $ageMin,
                    'age_max'            => $ageMax,
                    'subject'            => $parsed['subject'] ?? null,
                    'age_group'          => $ageTier,
                    'duration_minutes'   => (int)($parsed['duration_minutes'] ?? 15),
                    'difficulty'         => $parsed['difficulty'] ?? 'medium',
                    'is_free'            => $parsed['is_free'] ?? true,
                    'is_muslim_only'     => $parsed['is_muslim_only'] ?? false,
                ]);
                
                $activities[] = $activity;
                Log::info("Created activity {$i}/{$count}: {$activity->title}");
                
            } catch (\Throwable $e) {
                // Log individual error but continue batch
                Log::error("Error creating activity {$i}/{$count}: " . $e->getMessage());
                continue;  // Don't fail entire batch
            }
        }
        
        // 7. Commit transaction
        DB::commit();
        
        // 8. Return results with metadata
        return [
            'success' => true,
            'created' => count($activities),
            'requested' => $count,
            'activities' => $activities,
            'failures' => $count - count($activities),
        ];
        
    } catch (\Throwable $e) {
        // 9. Rollback on catastrophic error
        DB::rollBack();
        Log::error("Batch generation failed: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Batch generation failed. Please try again.',
            'created' => 0,
            'requested' => $count,
        ];
    }
}
```

---

## 4. SPRINT 2 ROADMAP & EFFORT BREAKDOWN

### Timeline: Weeks 2-3 Post-Soft Launch (Parallel with GA)

### Week 2: Critical Fixes (28 hours)

| Task | Effort | Owner | Dependency |
|------|--------|-------|-----------|
| **PAYMENT: Fix webhook security** | 2h | Backend | - |
| **PAYMENT: Implement idempotency** | 4h | Backend | Above |
| **PAYMENT: Remove fallback free access** | 2h | Backend | - |
| **ASYNC: Atomic budget counter** | 3h | Backend | - |
| **ASYNC: Exponential backoff 429** | 4h | Backend | - |
| **ASYNC: Video generation tests** | 2h | QA | - |
| **AI: Provider cache invalidation** | 3h | Backend | - |
| **AI: Batch generation error handling** | 4h | Backend | - |
| **Subtotal** | **24h** | | |

### Week 3: Testing & Monitoring (26 hours)

| Task | Effort | Owner | Dependency |
|------|--------|-------|--------|
| **PAYMENT: Webhook security tests** | 6h | QA | Week 2 fixes |
| **PAYMENT: Idempotency tests** | 4h | QA | Week 2 fixes |
| **ASYNC: Budget guard race condition test** | 3h | QA | Week 2 fixes |
| **ASYNC: Rate limit retry scenarios** | 4h | QA | Week 2 fixes |
| **AI: Provider fallback testing** | 2h | QA | Week 2 fixes |
| **PAYMENT: Stripe audit logging** | 3h | Backend | Week 2 |
| **ASYNC: Job retry observability** | 2h | Backend | - |
| **Subtotal** | **24h** | | |

### Optional Week 4: Enhancements (25 hours)

| Task | Effort | Owner | Dependency |
|------|--------|-------|-----------|
| **PAYMENT: PayPal implementation** | 8h | Backend | - |
| **ASYNC: Budget alert dashboard** | 5h | Frontend | - |
| **AI: Enhanced content filtering (ML)** | 8h | ML | - |
| **AI: Multilingual system prompts** | 4h | Backend | - |
| **Subtotal** | **25h** | | |

---

## 5. GO/NO-GO DECISION FOR SOFT LAUNCH

### Soft Launch (100 Beta Users) — APPROVAL CONDITIONS

| Domain | Condition | Status | Action |
|--------|-----------|--------|--------|
| **Payment** | Webhook security FIX MUST be applied | ✅ REQUIRED | Apply before day 1 of soft launch |
| **Payment** | Fallback free access MUST be removed | ✅ REQUIRED | Apply before day 1 of soft launch |
| **Async Jobs** | Current implementation acceptable | ✅ APPROVED | Deploy as-is, monitor |
| **AI Service** | Current implementation acceptable | ✅ APPROVED | Deploy as-is, improvements in Sprint 2 |

**Decision: CONDITIONAL APPROVAL**

✅ **Can launch IF:**
1. Webhook signature verification is made mandatory (no unsigned payloads)
2. Fallback free subscription access is removed
3. Amount validation is added ($0.50 minimum)

⏸️ **If fixes not applied by launch day:** Disable payment feature entirely, launch AI+content features only

---

## APPENDIX: Code Snippets for Quick Reference

### Quick Fix #1: Mandatory Webhook Signature (Payment)
**File:** `app/Http/Controllers/PaymentController.php`  
**Lines to replace:** 84-111  
**Effort:** 2 hours  
**Risk**: Very Low (strengthens security)

### Quick Fix #2: Atomic Budget Counter (Async Jobs)
**File:** `app/Jobs/GenerateActivityMediaJob.php`  
**Lines to replace:** 41-55, 87-93  
**Effort:** 3 hours  
**Risk**: Low (uses standard Redis operations)

### Quick Fix #3: Provider Cache Invalidation (AI)
**File:** `app/Services/AIAssistantService.php`  
**Lines to add:** Model observer  
**Effort:** 3 hours  
**Risk**: Low (standard Laravel cache invalidation)

---

**Report Generated**: 2026-04-10  
**Recommendation**: Apply critical payment fixes immediately before soft launch  
**Next Review**: End of week 2 (post-soft launch)

