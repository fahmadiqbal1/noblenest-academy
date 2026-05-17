<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ChildProfile;
use App\Models\User;
use App\Services\AIAssistantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AIAssistantSuggestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function suggest_for_child_returns_parsed_array_and_never_leaks_pii(): void
    {
        config(['services.groq.api_key' => 'test-key-abc']);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            ['title' => 'Counting Beans', 'why' => 'Builds subitizing.', 'activity_id' => null],
                            ['title' => 'Storytime Plus', 'why' => 'Builds language.',    'activity_id' => null],
                            ['title' => 'Color Sort',     'why' => 'Builds attention.',   'activity_id' => null],
                        ]),
                    ],
                ]],
            ], 200),
        ]);

        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::create([
            'parent_id' => $parent->id,
            'name' => 'Verysecret Childname',
            'nickname' => 'BabyBee',
            'date_of_birth' => now()->subYears(4),
            'preferred_language' => 'en',
            'parental_consent_at' => now(),
        ]);

        Cache::forget("ai-suggest-{$child->id}");

        $service = app(AIAssistantService::class);
        $out = $service->suggestForChild($child);

        $this->assertCount(3, $out);
        $this->assertSame('Counting Beans', $out[0]['title']);

        // Verify NO PII reached the request body.
        Http::assertSent(function ($request) {
            $body = (string) $request->body();
            $this->assertStringNotContainsString('Verysecret Childname', $body);
            $this->assertStringNotContainsString('BabyBee', $body);

            return true;
        });
    }

    #[Test]
    public function suggest_for_child_caches_on_second_call(): void
    {
        config(['services.groq.api_key' => 'test-key-abc']);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [['message' => ['content' => '[]']]],
            ], 200),
        ]);

        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::create([
            'parent_id' => $parent->id,
            'name' => 'Kid',
            'date_of_birth' => now()->subYears(3),
            'preferred_language' => 'en',
            'parental_consent_at' => now(),
        ]);

        Cache::forget("ai-suggest-{$child->id}");

        app(AIAssistantService::class)->suggestForChild($child);
        app(AIAssistantService::class)->suggestForChild($child);

        // Only one HTTP request should have been sent (second served from cache).
        Http::assertSentCount(1);
    }

    #[Test]
    public function suggest_for_child_returns_stub_when_no_api_key(): void
    {
        config(['services.groq.api_key' => '']);

        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::create([
            'parent_id' => $parent->id,
            'name' => 'Kid',
            'date_of_birth' => now()->subYears(3),
            'preferred_language' => 'en',
            'parental_consent_at' => now(),
        ]);

        Cache::forget("ai-suggest-{$child->id}");

        $out = app(AIAssistantService::class)->suggestForChild($child);
        $this->assertNotEmpty($out);
        $this->assertStringContainsString('unavailable', strtolower($out[0]['title']));
    }
}
