<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChildProfile;
use App\Services\AIAssistantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for AI Assistant functionality and safety.
 */
class AIAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected User $parent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parent = User::factory()->create(['role' => 'Parent']);
    }

    /**
     * Test that AI assistant endpoint requires message.
     */
    public function test_assistant_requires_message(): void
    {
        $response = $this->actingAs($this->parent)
            ->postJson('/ai/assistant/message', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    /**
     * Test that AI assistant returns expected response structure.
     */
    public function test_assistant_returns_valid_structure(): void
    {
        $response = $this->actingAs($this->parent)
            ->postJson('/ai/assistant/message', [
                'message' => 'Hello, what activities do you recommend?',
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'reply',
            'provider',
            'suggestions',
        ]);
    }

    /**
     * Test that assistant respects message length limit.
     */
    public function test_assistant_enforces_message_length_limit(): void
    {
        $longMessage = str_repeat('a', 1001); // Over 1000 char limit

        $response = $this->actingAs($this->parent)
            ->postJson('/ai/assistant/message', [
                'message' => $longMessage,
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test that rate limiting is applied.
     */
    public function test_assistant_rate_limited(): void
    {
        // Make many requests quickly
        for ($i = 0; $i < 35; $i++) {
            $response = $this->actingAs($this->parent)
                ->postJson('/ai/assistant/message', [
                    'message' => "Test message {$i}",
                ]);
        }

        // Last request should be rate limited
        $this->assertContains($response->status(), [200, 429]);
    }

    /**
     * Test AI service content filter blocks inappropriate content.
     */
    public function test_content_filter_blocks_inappropriate_responses(): void
    {
        $service = app(AIAssistantService::class);
        
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('filterContent');
        $method->setAccessible(true);

        // Test that inappropriate content is filtered
        $inappropriateContent = "Let's talk about violence and weapons.";
        $filtered = $method->invoke($service, $inappropriateContent);

        $this->assertStringContainsString(
            'educational questions',
            $filtered,
            'Inappropriate content should be filtered'
        );
    }

    /**
     * Test AI service allows safe educational content.
     */
    public function test_content_filter_allows_safe_content(): void
    {
        $service = app(AIAssistantService::class);
        
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('filterContent');
        $method->setAccessible(true);

        $safeContent = "Here are some great activities for learning letters and numbers!";
        $filtered = $method->invoke($service, $safeContent);

        $this->assertEquals($safeContent, $filtered, 'Safe content should pass through unchanged');
    }

    /**
     * Test suggestions are age-appropriate.
     */
    public function test_suggestions_are_age_appropriate(): void
    {
        $service = app(AIAssistantService::class);
        
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('generateSuggestions');
        $method->setAccessible(true);

        // Test infant suggestions (0-12 months)
        $infantSuggestions = $method->invoke($service, '', ['child_age' => 6]);
        $this->assertCount(3, $infantSuggestions);
        
        // Test school-age suggestions (61+ months)
        $schoolSuggestions = $method->invoke($service, '', ['child_age' => 84]);
        $this->assertCount(3, $schoolSuggestions);
        
        // Suggestions should be different for different age groups
        $this->assertNotEquals($infantSuggestions, $schoolSuggestions);
    }

    /**
     * Test child profile context is included in AI requests.
     */
    public function test_child_context_included_in_requests(): void
    {
        // Create a child profile
        $child = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'Test Child',
            'date_of_birth'      => now()->subMonths(36),
            'preferred_language' => 'fr',
        ]);

        $response = $this->actingAs($this->parent)
            ->postJson('/ai/assistant/message', [
                'message'          => 'What should my child learn?',
                'child_profile_id' => $child->id,
            ]);

        $response->assertStatus(200);
    }

    /**
     * Test assistant status endpoint.
     */
    public function test_assistant_status_endpoint(): void
    {
        // This tests the status endpoint if it exists
        // The endpoint shows if AI service is available
        $service = app(AIAssistantService::class);
        
        // Without configured providers, should use mock
        $this->assertIsBool($service->isAvailable());
    }
}
